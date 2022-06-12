<?php

namespace App\Tests\Integration;

use App\Tests\Traits\DataFixturesTrait;
use Doctrine\ORM\EntityManager;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IntegrationTestCase extends WebTestCase
{
    use DataFixturesTrait;

    private static bool $fixturesLoaded = false;

    protected ?EntityManager $manager;

    private KernelBrowser $client;

    protected ?AbstractDatabaseTool $databaseTool;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client  = self::createClient();
        $this->manager = $this->getService('doctrine')->getManager();
        $this->loadDataFixtures();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->manager->clear();

        if ($this->manager->getConnection()->isTransactionActive()) {
            $this->manager->getConnection()->rollBack();
        }

        $this->manager = null;
    }

    private function loadDataFixtures(): void
    {
        $this->databaseTool = $this->client->getContainer()->get(DatabaseToolCollection::class)->get();

        if (!self::$fixturesLoaded) {
            $this->databaseTool->loadFixtures($this->getFixtures());

            $this->manager->clear();

            self::$fixturesLoaded = true;
        }
    }

    protected function getService($id): ?object
    {
        return $this->client->getContainer()->get($id);
    }

    protected function truncateEntities(array $entities): void
    {
        $connection = $this->manager->getConnection();
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=0');
        $databasePlatform = $connection->getDatabasePlatform();
        foreach ($entities as $entity) {
            $query = $databasePlatform->getTruncateTableSQL(
                $this->manager->getClassMetadata($entity)->getTableName()
            );
            $connection->executeStatement($query);
        }
        $connection->executeQuery('SET FOREIGN_KEY_CHECKS=1');
    }
}
