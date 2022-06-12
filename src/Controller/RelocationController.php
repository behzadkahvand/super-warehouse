<?php

namespace App\Controller;

use App\DTO\BinRelocationData;
use App\Entity\WarehouseStorageBin;
use App\Entity\ItemSerial;
use App\Form\BinRelocationType;
use App\Form\ItemSerialMovementType;
use App\Service\Relocate\Picking\RelocatePickingService;
use App\Service\Relocate\Stowing\RelocateBinService;
use App\Service\Relocate\Stowing\RelocateItemService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

#[Route("/relocation", name: "relocation.")]
final class RelocationController extends Controller
{
    /**
     * @OA\Tag(name="Relocation")
     * @OA\Parameter(
     *     name="itemSerial",
     *     in="query",
     *     description="The Item serial you want to pick",
     *     @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     *     name="storageBin",
     *     in="query",
     *     description="Source storageBin serial that you want pick from it",
     *     @OA\Schema(type="string")
     * )
     * @OA\Response(
     *     response=200,
     *     description="Return information of picked itemSerial",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=ItemSerial::class, groups={"relocation.item"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/item/pick", name: "item.pick", methods: ["PATCH"])]
    public function pickItem(Request $request, RelocatePickingService $relocatePickingService): JsonResponse
    {
        $form = $this->createForm(ItemSerialMovementType::class, null, [
            'validation_groups' => [
                'item.relocation',
            ],
        ])
                     ->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $storageBin = $form->getData()->getStorageBin();
        $itemSerial = $form->getData()->getItemSerial();

        $relocatePickingService->checkCanRelocateItem($storageBin, $itemSerial);

        return $this->respond($itemSerial, context: ['groups' => 'relocation.item']);
    }

    /**
     * @OA\Tag(name="Relocation")
     * @OA\Parameter(
     *     name="itemSerial",
     *     in="query",
     *     description="The Item serial that you want to stow it",
     *     @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     *     name="storageBin",
     *     in="query",
     *     description="Destination storageBin serial that you want stow to it",
     *     @OA\Schema(type="string")
     * )
     * @OA\Response(
     *     response=200,
     *     description="Return information of stowing pullList",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=ItemSerial::class, groups={"relocation.item"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/item/stow", name: "item.stow", methods: ["PATCH"])]
    public function stowItem(
        Request $request,
        RelocateItemService $relocateItemService
    ): JsonResponse {
        $form = $this->createForm(ItemSerialMovementType::class, null, [
            'validation_groups' => [
                'item.relocation',
            ],
        ])
                     ->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $storageBin = $form->getData()->getStorageBin();
        $itemSerial = $form->getData()->getItemSerial();

        $relocateItemService->relocate($storageBin, $itemSerial);

        return $this->respond($itemSerial, context: ['groups' => 'relocation.item']);
    }

    /**
     * @OA\Tag(name="Relocation")
     * @OA\Parameter(
     *     name="sourceStorageBin",
     *     in="query",
     *     description="StorageBin serial that you want to pick it",
     *     @OA\Schema(type="string")
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Return information of picked storageBin",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=WarehouseStorageBin::class, groups={"relocation.bin"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/bin/pick", name: "bin.pick", methods: ["PATCH"])]
    public function pickStorageBin(Request $request, RelocatePickingService $relocatePickingService): JsonResponse
    {
        $form = $this->createForm(BinRelocationType::class, null, [
            'validation_groups' => [
                'bin.relocation.pick',
            ],
        ])
                     ->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        /** @var BinRelocationData $formData */
        $formData = $form->getData();

        $relocatePickingService->checkCanRelocateBin($formData->getSourceStorageBin());

        return $this->respond($formData->getSourceStorageBin(), context: ['groups' => 'relocation.bin']);
    }

    /**
     * @OA\Tag(name="Relocation")
     * @OA\Parameter(
     *     name="sourceStorageBin",
     *     in="query",
     *     description="StorageBin serial that you want to pick it",
     *     @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     *     name="destinationStorageBin",
     *     in="query",
     *     description="StorageBin serial that you want to stow to it",
     *     @OA\Schema(type="string")
     * ),
     * @OA\Response(
     *     response=200,
     *     description="Return information of stowed storageBin",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=WarehouseStorageBin::class, groups={"relocation.bin"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/bin/stow", name: "bin.stow", methods: ["PATCH"])]
    public function stowStorageBin(Request $request, RelocateBinService $relocateBinService): JsonResponse
    {
        $form = $this->createForm(BinRelocationType::class, null, [
            'validation_groups' => [
                'bin.relocation.stow',
            ],
        ])
                     ->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        /** @var BinRelocationData $formData */
        $formData = $form->getData();

        $relocateBinService->relocate($formData->getSourceStorageBin(), $formData->getDestinationStorageBin());

        return $this->respond($formData->getDestinationStorageBin(), context: ['groups' => 'relocation.bin']);
    }
}
