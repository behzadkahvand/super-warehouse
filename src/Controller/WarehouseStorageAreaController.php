<?php

namespace App\Controller;

use App\Entity\WarehouseStorageArea;
use App\Form\WarehouseStorageAreaType;
use App\Service\ORM\QueryBuilderFilterService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WarehouseStorageAreaController
 *
 * @Route("/warehouse-storage-areas", name="warehousesStorageArea.")
 */
final class WarehouseStorageAreaController extends Controller
{
    /**
     * @Route(name="index", methods={"GET"})
     *
     * @param Request $request
     * @param QueryBuilderFilterService $service
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Warehouse Storage Area")
     * @OA\Response(
     *     response=200,
     *     description="Return list of warehouse storage areas",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=WarehouseStorageArea::class, groups={"warehouseStorageArea.list"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    public function index(Request $request, QueryBuilderFilterService $service): JsonResponse
    {
        return $this->respondWithPagination(
            $service->filter(WarehouseStorageArea::class, $request->query->all()),
            [],
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['warehouseStorageArea.list']]
        );
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     *
     * @param WarehouseStorageArea $warehouseStorageArea
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Warehouse Storage Area")
     * @OA\Response(
     *     response=200,
     *     description="Return a warehouse storage area details.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=WarehouseStorageArea::class, groups={"warehouseStorageArea.read"})
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    public function show(WarehouseStorageArea $warehouseStorageArea): JsonResponse
    {
        return $this->respond(
            $warehouseStorageArea,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['warehouseStorageArea.read']]
        );
    }

    /**
     * @Route(name="store", methods={"POST"})
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Warehouse Storage Area")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=WarehouseStorageAreaType::class)))
     * @OA\Response(
     *     response=201,
     *     description="Warehouse storage area successfully created, returns the newly created warehouse storage area.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=WarehouseStorageArea::class, groups={"warehouseStorageArea.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    public function store(Request $request, EntityManagerInterface $manager): JsonResponse
    {
        $form = $this->createForm(WarehouseStorageAreaType::class)
                     ->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $warehouseStorageArea = $form->getData();

        $manager->persist($warehouseStorageArea);
        $manager->flush();

        return $this->respond(
            $warehouseStorageArea,
            JsonResponse::HTTP_CREATED,
            [],
            ['groups' => 'warehouseStorageArea.read']
        );
    }

    /**
     * @Route("/{id}", name="update", methods={"PATCH"})
     *
     * @param Request $request
     * @param WarehouseStorageArea $warehouseStorageArea
     * @param EntityManagerInterface $manager
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Warehouse Storage Area")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=WarehouseStorageAreaType::class)))
     * @OA\Response(
     *     response=200,
     *     description="Warehouse storage area successfully updated, returns the updated warehouse storage area.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=WarehouseStorageArea::class, groups={"warehouseStorageArea.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    public function update(
        Request $request,
        WarehouseStorageArea $warehouseStorageArea,
        EntityManagerInterface $manager
    ): JsonResponse {
        $form = $this->createForm(WarehouseStorageAreaType::class, $warehouseStorageArea)
                     ->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $manager->flush();

        return $this->respond(
            $warehouseStorageArea,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => 'warehouseStorageArea.read']
        );
    }
}
