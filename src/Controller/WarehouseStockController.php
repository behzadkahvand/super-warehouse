<?php

namespace App\Controller;

use App\Entity\WarehouseStock;
use App\Service\ORM\QueryBuilderFilterService;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;

/** @Route("/warehouse-stocks", name="warehouse.stock.") */
class WarehouseStockController extends Controller
{
    /**
     * @Route(name="index", methods={"GET"})
     *
     * @param Request                   $request
     * @param QueryBuilderFilterService $service
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Warehouse Stock")
     * @OA\Response(
     *     response=200,
     *     description="Return list of warehouse stocks",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=WarehouseStock::class, groups={"warehouse.stock.list"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    public function index(Request $request, QueryBuilderFilterService $service): JsonResponse
    {
        return $this->respondWithPagination(
            $service->filter(WarehouseStock::class, $request->query->all()),
            [],
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['warehouse.stock.list']]
        );
    }
}
