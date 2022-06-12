<?php

namespace App\Controller;

use App\Dictionary\PullListStatusDictionary;
use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\ReceiptItem;
use App\Form\PullList\AddPullListItemType;
use App\Form\PullList\AssignPullListLocatorType;
use App\Form\PullList\PullListType;
use App\Repository\PullListItemRepository;
use App\Service\ORM\QueryBuilderFilterService;
use App\Service\PullList\ConfirmedPullListByLocator\ConfirmedPullListByLocatorService;
use App\Service\PullList\ReceiptItemAddList\ReceiptItemAddListService;
use App\Service\PullList\ReceiptItemAddList\SearchPayload;
use App\Service\PullList\SentPullListToLocator\SentPullListToLocatorService;
use App\Service\PullListItem\AddPullListItemService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/pull-lists", name: "pullList.")]
class PullListController extends Controller
{
    public function __construct(private EntityManagerInterface $manager)
    {
    }


    /**
     *
     * @OA\Tag(name="PullList")
     * @OA\Response(
     *     response=200,
     *     description="Return list of pull lists",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=PullList::class, groups={"pullList.read"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     *
     */
    #[Route(name: "index", methods: ["GET"])]
    public function index(Request $request, QueryBuilderFilterService $filterService): JsonResponse
    {
        return $this->respondWithPagination(
            $filterService->filter(PullList::class, $request->query->all()),
            context: ['groups' => ['pullList.read']]
        );
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="PullList")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=PullListType::class)))
     * @OA\Response(
     *     response=200,
     *     description="Pull list is added successfully!",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=PullList::class, groups={"pullList.manual.store"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/manual", name: "store.manual", methods: ["POST"])]
    public function storeManual(Request $request): JsonResponse
    {
        $form = $this->createForm(
            PullListType::class,
            options: [
                'validation_groups' => ['pullList.manual.store',],
            ]
        )->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        /**
         * @var PullList $pullList
         */
        $pullList = $form->getData();

        $pullList->setStatus(PullListStatusDictionary::DRAFT);

        $this->manager->persist($pullList);
        $this->manager->flush();

        return $this->setMessage('Pull list is added successfully!')
                    ->respond($pullList, context: ['groups' => ['pullList.manual.store']]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="PullList")
     * @OA\Parameter(
     *     name="filter",
     *     in="query",
     *     description="Allow filtering response based on resource fields and relationships. example:
     *         filter[id]=10&filter[user.id]=10.
     *         valid keys: receiptItemId, receiptId, productId, InventoryId",
     *     @OA\Items(type="string")
     * )
     * @OA\Parameter(
     *     name="sort",
     *     in="query",
     *     description="Allow sorting results based on resource fields or relationships. example:
     *         sort[]=-id&sort[]=user.id. valid values: no sort available yet!",
     *
     *     @OA\Items(type="string")
     * )
     * @OA\Response(
     *     response=200,
     *     description="Pull List receipt items add list",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=ReceiptItem::class, groups={"pull-list.receipt-item.add-list"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/{id}/receipt-item-add-list", name: "receipt-item.add-list", requirements: ["id" => "\d+"], methods: ["GET"])]
    public function receiptItemAddList(
        PullList $pullList,
        Request $request,
        ReceiptItemAddListService $receiptItemAddList
    ): JsonResponse {
        $data = $request->query->all();

        $payload = new SearchPayload(
            $pullList->getWarehouse()->getId(),
            (array)($data['filter'] ?? []),
            (array)($data['sort'] ?? []),
        );

        $queryBuilder = $receiptItemAddList->get($payload);

        return $this->respondWithPagination(
            $queryBuilder,
            context: ['groups' => ['pull-list.receipt-item.add-list']]
        );
    }

    /**
     * @OA\Tag(name="PullList")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=PullListType::class)))
     * @OA\Response(
     *     response=200,
     *     description="Pull list is updated successfully!",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=PullList::class, groups={"pullList.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/{id}", name: "update", methods: ["PATCH"])]
    public function update(PullList $pullList, Request $request): JsonResponse
    {
        $isWarehouseUpdated = $request->get('warehouse') !== $pullList->getWarehouse()->getId();

        if (
            ($isWarehouseUpdated) &&
            ((PullListStatusDictionary::DRAFT !== $pullList->getStatus()) || (!$pullList->getItems()->isEmpty()))
        ) {
            return $this->respondInvalidParameters("You can only edit pull-list warehouse when pull-list status is DRAFT and it does not have any items!");
        }

        if (
            ($pullList->getPriority() !== $request->get('priority')) && (!in_array(
                $pullList->getStatus(),
                [PullListStatusDictionary::DRAFT, PullListStatusDictionary::SENT_TO_LOCATOR]
            ))
        ) {
            return $this->respondInvalidParameters("You can only edit pull-list priority when pull-list status is DRAFT or SENT_TO_LOCATOR!");
        }

        $form = $this->createForm(
            PullListType::class,
            $pullList,
            [
                'validation_groups' => ['pullList.update'],
            ]
        )->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        if ($isWarehouseUpdated) {
            $pullList->setLocator(null);
        }

        $this->manager->flush();

        return $this->setMessage('Pull list is updated successfully!')
                    ->respond($pullList, context: ['groups' => 'pullList.read']);
    }

    /**
     * @OA\Tag(name="PullList")
     * @OA\Response(
     *     response=200,
     *     description="Pull list successfully deleted"
     * )
     */
    #[Route("/{id}", name: "delete", methods: ["DELETE"])]
    public function delete(PullList $pullList,): JsonResponse
    {
        if (PullListStatusDictionary::DRAFT !== $pullList->getStatus()) {
            return $this->respondInvalidParameters("You can only delete a pull-list with DRAFT status!");
        }

        $pullListId = $pullList->getId();
        $this->manager->remove($pullList);
        $this->manager->flush();

        return $this->respondEntityRemoved($pullListId);
    }

    /**
     * @OA\Tag(name="PullList")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=AddPullListItemType::class)))
     * @OA\Response(
     *     response=200,
     *     description="Pull list items is added successfully!",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=PullList::class, groups={"pullList.items.add"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/{id}/items", name: "items.add", requirements: ["id" => "\d+"], methods: ["POST"])]
    public function addItems(PullList $pullList, Request $request, AddPullListItemService $addPullListItem): JsonResponse
    {
        $form = $this->createForm(AddPullListItemType::class)->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }
        $data = $form->getData();

        $data->setPullList($pullList);

        $addPullListItem->perform($data);

        return $this->setMessage('Pull list items is added successfully!')
                    ->respond($pullList, context: ['groups' => ['pullList.items.add']]);
    }

    /**
     * @OA\Tag(name="PullList")
     * @OA\Response(
     *     response=200,
     *     description="Return list of pull list items by pull list",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=PullListItem::class, groups={"pullList.items.index"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/{id}/items", name: "items.index", requirements: ["id" => "\d+"], methods: ["GET"])]
    public function items(PullList $pullList, PullListItemRepository $pullListItemRepository): JsonResponse
    {
        return $this->respond(
            $pullListItemRepository->getItemsByPullList($pullList),
            context: ['groups' => ['pullList.items.index']]
        );
    }

    /**
     * @OA\Tag(name="PullList")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=AssignPullListLocatorType::class)))
     * @OA\Response(
     *     response=200,
     *     description="Pull list is assigned to locator successfully!",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=PullList::class, groups={"pullList.locator.assign"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/{id}/locator", name: "locator.assign", requirements: ["id" => "\d+"], methods: ["PUT"])]
    public function assignLocator(PullList $pullList, Request $request): JsonResponse
    {
        $pullListStatus = $pullList->getStatus();

        if (!in_array($pullListStatus, [PullListStatusDictionary::DRAFT, PullListStatusDictionary::SENT_TO_LOCATOR])) {
            return $this->respondInvalidParameters("You can only assign locator when pull-list status is DRAFT or SENT_TO_LOCATOR!");
        }

        $form = $this->createForm(
            AssignPullListLocatorType::class,
            $pullList,
            [
                'validation_groups' => ['pullList.locator.assign',],
            ]
        )->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $this->manager->flush();

        return $this->setMessage('Pull list is assigned to locator successfully!')
                    ->respond($pullList, context: ['groups' => ['pullList.locator.assign']]);
    }

    /**
     * @OA\Tag(name="PullList")
     * @OA\Response(
     *     response=200,
     *     description="Pull list is sent to locator successfully!",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=PullList::class, groups={"pullList.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/{id}/sent-to-locator", name: "sent-to-locator", requirements: ["id" => "\d+"], methods: ["POST"])]
    public function sentToLocator(
        PullList $pullList,
        SentPullListToLocatorService $sentPullListToLocatorService
    ): JsonResponse {
        $sentPullListToLocatorService->perform($pullList);

        return $this->setMessage('Pull list is sent to locator successfully!')
                    ->respond($pullList, context: ['groups' => 'pullList.read']);
    }

    /**
     * @OA\Tag(name="PullList")
     * @OA\Response(
     *     response=200,
     *     description="Pull list is confirmed by locator successfully!",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=PullList::class, groups={"pullList.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/{id}/confirmed-by-locator", name: "confirmed-by-locator", requirements: ["id" => "\d+"], methods: ["POST"])]
    public function confirmedByLocator(
        int $id,
        ConfirmedPullListByLocatorService $confirmedPullListByLocatorService
    ): JsonResponse {
        $pullList = $confirmedPullListByLocatorService->perform($id, $this->getUser());

        return $this->setMessage('Pull list is confirmed by locator successfully!')
                    ->respond($pullList, context: ['groups' => 'pullList.read']);
    }
}
