<?php

namespace App\Listeners\Authentication;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class AuthenticationListener implements EventSubscriberInterface
{
    private int $expireTime;

    private int $refreshTokenTtl;

    /**
     * AuthenticationSuccessListener constructor.
     *
     * @param int $expireTime
     * @param int $refreshTokenTtl
     */
    public function __construct(int $expireTime, int $refreshTokenTtl)
    {
        $this->expireTime = $expireTime;
        $this->refreshTokenTtl = $refreshTokenTtl;
    }

    public static function getSubscribedEvents()
    {
        return [
            'lexik_jwt_authentication.on_authentication_success' => 'onAuthenticationSuccessHandler',
        ];
    }

    /**
     * Listener
     *
     * @param AuthenticationSuccessEvent $event
     * @throws ExceptionInterface
     */
    public function onAuthenticationSuccessHandler(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $event->setData([
            'token'           => $data['token'],
            'refreshToken'    => $data['refresh_token'],
            'refreshTokenTtl' => $this->refreshTokenTtl,
            'tokenType'       => 'Bearer',
            'expireDate'      => $this->expireTime,
            'account'         => [
                'id'   => $user->getId(),
                'name' => $user->getName(),
            ],
        ]);
    }
}
