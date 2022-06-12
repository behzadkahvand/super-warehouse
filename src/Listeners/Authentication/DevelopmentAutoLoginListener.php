<?php

namespace App\Listeners\Authentication;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Class DevelopmentAutoLoginListener
 */
final class DevelopmentAutoLoginListener
{
    private EntityManagerInterface $em;

    private JWTTokenManagerInterface $JWTTokenManager;

    private bool $developmentAutoLogin;

    public function __construct(
        EntityManagerInterface $em,
        JWTTokenManagerInterface $JWTTokenManager,
        bool $developmentAutoLogin = false
    ) {
        $this->em                   = $em;
        $this->JWTTokenManager      = $JWTTokenManager;
        $this->developmentAutoLogin = $developmentAutoLogin;
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$this->developmentAutoLogin || !$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if ($request->headers->has('Authorization')) {
            return;
        }

        if (null === $admin = $this->em->getRepository(Admin::class)->findOneBy([])) {
            return;
        }

        $token = $this->JWTTokenManager->create($admin);

        $request->headers->set('Authorization', "Bearer {$token}");
    }
}
