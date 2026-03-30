<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LogoutController extends AbstractController
{
    #[Route('/api/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        $response = new JsonResponse(null, Response::HTTP_NO_CONTENT);

        $response->headers->clearCookie(
            name: 'BEARER',
            path: '/',
            domain: null,
            secure: true,
            httpOnly: true,
            sameSite: 'lax',
        );

        return $response;
    }
}
