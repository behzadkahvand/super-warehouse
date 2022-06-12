<?php

namespace App\Controller;

use App\Form\WarehouseStorageBinType;
use App\Service\ORM\QueryBuilderFilterService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\WarehouseStorageBin;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

/**
 * @Route("/warehouse-storage-bins", name="warehouse.storage.bin.")
 */
final class WarehouseStorageBinController extends Controller
{
    /**
     * @Route(name="index", methods={"GET"})
     *
     * @param Request                   $request
     * @param QueryBuilderFilterService $service
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Warehouse Storage Bin")
     * @OA\Response(
     *     response=200,
     *     description="Return list of warehouse storage bins",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=WarehouseStorageBin::class, groups={"warehouse.storage.bin.list"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    public function index(Request $request, QueryBuilderFilterService $service): JsonResponse
    {
        return $this->respondWithPagination(
            $service->filter(WarehouseStorageBin::class, $request->query->all()),
            [],
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['warehouse.storage.bin.list']]
        );
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     *
     * @param WarehouseStorageBin $warehouseStorageBin
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Warehouse Storage Bin")
     * @OA\Response(
     *     response=200,
     *     description="Return a warehouse storage bin details.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=WarehouseStorageBin::class, groups={"warehouse.storage.bin.read"})
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    public function show(WarehouseStorageBin $warehouseStorageBin): JsonResponse
    {
        return $this->respond(
            $warehouseStorageBin,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['warehouse.storage.bin.read']]
        );
    }

    /**
     * @Route(name="store", methods={"POST"})
     *
     * @param Request                $request
     * @param EntityManagerInterface $manager
     *
     * @return JsonResponse
     * @OA\Tag(name="Warehouse Storage Bin")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=WarehouseStorageBinType::class)))
     * @OA\Response(
     *     response=201,
     *     description="Warehouse storage bin successfully created, returns the newly created warehouse storage bin.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=WarehouseStorageBin::class, groups={"warehouse.storage.bin.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    public function store(Request $request, EntityManagerInterface $manager): JsonResponse
    {
        $form = $this->createForm(WarehouseStorageBinType::class)
                     ->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $warehouseStorageBin = $form->getData();
        $manager->persist($warehouseStorageBin);
        $manager->flush();

        return $this->respond(
            $warehouseStorageBin,
            JsonResponse::HTTP_CREATED,
            [],
            ['groups' => 'warehouse.storage.bin.read']
        );
    }

    /**
     * @Route("/{id}", name="update", methods={"PATCH"})
     *
     * @param Request                $request
     * @param WarehouseStorageBin    $warehouseStorageBin
     * @param EntityManagerInterface $manager
     *
     * @return JsonResponse
     * @throws ExceptionInterface
     *
     * @OA\Tag(name="Warehouse Storage Bin")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=WarehouseStorageBinType::class)))
     * @OA\Response(
     *     response=200,
     *     description="Warehouse storage bin successfully updated, returns the updated warehouse storage bin.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=WarehouseStorageBin::class, groups={"warehouse.storage.bin.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    public function update(
        Request $request,
        WarehouseStorageBin $warehouseStorageBin,
        EntityManagerInterface $manager
    ): JsonResponse {
        $form = $this->createForm(WarehouseStorageBinType::class, $warehouseStorageBin)
                     ->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $manager->flush();

        return $this->respond(
            $warehouseStorageBin,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => 'warehouse.storage.bin.read']
        );
    }
}
