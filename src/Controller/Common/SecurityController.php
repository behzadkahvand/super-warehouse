<?php

namespace App\Controller\Common;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class SecurityController extends AbstractController
{
    /**
     * @Route("/security/login", name="admin.security.login", methods={"POST"})
     *
     * @OA\Tag(name="Admin")
     *
     * @OA\Parameter(
     *     name="Body Parameters",
     *     in="query",
     *     @OA\Schema(
     *         type="object",
     *         @OA\Property(property="username", type="string"),
     *         @OA\Property(property="password", type="string"),
     *     )
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="Return token and account info",
     *     @OA\Schema(
     *     type="object",
     *      @OA\Property(property="token", type="string")
     * )
     * )
     */
    public function admin()
    {
    }
}
