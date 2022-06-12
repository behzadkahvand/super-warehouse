<?php

namespace App\Tests\Functional\Controller;

use App\Dictionary\PullListStatusDictionary;
use App\Entity\PullListItem;
use App\Repository\PullListItemRepository;
use App\Tests\Functional\FunctionalTestCase;
use Doctrine\Persistence\ObjectRepository;

class PullListItemControllerTest extends FunctionalTestCase
{
    protected ObjectRepository|PullListItemRepository|null $pullListItemRepo;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pullListItemRepo = $this->manager->getRepository(PullListItem::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->pullListItemRepo = null;
    }

    public function testItCanDeletePullListItemSuccessfully(): void
    {
        $pullListItem = $this->pullListItemRepo->findOneBy([
            "status" => PullListStatusDictionary::DRAFT,
        ]);

        $pullListItemId = $pullListItem->getId();

        $this->loginAs($this->admin)->sendRequest(
            'DELETE',
            $this->route('admin.pullListItem.delete', [
                'id' => $pullListItemId,
            ])
        );

        self::assertResponseStatusCodeSame(200);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);
        self::assertTrue($response['succeed']);
        self::assertEquals('Entity has been removed successfully!', $response['message']);
        self::assertNotEmpty($response['results']);
        self::assertEquals([], $response['metas']);

        $result = $response['results'];
        self::assertArrayHasKey('id', $result);
        self::assertEquals($pullListItemId, $result['id']);
    }

    public function testDeletePullListFailedWhenStatusIsNotDraft(): void
    {
        $pullListItem = $this->pullListItemRepo->findOneBy([
            "status" => PullListStatusDictionary::DRAFT,
        ]);

        $pullListItem->setStatus(PullListStatusDictionary::STOWING);
        $this->manager->flush();

        $pullListItemId = $pullListItem->getId();

        $this->loginAs($this->admin)->sendRequest(
            'DELETE',
            $this->route('admin.pullListItem.delete', [
                'id' => $pullListItemId,
            ])
        );

        self::assertResponseStatusCodeSame(422);

        $response = $this->getControllerResponse();

        self::assertResponseEnvelope($response);

        self::assertFalse($response['succeed']);
        self::assertEquals(
            'You can only delete a pull-list item with DRAFT status!',
            $response['message']
        );
        self::assertEmpty($response['results']);
        self::assertEquals([], $response['metas']);
    }
}
