<?php

namespace App\Controller;

use App\Service\TestLegifranceApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class TestController extends AbstractController
{
    private $legifranceApiService;

    public function __construct(TestLegifranceApiService $legifranceApiService)
    {
        $this->legifranceApiService = $legifranceApiService;
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    #[Route('/test', name: 'app_test')]
    public function index(TestLegifranceApiService $legifranceApiService): JsonResponse
    {
        try {
            $data = $legifranceApiService->requestApiData('https://sandbox-api.aife.economie.gouv.fr/v1/your-endpoint');
            return new JsonResponse($data);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

}
