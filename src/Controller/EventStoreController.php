<?php

namespace App\Controller;

use App\Service\MongoFilter\PipelineMongoQueryBuilder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Document\Integration\Timcheh\EventStore;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

#[Route("/event-store", name: "event_store.")]
class EventStoreController extends Controller
{
    /**
     * @OA\Tag(name="Event Store")
     * @OA\Parameter(
     *     name="Body Parameters",
     *     in="query",
     *     @OA\Schema(
     *         type="object",
     *         @OA\Property(property="filter[event_store.messageId]", type="string"),
     *         @OA\Property(property="filter[event_store.messageName]", type="string"),
     *         @OA\Property(property="filter[event_store.sourceServiceName]", type="string"),
     *         @OA\Property(property="filter[event_store.sort]", type="string", enum={"asc", "desc"}),
     *         @OA\Property(property="filter[event_store.createdAt.min]", type="string"),
     *         @OA\Property(property="filter[event_store.createdAt.max]", type="string"),
     *         @OA\Property(property="filter[event_store.payload][{fieldName}]", type="string"),
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Return list of event stores.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=EventStore::class, groups={"event.store.read"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route(name: "index", methods: ["GET"])]
    public function index(
        Request $request,
        PipelineMongoQueryBuilder $queryBuilder
    ): JsonResponse {
        return $this->respondWithPagination(
            $queryBuilder->filter(EventStore::class, $request->query->all()),
            context: ['groups' => 'event.store.read']
        );
    }
}
