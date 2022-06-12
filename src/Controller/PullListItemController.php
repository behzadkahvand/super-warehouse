<?php

namespace App\Controller;

use App\Dictionary\PullListStatusDictionary;
use App\Entity\PullListItem;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/pull-list-items", name: "pullListItem.")]
class PullListItemController extends Controller
{
    public function __construct(private EntityManagerInterface $manager)
    {
    }

    /**
     * @OA\Tag(name="PullListItem")
     * @OA\Response(
     *     response=200,
     *     description="Pull list item successfully deleted"
     * )
     */
    #[Route("/{id}", name: "delete", methods: ["DELETE"])]
    public function delete(PullListItem $pullListItem): JsonResponse
    {
        if (PullListStatusDictionary::DRAFT !== $pullListItem->getStatus()) {
            return $this->respondInvalidParameters("You can only delete a pull-list item with DRAFT status!");
        }

        $pullListItemId = $pullListItem->getId();
        $this->manager->remove($pullListItem);
        $this->manager->flush();

        return $this->respondEntityRemoved($pullListItemId);
    }
}
