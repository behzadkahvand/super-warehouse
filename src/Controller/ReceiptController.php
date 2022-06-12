<?php

namespace App\Controller;

use App\Dictionary\ReceiptStatusDictionary;
use App\DTO\GRMarketPlacePackageReceiptData;
use App\DTO\NoneReferenceReceiptData;
use App\DTO\STInboundReceiptFormData;
use App\Entity\Receipt;
use App\Entity\Receipt\GINoneReceipt;
use App\Entity\Receipt\GIShipmentReceipt;
use App\Entity\Receipt\GRMarketPlacePackageReceipt;
use App\Entity\Receipt\GRNoneReceipt;
use App\Entity\Receipt\STOutboundReceipt;
use App\Form\GRMarketPlacePackageReceiptType;
use App\Form\NoneReferenceReceiptType;
use App\Form\STInboundReceiptType;
use App\Service\PickList\GoodIssuePickListService;
use App\Service\Receipt\NoneReferenceReceiptService;
use App\Service\Receipt\ReceiptSearchService\ReceiptSearchService;
use App\Service\Receipt\GRMarketPlacePackageReceiptService;
use App\Service\Receipt\STInboundReceiptService;
use App\Service\StatusTransition\StateTransitionHandlerService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/receipts", name: "receipt.")]
final class ReceiptController extends Controller
{
    /**
     * @param Request $request
     * @param ReceiptSearchService $receiptSearchService
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Receipt")
     * @OA\Response(
     *     response=200,
     *     description="Return list of receipts",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=Receipt::class, groups={"receipt.list"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route(name: "index", methods: ["GET"])]
    public function index(
        Request $request,
        ReceiptSearchService $receiptSearchService
    ): JsonResponse {
        return $this->respondWithPagination(
            $receiptSearchService->perform($request->query->all()),
            context: ['groups' => ['receipt.list']]
        );
    }

    /**
     * @param Receipt $receipt
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Receipt")
     * @OA\Response(
     *     response=200,
     *     description="Return a receipt details.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=Receipt::class, groups={"receipt.read"})
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/{id}", name: "show", methods: ["GET"])]
    public function show(
        Receipt $receipt
    ): JsonResponse {
        return $this->respond($receipt, context: ['groups' => 'receipt.read']);
    }

    /**
     * @param Request $request
     * @param Receipt $receipt
     * @param NoneReferenceReceiptService $noneReferenceReceiptService
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Receipt")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=NoneReferenceReceiptData::class, groups={"receipt.manual.update"})))
     * @OA\Response(
     *     response=200,
     *     description="Receipt successfully updated, returns the updated receipt.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=Receipt::class, groups={"receipt.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/manual/{id}", name: "update.manual.receipt", methods: ["PATCH"])]
    public function updateManualReceipt(
        Receipt $receipt,
        Request $request,
        NoneReferenceReceiptService $noneReferenceReceiptService
    ): JsonResponse {
        if (!in_array(get_class($receipt), [GINoneReceipt::class, GRNoneReceipt::class, STOutboundReceipt::class])) {
            return $this->respondInvalidParameters("You can only edit none receipt!");
        }

        if (ReceiptStatusDictionary::DRAFT !== $receipt->getStatus()) {
            return $this->respondInvalidParameters("You can only edit receipt with DRAFT status!");
        }

        if (!$receipt->getReceiptItems()->isEmpty()) {
            return $this->respondInvalidParameters("it's not possible edit a receipt while it has items !");
        }

        $form = $this->createForm(NoneReferenceReceiptType::class, null, [
            'validation_groups' => [
                'receipt.manual.update',
                'receipt.' . strtolower($request->get('type')) . '.update',
            ],
        ])
                     ->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $noneReferenceReceiptService->updateReceipt($receipt, $form->getData());

        return $this->respond($receipt, context: ['groups' => 'receipt.read']);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $manager
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Receipt")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=GRMarketPlacePackageReceiptType::class)))
     * @OA\Response(
     *     response=201,
     *     description="Good receipt successfully created, returns the newly created good receipt.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=GRMarketPlacePackageReceipt::class, groups={"receipt.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/good-receipt", name: "store.good.receipt", methods: ["POST"])]
    public function storeGoodReceipt(
        Request $request,
        EntityManagerInterface $manager,
        GRMarketPlacePackageReceiptService $receiptService
    ): JsonResponse {
        $form = $this->createForm(GRMarketPlacePackageReceiptType::class)
                     ->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        /** @var GRMarketPlacePackageReceiptData $receiptData */
        $receiptData = $form->getData();

        $receipt = $receiptService->makeReceipt($receiptData);

        $manager->flush();

