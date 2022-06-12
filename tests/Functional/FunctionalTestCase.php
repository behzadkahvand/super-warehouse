<?php

namespace App\Tests\Functional;

use App\Entity\Admin;
use App\Tests\Traits\DataFixturesTrait;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Liip\TestFixturesBundle\Services\DatabaseTools\MongoDBDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class FunctionalTestCase
 *
 * @method static bool assertNullableString(string $subject)
 * @method static bool assertNullableBool(string $subject)
 * @method static bool assertNullableBoolean(string $subject)
 * @method static bool assertNullableInt(string $subject)
 * @method static bool assertNullableInteger(string $subject)
 * @method static bool assertNullableLong(string $subject)
 * @method static bool assertNullableFloat(string $subject)
 * @method static bool assertNullableDouble(string $subject)
 * @method static bool assertNullableReal(string $subject)
 * @method static bool assertNullableNumeric(string $subject)
 * @method static bool assertNullableScalar(string $subject)
 * @method static bool assertNullableArray(string $subject)
 * @method static bool assertNullableIterable(string $subject)
 * @method static bool assertNullableCountable(string $subject)
 * @method static bool assertNullableCallable(string $subject)
 * @method static bool assertNullableObject(string $subject)
 * @method static bool assertNullableResource(string $subject)
 * @method static bool assertNullableNull(string $subject)
 * @method static bool assertNullableAlnum(string $subject)
 * @method static bool assertNullableAlpha(string $subject)
 * @method static bool assertNullableCntrl(string $subject)
 * @method static bool assertNullableDigit(string $subject)
 * @method static bool assertNullableGraph(string $subject)
 * @method static bool assertNullableLower(string $subject)
 * @method static bool assertNullablePrint(string $subject)
 * @method static bool assertNullablePunct(string $subject)
 * @method static bool assertNullableSpace(string $subject)
 * @method static bool assertNullableUpper(string $subject)
 * @method static bool assertNullableXdigit(string $subject)
 * @method static bool assertNullableDatetime(string $subject)
 */
class FunctionalTestCase extends WebTestCase
{
    use DataFixturesTrait;

    private ?Router $router;

    private ?KernelBrowser $client;

    private ?string $jwtToken;

    private bool $shouldLogin = false;

    protected ?EntityManagerInterface $manager;

    protected ?EventDispatcherInterface $dispatcher;

    private static bool $fixturesLoaded = false;

    protected ?Admin $admin;

    protected ?DatabaseToolCollection $databaseTool;

    protected function setUp(): void
    {
        $this->client       = self::createClient();
        $this->dispatcher   = $this->client->getContainer()->get('event_dispatcher');
        $this->manager      = $this->client->getContainer()->get('doctrine')->getManager();
        $this->router       = $this->client->getContainer()->get('router');
        $this->databaseTool = $this->client->getContainer()->get(DatabaseToolCollection::class);

        if (!self::$fixturesLoaded) {
            $this->databaseTool->get()->loadFixtures($this->getFixtures());
            $this->databaseTool->get(registryName: 'doctrine_mongodb')->loadFixtures($this->getDocumentFixtures());

            $this->manager->clear();

            self::$fixturesLoaded = true;
        }

        $this->admin = $this->manager->getRepository(Admin::class)->findOneBy([]);

        $this->manager->getConfiguration()->setSQLLogger();
        $this->manager->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->manager->clear();

        if ($this->manager->getConnection()->isTransactionActive()) {
            $this->manager->getConnection()->rollBack();
        }

        $this->manager    = null;
        $this->dispatcher = null;
        $this->client     = null;
        $this->router     = null;
        $this->jwtToken   = null;
        $this->admin      = null;

        parent::tearDown();
    }

