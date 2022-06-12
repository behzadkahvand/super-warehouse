<?php

namespace App\Tests\Unit\Listeners\Authentication;

use App\Entity\Admin;
use App\Listeners\Authentication\AuthenticationListener;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthenticationListenerTest extends MockeryTestCase
{
    /**
     * @var Mockery\LegacyMockInterface|Mockery\MockInterface|UserInterface
     */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = Mockery::mock(Admin::class);
    }

    public function testOnAuthenticationSuccessHandler()
    {
        $authenticationSubscriber = new AuthenticationListener(1000, 2000);

        $this->user->shouldReceive('getId')->once()->andReturn(1);
        $this->user->shouldReceive('getName')->once()->andReturn("John");

        $event = new AuthenticationSuccessEvent([
            'token' => 'token',
            'refresh_token' => 'refreshToken',
        ], $this->user, new Response());
        $authenticationSubscriber->onAuthenticationSuccessHandler($event);

        $data = $event->getData();

        self::assertEquals('token', $data['token']);
        self::assertEquals('refreshToken', $data['refreshToken']);
        self::assertEquals('Bearer', $data['tokenType']);
        self::assertEquals(1000, $data['expireDate']);
        self::assertEquals(2000, $data['refreshTokenTtl']);

        self::assertArrayHasKey('id', $data['account']);
        self::assertArrayHasKey('name', $data['account']);
    }
}