        return $this->respond($receipt, JsonResponse::HTTP_CREATED, context: ['groups' => 'receipt.read',]);
    }

    /**
     * @param Request $request
     * @param NoneReferenceReceiptService $noneReferenceReceiptService
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Receipt")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=NoneReferenceReceiptData::class, groups={"receipt.manual.store"})))
     * @OA\Response(
     *     response=201,
     *     description="Receipt successfully created, returns the newly created receipt.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=Receipt::class, groups={"receipt.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/manual", name: "store.manual.receipt", methods: ["POST"])]
    public function storeManualReceipt(
        Request $request,
        NoneReferenceReceiptService $noneReferenceReceiptService
    ): JsonResponse {
        $form = $this->createForm(NoneReferenceReceiptType::class, null, [
            'validation_groups' => [
                'receipt.manual.store',
                'receipt.' . strtolower($request->get('type')) . '.create',
            ],
        ])
                     ->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $receipt = $noneReferenceReceiptService->makeReceipt($form->getData());

        return $this->respond($receipt, JsonResponse::HTTP_CREATED, context: ['groups' => 'receipt.read',]);
    }

    /**
     * @OA\Tag(name="Receipt")
     * @OA\Response(
     *     response=200,
     *     description="Receipt item successfully deleted"
     * )
     *
     * @param Receipt $receipt
     * @param EntityManagerInterface $entityManager
     *
     * @return JsonResponse
     */
    #[Route("/{id}", name: "delete", methods: ["DELETE"])]
    public function delete(
        Receipt $receipt,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        if (ReceiptStatusDictionary::DRAFT !== $receipt->getStatus()) {
            return $this->respondInvalidParameters("You can only delete a receipt with DRAFT status!");
        }

        if (!$receipt->getReceiptItems()->isEmpty()) {
            return $this->respondInvalidParameters("it's not possible delete a receipt while it has items!");
        }

        $receiptItemId = $receipt->getId();
        $entityManager->remove($receipt);
        $entityManager->flush();

        return $this->respondEntityRemoved($receiptItemId);
    }

    /**
     * @OA\Tag(name="Receipt")
     * @OA\Response(
     *     response=200,
     *     description="Receipt status successfully approved, returns the updated receipt.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=Receipt::class, groups={"receipt.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/manual/{id}/approve", name: "approve.manual.receipt", methods: ["PATCH"])]
    public function approveManualReceiptStatus(
        Receipt $receipt,
        StateTransitionHandlerService $transitionHandlerService
    ): JsonResponse {
        if (!in_array(get_class($receipt), [GINoneReceipt::class, GRNoneReceipt::class, STOutboundReceipt::class])) {
            return $this->respondInvalidParameters("You can only edit none receipt!");
        }

        $transitionHandlerService->transitState($receipt, ReceiptStatusDictionary::APPROVED);

        return $this->respond($receipt, context: ['groups' => 'receipt.read']);
    }

    /**
     * @OA\Tag(name="Receipt")
     * @OA\Response(
     *     response=200,
     *     description="Receipt status updated and pick list created successfully",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=Receipt::class, groups={"receipt.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     *
     * @param Receipt $receipt
     * @param GoodIssuePickListService $pickListService
     *
     * @return JsonResponse
     */
    #[Route("/{id}/ready-to-pick", name: "update.status.ready.to.pick", methods: ["POST"])]
    public function readyToPick(Receipt $receipt, GoodIssuePickListService $pickListService): JsonResponse
    {
        if (ReceiptStatusDictionary::APPROVED !== $receipt->getStatus()) {
            return $this->respondInvalidParameters("You can only update a receipt with APPROVED status!");
        }

        if (
            !in_array(
                get_class($receipt),
                [GINoneReceipt::class, STOutboundReceipt::class, GIShipmentReceipt::class]
            )
        ) {
            return $this->respondInvalidParameters("You can only edit good issue receipt!");
        }

        $pickListService->create($receipt);

        return $this->respond($receipt, context: ['groups' => 'receipt.read']);
    }

    /**
     * @OA\Tag(name="Receipt")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=STInboundReceiptFormData::class, groups={"store"})))
     * @OA\Response(
     *     response=201,
     *     description="Receipt successfully created, returns the newly created receipt.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=Receipt::class, groups={"receipt.read","store.st-inbound"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/st-inbound", name: "store.st-inbound.receipt", methods: ["POST"])]
    public function storeSTInboundReceipt(
        Request $request,
        STInboundReceiptService $inboundReceiptService
    ): JsonResponse {
        $form = $this->createForm(STInboundReceiptType::class, null, [
            'validation_groups' => ['store',],
        ])
                     ->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $receipt = $inboundReceiptService->create($form->getData()->getOutboundReceipt());

        return $this->respond($receipt, JsonResponse::HTTP_CREATED, context:
            ['groups' => ['receipt.read', 'store.st-inbound']]);
    }
}
