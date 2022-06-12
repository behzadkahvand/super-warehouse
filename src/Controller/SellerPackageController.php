<?php

namespace App\Controller;

use App\Dictionary\SellerPackageStatusDictionary;
use App\Entity\SellerPackage;
use App\Form\SellerPackageType;
use App\Service\ORM\QueryBuilderFilterService;
use App\Service\SellerPackage\SellerPackageService;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;

/** @Route("/seller-packages", name="seller.package.") */
final class SellerPackageController extends Controller
{
    /**
     * @Route(name="index", methods={"GET"})
     *
     * @param Request                   $request
     * @param QueryBuilderFilterService $service
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Seller Package")
     * @OA\Response(
     *     response=200,
     *     description="Return list of seller packages",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(type="object",
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="sellerName", type="string"),
     *                      @OA\Property(property="status", type="string"),
     *                      @OA\Property(property="createdAt", type="string"),
     *                      @OA\Property(property="quantity", type="integer"),
     *                      @OA\Property(property="inventoryCount", type="integer")
     *                ),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    public function index(
        Request $request,
        QueryBuilderFilterService $service
    ): JsonResponse {
        $queryBuilder = $service->filter(SellerPackage::class, $request->query->all());

        [$alias] = $queryBuilder->getRootAliases();

        $queryBuilder->leftJoin("$alias.packageItems", 'spi')
                     ->leftJoin("$alias.warehouse", 'w')
                     ->addSelect("PARTIAL $alias.{id, status, createdAt}")
                     ->addSelect("PARTIAL spi.{id, expectedQuantity, inventory}")
                     ->addSelect("PARTIAL w.{id, title}");

        return $this->respondWithPagination(
            $queryBuilder,
            statusCode: JsonResponse::HTTP_OK,
            context: ['groups' => ['seller.package.list']]
        );
    }

    /**
     * @param SellerPackage $sellerPackage
     * @param Request       $request
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Seller Package")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=SellerPackageType::class)))
     * @OA\Response(
     *     response=201,
     *     description="Seller package updated successfully, returns updated seller package.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=SellerPackage::class, groups={"seller.package.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/{id}", name: "update", methods: ["PATCH"])]
    public function update(
        SellerPackage $sellerPackage,
        Request $request,
        EntityManagerInterface $manager
    ): JsonResponse {
        $form = $this->createForm(SellerPackageType::class, $sellerPackage)
                     ->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $manager->flush();

        return $this->respond(
            $sellerPackage,
            context: ['groups' => 'seller.package.read',]
        );
    }

    /**
     * @param SellerPackage $sellerPackage
     * @param Request       $request
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Seller Package")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=SellerPackageType::class)))
     * @OA\Response(
     *     response=201,
     *     description="Seller package updated successfully, returns updated seller package.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=SellerPackage::class, groups={"seller.package.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/{id}/cancel", name: "cancel", methods: ["POST"])]
    public function cancel(
        SellerPackage $sellerPackage,
        SellerPackageService $sellerPackageService
    ): JsonResponse {
        $sellerPackageService->cancel($sellerPackage);

        return $this->respond(
            $sellerPackage,
            context: ['groups' => 'seller.package.read',]
        );
    }
}
