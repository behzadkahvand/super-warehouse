<?php

namespace App\Controller;

use App\Dictionary\ReceiptTypeDictionary;
use App\Entity\PickList;
use App\Form\HandHeldConfirmPickListType;
use App\Repository\ItemSerialRepository;
use App\Repository\PickListRepository;
use App\Exceptions\HandHeld\InvalidItemSerialException;
use App\Exceptions\HandHeld\InvalidStorageBinException;
use App\Service\PickList\HandHeld\Picking\HandHeldPickingService;
use App\Service\PickList\HandHeld\ShowList\HandHeldListService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Security\Core\Security;

#[Route("/hand-held/pick-lists", name: "hand.held.pick.list.")]
final class HandHeldPickListController extends Controller
{
    /**
     * @OA\Tag(name="Hand Held - Pick List")
     * @OA\Response(
     *     response=200,
     *     description="Return list of pick lists for hand held processing",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=PickList::class, groups={"pick.hand.held.list"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route(name: "show", methods: ["GET"])]
    public function show(Request $request, HandHeldListService $handHeldListService): JsonResponse
    {
        $receiptType = $request->get('receiptType');

        if (
            !$receiptType || !in_array(
                $receiptType,
                [ReceiptTypeDictionary::GOOD_ISSUE, ReceiptTypeDictionary::STOCK_TRANSFER]
            )
        ) {
            return $this->respondInvalidParameters("ReceiptType is not valid!");
        }

        $pickList = $handHeldListService->getList($receiptType);

        return $this->respond($pickList, context: ['groups' => 'pick.hand.held.list']);
    }

    /**
     * @OA\Tag(name="Hand Held - Pick List")
     * @OA\Parameter(
     *     name="Body Parameters",
     *     in="query",
     *     @OA\Schema(
     *         type="object",
     *         @OA\Property(
     *             property="items",
     *             type="array",
     *             description="Array of pick list ids",
     *             @OA\Items(type="integer"),
     *         ),
     *         example={"items"={1,2,3}}
     *     )
     * )
     * @OA\Response(
     *     response=200,
     *     description="Confirm pick lists has done successfully",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string", default="Confirm pick lists has done successfully"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(type="string")
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     * @OA\Response(
     *     response=422,
     *     description="Failed validation",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean", default=false),
     *         @OA\Property(property="message", type="string", default="Validation error has been detected!"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            @OA\Property(property="propertyPath", type="array", @OA\Items(type="string"))
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/confirm", name: "confirm", methods: ["POST"])]
    public function confirm(Request $request, HandHeldListService $handHeldListService): JsonResponse
    {
        $form = $this->createForm(HandHeldConfirmPickListType::class, null)
                     ->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $handHeldListService->confirmList($form->getData()->getItems());

        return $this->setMessage('Confirm pick lists has done successfully')
                    ->respond();
    }

    /**
     * @OA\Tag(name="Hand Held - Pick List")
     * @OA\Parameter(
     *     name="binSerial",
     *     in="query",
     *     description="Storage bin serial",
     *     @OA\Schema(type="string")
     * )
     * @OA\Response(
     *     response=200,
     *     description="Scan storage bin serial has done successfully",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string", default="Scan storage bin serial has done successfully"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(type="string")
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     * @OA\Response(
     *     response=422,
     *     description="Failed validation",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean", default=false),
     *         @OA\Property(property="message", type="string", default="Validation error has been detected!"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            @OA\Property(property="propertyPath", type="array", @OA\Items(type="string"))
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/scan/bin", name: "scan.bin.serial", methods: ["GET"])]
    public function scanBinSerial(
        Request $request,
        PickListRepository $pickListRepository
    ): JsonResponse {
        $binSerial = $request->get('binSerial');

        if (!$binSerial) {
            return $this->respondInvalidParameters("binSerial is not valid!");
        }

        $pickerActivePickList = $pickListRepository->getPickerActivePickListByStorageBinSerial(
            $binSerial,
            $this->getUser()
        );

        if (!$pickerActivePickList) {
            throw new InvalidStorageBinException("You have not any active picklist in given storageBin!");
        }

        return $this->setMessage('Scan storage bin serial has done successfully')
                    ->respond();
    }

    /**
     * @OA\Tag(name="Hand Held - Pick List")
     * @OA\Parameter(
     *     name="itemSerial",
     *     in="query",
     *     description="Item serial",
     *     @OA\Schema(type="string")
     * )
     * @OA\Response(
     *     response=200,
     *     description="Return information of picking pickList",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=PickList::class, groups={"pick.hand.held.picking"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/pick/{id}", name: "pick", methods: ["PATCH"])]
    public function pick(
        Request $request,
        PickList $pickList,
        ItemSerialRepository $itemSerialRepository,
        HandHeldPickingService $handHeldPickingService
    ): JsonResponse {
        $itemSerial = $request->get("itemSerial");

        if (!$itemSerial) {
            return $this->respondInvalidParameters("itemSerial can not be empty!");
        }

        $itemSerialEntity = $itemSerialRepository->findOneBy(['serial' => $itemSerial]);

        if (!$itemSerialEntity) {
            throw new InvalidItemSerialException("There is no any itemSerial for given serial!");
        }

        $handHeldPickingService->pick($pickList, $itemSerialEntity);

        return $this->respond($pickList, context: ['groups' => 'pick.hand.held.picking']);
    }
}
