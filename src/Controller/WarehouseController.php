<?php

namespace App\Controller;

use App\Entity\Warehouse;
use App\Form\WarehouseType;
use App\Service\ORM\QueryBuilderFilterService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/warehouses", name: "warehouse.")]
class WarehouseController extends Controller
{
    /**
     * @param Request $request
     * @param QueryBuilderFilterService $service
     *
     * @return JsonResponse
     * @OA\Tag(name="Warehouse")
     * @OA\Response(
     *     response=200,
     *     description="Return list of warehouses",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=Warehouse::class, groups={"warehouse.list"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route(name: "index", methods: ["GET"])]
    public function index(Request $request, QueryBuilderFilterService $service): JsonResponse
    {
        return $this->respondWithPagination(
            $service->filter(Warehouse::class, $request->query->all()),
            context: ['groups' => ['warehouse.list']]
        );
    }

    /**
     * @OA\Tag(name="Warehouse")
     * @OA\Response(
     *     response=200,
     *     description="Return a warehouse details.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=Warehouse::class, groups={"warehouse.read"})
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/{id}", name: "show", methods: ["GET"])]
    public function show(Warehouse $warehouse): JsonResponse
    {
        return $this->respond($warehouse, context: ['groups' => ['warehouse.read']]);
    }

    /**
     * @OA\Tag(name="Warehouse")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=WarehouseType::class)))
     * @OA\Response(
     *     response=201,
     *     description="Warehouse successfully created, returns the newly created warehouse.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=Warehouse::class, groups={"warehouse.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route(name: "store", methods: ["POST"])]
    public function store(Request $request, EntityManagerInterface $manager): JsonResponse
    {
        $form = $this->createForm(WarehouseType::class)
                     ->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $warehouse = $form->getData();

        $manager->persist($warehouse);
        $manager->flush();

        return $this->respond($warehouse, JsonResponse::HTTP_CREATED, context: ['groups' => 'warehouse.read']);
    }

    /**
     * @OA\Tag(name="Warehouse")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=WarehouseType::class)))
     * @OA\Response(
     *     response=200,
     *     description="Warehouse successfully updated, returns the updated warehouse.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=Warehouse::class, groups={"warehouse.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/{id}", name: "update", methods: ["PATCH"])]
    public function update(
        Request $request,
        Warehouse $warehouse,
        EntityManagerInterface $manager
    ): JsonResponse {
        $form = $this->createForm(WarehouseType::class, $warehouse)
                     ->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $manager->flush();

        return $this->respond($warehouse, context: ['groups' => 'warehouse.read']);
    }
}
