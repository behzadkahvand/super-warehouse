<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\AdminType;
use App\Service\ORM\QueryBuilderFilterService;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route("/admins", name: "admin.")]
class AdminController extends Controller
{
    public function __construct(protected EntityManagerInterface $manager, protected UserPasswordHasherInterface $hasher)
    {
    }

    /**
     * @param Request $request
     * @param QueryBuilderFilterService $service
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Admin")
     * @OA\Response(
     *     response=200,
     *     description="Return list of admins",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=Admin::class, groups={"admin.list", "timestampable"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route(name: "index", methods: ["GET"])]
    public function index(Request $request, QueryBuilderFilterService $service): JsonResponse
    {
        return $this->respondWithPagination(
            $service->filter(Admin::class, $request->query->all()),
            context: ['groups' => ['admin.list', 'timestampable']]
        );
    }

    /**
     * @param Admin $admin
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Admin")
     * @OA\Response(
     *     response=200,
     *     description="Return a admin details.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=Admin::class, groups={"admin.read"})
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/{id}", name: "show", methods: ["GET"])]
    public function show(Admin $admin): JsonResponse
    {
        return $this->respond($admin, context: ['groups' => ['admin.read']]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @OA\Tag(name="Admin")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=AdminType::class)))
     * @OA\Response(
     *     response=201,
     *     description="Admin successfully created, returns the newly created admin.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=Admin::class, groups={"admin.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route(name: "store", methods: ["POST"])]
    public function store(Request $request): JsonResponse
    {
        $form = $this->createForm(AdminType::class)
                     ->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        /** @var Admin $admin */
        $admin = $form->getData();
        $admin->setPassword($this->hasher->hashPassword($admin, $admin->getPlainPassword()));
        $admin->eraseCredentials();

        $this->manager->persist($admin);
        $this->manager->flush();

        return $this->respond(
            $admin,
            JsonResponse::HTTP_CREATED,
            context: ['groups' => 'admin.read']
        );
    }

    /**
     * @param Request $request
     * @param Admin $admin
     *
     * @return JsonResponse
     * @throws ExceptionInterface
     * @OA\Tag(name="Admin")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=AdminType::class)))
     * @OA\Response(
     *     response=200,
     *     description="Admin successfully updated, returns the updated admin.",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="object",
     *            ref=@Model(type=Admin::class, groups={"admin.read"}),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route("/{id}", name:"update", methods:["PATCH"])]
    public function update(Request $request, Admin $admin): JsonResponse
    {
        $form = $this->createForm(AdminType::class, $admin)
                     ->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        if ($admin->getPlainPassword()) {
            $admin->setPassword($this->hasher->hashPassword($admin, $admin->getPlainPassword()));
            $admin->eraseCredentials();
        }

        $this->manager->flush();

        return $this->respond($admin, context: ['groups' => 'admin.read']);
    }
}
