<?php
/**
 * @author samibahij
 *  (c) Sami Bahij <sami.bahij1@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace App\Service;

use App\Entity\Document;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class UpdateFileService extends AbstractController
{

    /** Ne divulge pas ce fichier, il y a mes clés secrète API */
    private string $apiKey = "";
    private string $searchEngineId = "";

    /**
     * Liste des abbréviations des codes. C'est ici que tu les rajoutes si t'en as d'autres.
     * key => value
     */
    private array $codes = [
        'CC' => 'code civil',
        'CPC' => 'code de procédure civile',
        'CCom' => 'code de commerce',
        'CPP' => 'code de procédure pénale',
        'CT' => 'code du travail',
        'CP' => 'code pénal',
        'CSI' => 'code de la sécurité intérieure',
    ];

    /** Injecte le client HTTP pour pouvoir effectuer des requêtes. */
    public function __construct(private HttpClientInterface $client,
                                private EntityManagerInterface $entityManager,
                                private ParameterBagInterface $params)
    {
    }


    /**
     * Mise à jour du fichier Markdown avec des liens vers les articles de loi.
     * Lit le contenu du fichier, cherche des références à des articles de loi,
     * effectue une recherche sur Google pour chaque article, et met à jour le fichier
     * avec des liens Markdown si un lien Legifrance est trouvé.
     *
     * /!\ Ne touche au pattern que si t'es sûr de ta regex !
     *
     * @return bool Retourne true si le fichier a été mis à jour avec succès.
     */
    public function updateFile(): bool
    {
        $currentLatestDocument = $this->entityManager->getRepository(Document::class)->findOneBy([
            'user' => $this->getUser(),
            'isLastest' => true,
        ]);

        $projectDir = $this->getParameter('upload_directory');

        $filePath = $projectDir . '/' . $currentLatestDocument->getFileName();

        $content = file_get_contents($filePath);

        if ($content === false) {
            error_log("Erreur lors de la lecture du fichier.");
            return false;
        }

        $lines = explode("\n", $content);
        $updatedLines = [];

        foreach ($lines as $line) {
            $lineUpdated = $line;

            foreach ($this->codes as $abbr => $full) {
                $pattern = "/\bArt(?:\s([LRD]))?\s?(\d+(?:-\d+)?(?:\.\d+)?)\s$abbr\b/";
                preg_match_all($pattern, $line, $matches, PREG_SET_ORDER);

                foreach ($matches as $match) {
                    $existingLinkPattern = "/\[" . preg_quote($match[0], '/') . "\]\(https?:\/\/[^\)]+\)/";
                    if (!preg_match($existingLinkPattern, $line)) {
                        // Si la référence n'est pas déjà un lien
                        $articleType = $match[1] ?? '';
                        $articleNumber = $match[2];
                        $formattedString = "Article " . $articleType . $articleNumber . " " . $full;
                        $searchResults = $this->searchGoogle($formattedString);

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

        $updatedContent = implode("\n", $updatedLines);
        $result = file_put_contents($filePath, $updatedContent);
        if ($result === false) {
            error_log("Erreur lors de l'écriture dans le fichier.");
            return false;
        }

        return true;
    }


    /**
     * Effectue une recherche sur Google en utilisant l'API Custom Search de Google.
     * Utilisée pour trouver des liens vers les articles de loi sur Legifrance.
     *
     * @param string $query La requête de recherche.
     * @return array Tableau des résultats de recherche; tableau vide si erreur.
     */
    private function searchGoogle(string $query): array
    {
        $url = 'https://www.googleapis.com/customsearch/v1';

        try {
            $response = $this->client->request('GET', $url, [
                'query' => [
                    'key' => $this->apiKey,
                    'cx' => $this->searchEngineId,
                    'q' => $query,
                ]
            ]);

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