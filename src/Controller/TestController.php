<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(Request $request)
    {
        $client = new Client();

        // Préparer les données à envoyer au script Python
        $dataToSend = [
            'legifrance_api_key' => '1559207d-b427-4364-a53a-e74f3fb605eb',
            'legifrance_api_secret' => '73b502c0-172e-448e-a5ae-d7b6333d2ff7',
            'code_name' => 'Code civil',      // Exemple de valeur
            'search' => '7',                  // Exemple de valeur
            'champ' => 'ARTICLE',             // Optionnel
            'formatter' => true               // Optionnel
        ];

        try {
            $response = $client->request('POST', 'http://localhost:5001/api/get_article', [
                'json' => $dataToSend
            ]);

            // Vérifier le code de statut HTTP
            $statusCode = $response->getStatusCode();
            if ($statusCode !== 200) {
                return new JsonResponse(['error' => 'Erreur côté serveur Python', 'status' => $statusCode], $statusCode);
            }

            $result = json_decode($response->getBody()->getContents(), true);

            // Vérifier si le JSON est valide
            if (json_last_error() !== JSON_ERROR_NONE) {
                return new JsonResponse(['error' => 'Erreur de décodage JSON'], 500);
            }

            // Utiliser $result selon tes besoins
            return new JsonResponse($result);

        } catch (RequestException $e) {
            // Gestion des erreurs de requête HTTP
            return new JsonResponse(['error' => $e->getMessage()], $e->getCode() ?: 500);
        }
    }
}