    protected function sendRequest(
        $method,
        $uri,
        $content = null,
        array $parameters = [],
        array $headers = []
    ): KernelBrowser {
        $serverParams = [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT'  => 'application/json',
        ];

        if ($this->shouldLogin()) {
            $token        = $this->jwtToken ?? $this->getToken();
            $serverParams += ['HTTP_Authorization' => "Bearer $token"];
        }

        $this->client->request(
            $method,
            $uri,
            $parameters,
            [],
            array_merge($serverParams, $headers),
            json_encode($content)
        );

        return $this->client;
    }

    protected function route(string $name, array $parameters = []): string
    {
        return $this->router->generate($name, $parameters);
    }

    protected function loginAs(?UserInterface $user): self
    {
        if ($user) {
            $this->jwtToken    = $this->client->getContainer()->get(JWTTokenManagerInterface::class)->create($user);
            $this->shouldLogin = true;
        } else {
            $this->shouldLogin = false;
        }

        return $this;
    }

    protected function getControllerResponse(): array
    {
        return json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    public static function assertResponseEnvelope(array $response): void
    {
        self::assertArrayHasKeys(['succeed', 'message', 'results', 'metas'], $response);
    }

    public static function assertExceptionResponseEnvelope(array $response): void
    {
        self::assertArrayHasKeys(['title', 'status', 'detail'], $response);
    }

    protected static function assertArrayHasKeys(array $keys, array $subject): void
    {
        foreach ($keys as $key) {
            self::assertArrayHasKey($key, $subject);
        }
    }

    protected static function assertArrayHasKeysAndValues(array $keysAndValues, array $subject): void
    {
        foreach ($keysAndValues as $key => $value) {
            self::assertArrayHasKey($key, $subject);
            self::assertEquals($value, $subject[$key]);
        }
    }

    /**
     * @inheritDoc
     */
    public static function __callStatic($name, $arguments)
    {
        if (!str_starts_with($name, 'assertNullable')) {
            throw new \BadMethodCallException('Call to undefined method %s::%s()', static::class, $name);
        }

        $type = strtolower(substr($name, 14));

        $functionsMap = [
            'bool'      => 'is_bool',
            'boolean'   => 'is_bool',
            'int'       => 'is_int',
            'integer'   => 'is_int',
            'long'      => 'is_int',
            'float'     => 'is_float',
            'double'    => 'is_float',
            'real'      => 'is_float',
            'numeric'   => 'is_numeric',
            'string'    => 'is_string',
            'scalar'    => 'is_scalar',
            'array'     => 'is_array',
            'iterable'  => 'is_iterable',
            'countable' => 'is_countable',
            'callable'  => 'is_callable',
            'object'    => 'is_object',
            'resource'  => 'is_resource',
            'null'      => 'is_null',
            'alnum'     => 'ctype_alnum',
            'alpha'     => 'ctype_alpha',
            'cntrl'     => 'ctype_cntrl',
            'digit'     => 'ctype_digit',
            'graph'     => 'ctype_graph',
            'lower'     => 'ctype_lower',
            'print'     => 'ctype_print',
            'punct'     => 'ctype_punct',
            'space'     => 'ctype_space',
            'upper'     => 'ctype_upper',
            'xdigit'    => 'ctype_xdigit',
            'datetime'  => 'is_datetime',
        ];

        if (!isset($functionsMap[$type])) {
            throw new \InvalidArgumentException('Invalid type ' . $type);
        }

        $expr = array_key_exists(0, $arguments) && (null === $arguments[0] || $functionsMap[$type](...$arguments));

        self::assertTrue($expr);
    }


    private function getToken(): ?string
    {
        $user = $this->manager->getRepository(Admin::class)->findOneBy([]);

        return $this->jwtToken = $user ? $this->client->getContainer()->get(JWTTokenManagerInterface::class)->create($user) : null;
    }

    private function shouldLogin(): bool
    {
        return $this->shouldLogin;
    }

    protected function getService($id): object|null
    {
        return $this->client->getContainer()->get($id);
    }
}
