<?php

namespace App\Tests\Unit\Listeners\Authentication;

use App\Listeners\Authentication\DevelopmentAutoLoginListener;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class DevelopmentAutoLoginListenerTest
 */
final class DevelopmentAutoLoginListenerTest extends MockeryTestCase
{
    public function testItReturnIfAutoLoginIsNotEnable(): void
    {
        $listener = new DevelopmentAutoLoginListener(
            Mockery::mock(EntityManagerInterface::class),
            Mockery::mock(JWTTokenManagerInterface::class),
            false
        );

        $event = Mockery::mock(RequestEvent::class);
        $event->shouldNotReceive('isMainRequest');

        $listener->onRequest($event);
    }

    public function testItReturnOnMasterRequests(): void
    {
        $listener = new DevelopmentAutoLoginListener(
            Mockery::mock(EntityManagerInterface::class),
            Mockery::mock(JWTTokenManagerInterface::class),
            true
        );

        $event = Mockery::mock(RequestEvent::class);
        $event->shouldReceive('isMainRequest')->once()->andReturnFalse();

        $listener->onRequest($event);
    }

    public function testItReturnIfRequestHasAuthorizationHeader(): void
    {
        $listener = new DevelopmentAutoLoginListener(
            Mockery::mock(EntityManagerInterface::class),
            Mockery::mock(JWTTokenManagerInterface::class),
            true
        );

        $event = Mockery::mock(RequestEvent::class);
        $event->shouldReceive('isMainRequest')->once()->withNoArgs()->andReturnTrue();
        $event->shouldReceive('getRequest')->once()->withNoArgs()->andReturnUsing(function () {
            return new class {
                public $headers;
                public function __construct()
                {
                    $this->headers = new class {
                        public function has(string $header): bool
                        {
                            return true;
                        }
                    };
                }
            };
        });

        $listener->onRequest($event);
    }

    public function testItReturnItSetAuthorizationHeader(): void
    {
        $user = Mockery::mock(UserInterface::class);

        $em = Mockery::mock(EntityManagerInterface::class);
        $em->shouldReceive('getRepository->findOneBy')
           ->once()
           ->with([])
           ->andReturn($user);

        $JWTTokenManager = Mockery::mock(JWTTokenManagerInterface::class);
        $JWTTokenManager->shouldReceive('create')->once()->with($user)->andReturn('123456');

        $listener = new DevelopmentAutoLoginListener(
            $em,
            $JWTTokenManager,
            true
        );

        $event = Mockery::mock(RequestEvent::class);
        $event->shouldReceive('isMainRequest')->once()->withNoArgs()->andReturnTrue();
        $event->shouldReceive('getRequest')->once()->withNoArgs()->andReturnUsing(function () {
            return new class {
                public $headers;
                public function __construct()
                {
                    $this->headers = new class {
                        public function has(string $header): bool
                        {
                            return false;
                        }
                        public function set(string $header, string $value): void
                        {
                            return;
                        }
                    };
                }
            };
        });

        $listener->onRequest($event);
    }
}
