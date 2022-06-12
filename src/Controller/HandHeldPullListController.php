<?php

namespace App\Controller;

use App\Entity\PullList;
use App\Entity\PullListItem;
use App\Entity\WarehouseStorageBin;
use App\Form\ItemSerialMovementType;
use App\Repository\PullListItemRepository;
use App\Repository\PullListRepository;
use App\Service\PullList\HandHeld\ActivePullListToLocate\ActivePullListToLocateService;
use App\Service\PullList\HandHeld\StowingProcess\Exceptions\PullListItemNotFoundException;
use App\Service\PullList\HandHeld\StowingProcess\StowingProcessService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

#[Route("/hand-held/pull-lists", name: "hand-held.pull-list.")]
final class HandHeldPullListController extends Controller
{
    /**
     * @OA\Tag(name="Hand Held - Pull List")
     * @OA\Parameter(
     *     name="storageBin",
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
     *            @OA\Items(ref=@Model(type=WarehouseStorageBin::class, groups={"stow.hand-held.scan-serial"})),
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
    #[Route("/scan/bin", name: "scan.bin.serial", methods: ["POST"])]
    public function scanStorageBin(Request $request): JsonResponse
    {
        $form = $this->createForm(ItemSerialMovementType::class, null, [
            'validation_groups' => [
                'handHeld.pullList',
            ],
        ])
                     ->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $storageBin = $form->getData()->getStorageBin();

        if (!$storageBin->checkIsActiveForStow()) {
            return $this->respondInvalidParameters("Scanned storageBin is not active for stow");
        }

        return $this->setMessage('Scan storage bin serial has done successfully')
                    ->respond($storageBin, context: ['groups' => 'stow.hand-held.scan-serial']);
    }

    /**
     * @OA\Tag(name="Hand Held - Pull List")
     * @OA\Parameter(
     *     name="itemSerial",
     *     in="query",
     *     description="Item serial",
     *     @OA\Schema(type="string")
     * ),
     * @OA\Parameter(
     *     name="storageBin",
     *     in="query",
     *     description="StorageBin serial",
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
     *            @OA\Items(ref=@Model(type=PullList::class, groups={"stow.hand-held.stowing"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/stow/{id}", name: "stow", methods: ["PATCH"])]
    public function stow(
        Request $request,
        PullList $pullList,
        PullListItemRepository $pullListItemRepository,
        StowingProcessService $stowingProcessService
    ): JsonResponse {
        $form = $this->createForm(ItemSerialMovementType::class, null, [
            'validation_groups' => [
                'handHeld.pullList',
                'handHeld.pullList.stow',
            ],
        ])
                     ->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $storageBin = $form->getData()->getStorageBin();
        $itemSerial = $form->getData()->getItemSerial();

        $pullListItem = $pullListItemRepository->findPullListItemByPullListAndItemSerial($pullList, $itemSerial);
        if (!$pullListItem) {
            throw new PullListItemNotFoundException("There are not any pullList Item for given pullList and itemSerial!");
        }

        $stowingProcessService->stow($pullList, $pullListItem, $storageBin, $itemSerial);

        return $this->respond($pullList, context: ['groups' => 'stow.hand-held.stowing']);
    }

    /**
     * @OA\Tag(name="Hand Held - Pull List")
     * @OA\Parameter(
     *     name="storageBin",
     *     in="query",
     *     description="StorageBin serial",
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
     *            @OA\Items(ref=@Model(type=PullList::class, groups={"stow.hand-held.stowing"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/confirm-all/items/{id}", name: "confirm-all", methods: ["PATCH"])]
    public function confirmAll(
        Request $request,
        PullListItem $pullListItem,
        StowingProcessService $stowingProcessService
    ): JsonResponse {
        $form = $this->createForm(ItemSerialMovementType::class, null, [
            'validation_groups' => [
                'handHeld.pullList',
            ],
        ])
                     ->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $storageBin = $form->getData()->getStorageBin();

        $stowingProcessService->batchStow($pullListItem, $storageBin);

        return $this->respond($pullListItem->getPullList(), context: ['groups' => 'stow.hand-held.stowing']);
    }

    /**
     * @OA\Tag(name="Hand Held - Pull List")
     * @OA\Response(
     *     response=200,
     *     description="Get active pull list to locate",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string", default="Get active pull list to locate"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=PullList::class, groups={"pull-list.hand-held.active-for-locate"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/active-for-locate", name: "active-for-locate", methods: ["GET"])]
    public function activeLocatorPullList(ActivePullListToLocateService $activePullListToLocate): JsonResponse
    {
        return $this->respond(
            $activePullListToLocate->get($this->getUser()) ?? [],
            context: ['groups' => 'pull-list.hand-held.active-for-locate']
        );
    }

    /**
     * @OA\Tag(name="Hand Held - Pull List")
     * @OA\Response(
     *     response=200,
     *     description="Get current locator active pull list",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string", default="Get current locator active pull list"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=PullList::class, groups={"pull-list.hand-held.show-active-list"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/show-active-list", name: "show-active-list", methods: ["GET"])]
    public function showActiveList(PullListRepository $pullListRepository): JsonResponse
    {
        return $this->respond(
            $pullListRepository->getLatestLocatorActivePullList($this->getUser()) ?? [],
            context: ['groups' => 'pull-list.hand-held.show-active-list']
        );
    }
}
