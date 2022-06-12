<?php

namespace App\Controller;

use App\Dictionary\ReceiptStatusDictionary;
use App\Entity\ReceiptItem;
use App\Form\ReceiptItemType;
use App\Service\ORM\QueryBuilderFilterService;
use App\Service\ReceiptItem\ReceiptItemManualService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ReceiptItemController
 *
 * @Route("/receipt-items", name="receiptItem.")
 */
final class ReceiptItemController extends Controller
{
    /**
     * @Route(name="index", methods={"GET"})
     *
     * @param Request $request
     * @param QueryBuilderFilterService $filterService
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Receipt Item")
     * @OA\Response(
     *     response=200,
     *     description="Return list of receipt items",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=ReceiptItem::class, groups={"receiptItem.list"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    public function index(Request $request, QueryBuilderFilterService $filterService): JsonResponse
    {
        return $this->respondWithPagination(
            $filterService->filter(ReceiptItem::class, $request->query->all()),
            [],
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['receiptItem.list']]
        );
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     *
     * @param ReceiptItem $receiptItem
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Receipt Item")
     * @OA\Response(
     *     response=200,
     *     description="Return a receipt item details.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=ReceiptItem::class, groups={"receiptItem.read"})
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    public function show(ReceiptItem $receiptItem): JsonResponse
    {
        return $this->respond(
            $receiptItem,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['receiptItem.read']]
        );
    }

    /**
     * @Route(name="store", methods={"POST"})
     *
     * @param Request $request
     * @param ReceiptItemManualService $itemManualService
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Receipt Item")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=ReceiptItemType::class)))
     * @OA\Response(
     *     response=201,
     *     description="Receipt item successfully created, returns the newly created receipt item.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=ReceiptItem::class, groups={"receiptItem.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    public function store(Request $request, ReceiptItemManualService $itemManualService): JsonResponse
    {
        $form = $this->createForm(ReceiptItemType::class, null, [
            'validation_groups' => ['receipt_item.create'],
        ])
                     ->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $receiptItem = $itemManualService->create($form->getData());

        return $this->respond($receiptItem, JsonResponse::HTTP_CREATED, [], [
            'groups' => 'receiptItem.read',
        ]);
    }

    /**
     * @Route("/{id}", name="update", methods={"PATCH"})
     *
     * @param Request $request
     * @param ReceiptItem $receiptItem
     * @param ReceiptItemManualService $itemManualService
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Receipt Item")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=ReceiptItemType::class)))
     * @OA\Response(
     *     response=200,
     *     description="Receipt item successfully updated, returns the updated receipt item.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=ReceiptItem::class, groups={"receiptItem.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    public function update(
        Request $request,
        ReceiptItem $receiptItem,
        ReceiptItemManualService $itemManualService
    ): JsonResponse {
        if (ReceiptStatusDictionary::DRAFT !== $receiptItem->getStatus()) {
            return $this->respondWithError(
                "You can only edit an item with DRAFT status!",
                [],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if (ReceiptStatusDictionary::DRAFT !== $receiptItem->getReceipt()->getStatus()) {
            return $this->respondWithError(
                "You can only edit an item that it's receipt has DRAFT status!",
                [],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $form = $this->createForm(ReceiptItemType::class, null, [
            'validation_groups' => ['receipt_item.update'],
        ])
                     ->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $receiptItem = $itemManualService->update($form->getData(), $receiptItem);

        return $this->respond($receiptItem, JsonResponse::HTTP_OK, [], [
            'groups' => 'receiptItem.read',
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     * @OA\Tag(name="Receipt Item")
     * @OA\Response(
     *     response=200,
     *     description="Receipt item successfully deleted"
     * )
     *
     * @param ReceiptItem $receiptItem
     *
     * @return JsonResponse
     */
    public function delete(ReceiptItem $receiptItem, EntityManagerInterface $entityManager): JsonResponse
    {
        if (ReceiptStatusDictionary::DRAFT !== $receiptItem->getStatus()) {
            return $this->respondWithError(
                "You can only delete an item with DRAFT status!",
                [],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if (ReceiptStatusDictionary::DRAFT !== $receiptItem->getReceipt()->getStatus()) {
            return $this->respondWithError(
                "You can only delete an item that it's receipt has DRAFT status!",
                [],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $receiptItemId = $receiptItem->getId();
        $entityManager->remove($receiptItem);
        $entityManager->flush();

        return $this->respondEntityRemoved($receiptItemId);
    }
}
