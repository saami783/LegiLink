<?php

namespace App\Controller\User\Api;

use App\Entity\User;
use App\Repository\ApiRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class PythonApiController extends AbstractController
{
    #[Route('/get-articles', name: 'app_python_api')]
    public function index(Request $request, ApiRepository $repository)
    {
        $client = new Client();

        /** @var User $user */
        $user = $this->getUser();
        $api = $repository->findOneBy(['user' => $user, 'isDefault' => true]);

        // Prépare les données à envoyer à l'api Python
        $dataToSend = [
            'legifrance_api_key' => $api->getApiKey(),
            'legifrance_api_secret' => $api->getApiSecret(),
            'code_name' => 'Code civil',      // Exemple de valeur
            'search' => '7',                  // Exemple de valeur
//            'champ' => 'ARTICLE',             // Optionnel
//            'formatter' => true               // Optionnel
        ];

        try {
            $response = $client->request('POST', 'http://localhost:5001/api/get_article', [
                'json' => $dataToSend
            ]);

            // Vérifie le code de statut HTTP
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                return new JsonResponse(['error' => 'Erreur côté serveur Python', 'status' => $statusCode], $statusCode);
            }

            $result = json_decode($response->getBody()->getContents(), true);

            // Vérifie si le JSON est valide
            if (json_last_error() !== JSON_ERROR_NONE) {
                return new JsonResponse(['error' => 'Erreur de décodage JSON'], 500);
            }

            // Utilise $result
            return new JsonResponse($result);

        } catch (RequestException $e) {
            // Gestion des erreurs de requête HTTP
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }

}
