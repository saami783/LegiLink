<?php

namespace App\Service;

use App\Entity\User;
use App\Enum\ExtensionFileEnum;
use App\Repository\DocumentRepository;
use App\Strategy\Interface\FileStrategyInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Gère la sélection et l'exécution des stratégies de mise à jour de fichiers.
 *
 * Cette classe utilise le pattern Strategy pour choisir et exécuter la stratégie
 * appropriée en fonction de l'extension du fichier de l'utilisateur.
 */
class FileStrategyManagerService
{

    /**
     * @var array<string, FileStrategyInterface> Tableau associatif des stratégies indexées par extension de fichier
     */
    private array $strategies = [];

    /**
     * @param DocumentRepository $documentRepository Utilisé pour récupérer les documents de l'utilisateur
     * @param TokenStorageInterface $tokenStorage Utilisé pour obtenir l'utilisateur actuellement authentifié
     */
    public function __construct(
        private readonly DocumentRepository $documentRepository,
        private readonly TokenStorageInterface $tokenStorage
    ) { }

    /**
     * Ajoute une nouvelle stratégie pour une extension de fichier spécifique.
     *
     * @param string $extension L'extension du fichier (ex: 'md', 'doc', 'docx')
     * @param FileStrategyInterface $strategy L'instance de la stratégie à utiliser pour cette extension
     */
    public function addStrategy(string $extension, FileStrategyInterface $strategy) : void {
        $this->strategies[$extension] = $strategy;
    }

    /**
     * Exécute la stratégie appropriée pour le fichier le plus récent de l'utilisateur actuel.
     *
     * @throws \LogicException Si aucun utilisateur n'est authentifié ou si l'utilisateur n'est pas du bon type
     * @throws \InvalidArgumentException Si aucune stratégie n'est trouvée pour l'extension du fichier
     */
    public function executeStrategy() : void {
        $user = $this->getUser();

        $document = $this->documentRepository->findOneBy(['user' => $user, 'isLastest' => true]);
        $extension = pathinfo($document->getFileName(), PATHINFO_EXTENSION);

        $strategy = $this->getStrategy($extension);
        $strategy->execute($user);
    }

    /**
     * Récupère la stratégie appropriée en fonction de l'extension du fichier.
     *
     * @param string $extension L'extension du fichier
     * @return FileStrategyInterface La stratégie correspondante
     * @throws \InvalidArgumentException Si aucune stratégie n'est trouvée pour l'extension donnée
     */
    private function getStrategy(string $extension): FileStrategyInterface
    {
        switch ($extension) {
            case ExtensionFileEnum::MD:
                return $this->strategies[ExtensionFileEnum::MD] ?? throw new \InvalidArgumentException('MD strategy not found');
            case ExtensionFileEnum::DOC:
                return $this->strategies[ExtensionFileEnum::DOC] ?? throw new \InvalidArgumentException('DOC strategy not found');
            case ExtensionFileEnum::DOCX:
                return $this->strategies[ExtensionFileEnum::DOCX] ?? throw new \InvalidArgumentException('DOCX strategy not found');
            default:
                throw new \InvalidArgumentException(sprintf('No strategy found for extension "%s"', $extension));
        }
    }

    /**
     * Récupère la stratégie appropriée en fonction de l'extension du fichier.
     *
     * @param string $extension L'extension du fichier
     * @return FileStrategyInterface La stratégie correspondante
     * @throws \InvalidArgumentException Si aucune stratégie n'est trouvée pour l'extension donnée
     */
    private function getUser(): User
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            throw new \LogicException('No authentication token found');
        }

        $user = $token->getUser();
        if (!$user instanceof User) {
            throw new \LogicException('User must be an instance of App\Entity\User');
        }

        return $user;
    }

}