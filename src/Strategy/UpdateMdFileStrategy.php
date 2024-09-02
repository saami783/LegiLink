<?php

namespace App\Strategy;

use App\Entity\Api;
use App\Entity\ApiExecution;
use App\Entity\Document;
use App\Entity\Setting;
use App\Entity\User;
use App\Strategy\Abstract\AbstractFileStrategy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Enum\CodeEnum;
use Vich\UploaderBundle\Storage\StorageInterface;

class UpdateMdFileStrategy extends AbstractFileStrategy
{

    private int $count = 0;

    /**
     * @throws \Exception
     */
    public function execute(User $user): void
    {
       $this->updateFile($user);
    }


    /** Injecte le client HTTP pour pouvoir effectuer des requêtes. */
    public function __construct(private readonly HttpClientInterface    $client,
                                private readonly EntityManagerInterface $entityManager,
                                private readonly StorageInterface $storage,
    )
    { }


    /**
     * Mise à jour du fichier Markdown avec des liens vers les articles de loi.
     * Lit le contenu du fichier, cherche des références à des articles de loi,
     * effectue une recherche sur Google pour chaque article, et met à jour le fichier
     * avec des liens Markdown si un lien Legifrance est trouvé.
     *
     * /!\ Ne touche au pattern que si t'es sûr de ta regex !
     *
     * @return void Retourne true si le fichier a été mis à jour avec succès.
     * @throws \Exception
     */
    private function updateFile(User $user): void
    {
        $apiExecution = new ApiExecution();
        $apiExecution->setExecutedAt(new \DateTimeImmutable());
        $apiExecution->setUser($user);
        $apiExecution->setExecution(1);

        $currentLatestDocument = $this->entityManager->getRepository(Document::class)->findOneBy([
            'user' => $user,
            'isLastest' => true,
        ]);

        $api = $this->entityManager->getRepository(Api::class)->findOneBy(['user' => $user, 'isDefault' => true]);
        $setting = $this->entityManager->getRepository(Setting::class)->findOneBy(['user' => $user]);

        $filePath = $this->storage->resolvePath($currentLatestDocument, 'file');

        if (!$filePath || !file_exists($filePath)) throw new \Exception('File not found ' . $filePath);

        $content = file_get_contents($filePath);

        if ($content === false) throw new \Exception('Error reading the file ' . $filePath);

        $lines = explode("\n", $content);
        $updatedLines = [];

        foreach ($lines as $line) {
            $lineUpdated = $line;

            foreach (CodeEnum::getCodes() as $abbr => $full) {
                $pattern = "/\bArt(?:\s([LRD]))?\s?(\d+(?:-\d+)?(?:\.\d+)?)\s$abbr\b/";
                preg_match_all($pattern, $line, $matches, PREG_SET_ORDER);

                foreach ($matches as $match) {
                    $existingLinkPattern = "/\[" . preg_quote($match[0], '/') . "\]\(https?:\/\/[^\)]+\)/";
                    if (!preg_match($existingLinkPattern, $line)) {
                        // Si la référence n'est pas déjà un lien
                        $articleType = $match[1] ?? '';
                        $articleNumber = $match[2];
                        $formattedString = "Article " . $articleType . $articleNumber . " " . $full;
                        $searchResults = $this->searchGoogle($formattedString, $api, $setting);

                        if (!empty($searchResults)) {
                            $legifranceLink = $this->findLegifranceLink($searchResults);
                            if ($legifranceLink !== null) {
                                $markdownLink = "[" . $match[0] . "](" . $legifranceLink . ")";
                                $lineUpdated = preg_replace('/' . preg_quote($match[0], '/') . '/', $markdownLink, $lineUpdated, 1);
                            }
                        }
                    }
                }
            }

            $updatedLines[] = $lineUpdated;
        }

        $setting->setTotalRequestSent($setting->getTotalRequestSent() + $this->count);

        $apiExecution->setRequest($setting->getTotalRequestSent());
        $this->entityManager->persist($apiExecution);
        $this->entityManager->flush();

        $this->entityManager->persist($setting);
        $this->entityManager->flush();

        $updatedContent = implode("\n", $updatedLines);
        $result = file_put_contents($filePath, $updatedContent);
        if ($result === false) {
            error_log("Erreur lors de l'écriture dans le fichier.");
            return;
        }

    }


    /**
     * Effectue une recherche sur Google en utilisant l'API Custom Search de Google.
     * Utilisée pour trouver des liens vers les articles de loi sur Legifrance.
     *
     * @param string $query La requête de recherche.
     * @return array Tableau des résultats de recherche; tableau vide si erreur.
     */
    private function searchGoogle(string $query, Api $api): array
    {
        $url = 'https://www.googleapis.com/customsearch/v1';

        try {
            $response = $this->client->request('GET', $url, [
                'query' => [
                    'key' => $api->getApiKey(),
                    'cx' => $api->getApiSecret(),
                    'q' => $query,
                ]
            ]);

            // Après une requête réussie, je mets à jour le nombre de requêtes envoyées
            $this->count += 1;

            return $response->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Extrait un lien vers Legifrance des résultats de recherche Google.
     * Parcourt les résultats et cherche un lien commençant par "https://www.legifrance.gouv.fr/".
     *
     * @param array $searchResults Résultats de la recherche Google.
     * @return string|null Le lien vers l'article sur Legifrance, ou null si non trouvé.
     */
    private function findLegifranceLink(array $searchResults): ?string
    {
        if (empty($searchResults['items'])) {
            return null;
        }

        foreach ($searchResults['items'] as $item) {
            if (isset($item['link']) && str_starts_with($item['link'], 'https://www.legifrance.gouv.fr/')) {
                return $item['link'];  // Retourne l'URL trouvée
            }
        }

        return null;  // Retourne null si aucun lien correspondant n'est trouvé
    }
}