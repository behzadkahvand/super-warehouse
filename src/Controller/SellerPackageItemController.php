<?php

namespace App\Controller;

use App\Entity\SellerPackage;
use App\Form\SellerPackageItemType;
use App\Repository\SellerPackageRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\SellerPackageItem;
use Nelmio\ApiDocBundle\Annotation\Model;

#[Route("seller-packages/{sellerPackageId}/items", name: "seller.package.item.")]
class SellerPackageItemController extends Controller
{
    /**
     * @param SellerPackage $sellerPackage
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Seller Package Item")
     * @OA\Response(
     *     response=200,
     *     description="Return list of seller package items",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=SellerPackageItem::class, groups={"seller.package.item.show"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route(name: "show", methods: ["GET"])]
    public function showItems(
        int $sellerPackageId,
        SellerPackageRepository $sellerPackageRepository
    ): JsonResponse {
        $sellerPackage = $sellerPackageRepository->find($sellerPackageId);

        return $this->respond(
            $sellerPackage->getPackageItems(),
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['seller.package.item.show']]
        );
    }

    /**
     * @param int                    $sellerPackageId
     * @param SellerPackageItem      $sellerPackageItem
     * @param Request                $request
     * @param EntityManagerInterface $manager
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Seller Package Item")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=SellerPackageItemType::class)))
     * @OA\Response(
     *     response=200,
     *     description="Update seller package item quantities",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=SellerPackageItem::class, groups={"seller.package.item.show"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/{id}", name: "update", methods: ["PATCH"])]
    public function update(
        int $sellerPackageId,
        SellerPackageItem $sellerPackageItem,
        Request $request,
        EntityManagerInterface $manager
    ): JsonResponse {
        $form = $this->createForm(SellerPackageItemType::class, $sellerPackageItem)
                     ->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $manager->flush();

        return $this->respond(
            $sellerPackageItem,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['seller.package.item.show']]
        );
    }
}
