<?php

namespace App\Controller;

use App\Entity\PickList;
use App\Entity\PickListBugReport;
use App\Form\PickListType;
use App\Service\ORM\QueryBuilderFilterService;
use App\Service\PickList\BugReport\PickListBugReportService;
use App\Service\PickList\BugReport\PickListBugReportStatusService;
use App\Service\PickList\ShipmentPickListService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

#[Route("/pick-lists", name: "pick.list.")]
final class PickListController extends Controller
{
    /**
     * @param Request                   $request
     * @param QueryBuilderFilterService $service
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Pick List")
     * @OA\Response(
     *     response=200,
     *     description="Return list of pick lists",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=PickList::class, groups={"pick.list.index"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route(name: "index", methods: ["GET"])]
    public function index(
        Request $request,
        QueryBuilderFilterService $service
    ): JsonResponse {
        return $this->respondWithPagination(
            $service->filter(PickList::class, $request->query->all()),
            context: ['groups' => ['pick.list.index']]
        );
    }

    /**
     * @param Request                 $request
     * @param ShipmentPickListService $pickListService
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Pick List")
     * @OA\Parameter(name="Body Parameters", in="query", @OA\Schema(ref=@Model(type=PickListType::class)))
     * @OA\Response(
     *     response=200,
     *     description="Return list of pick lists",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=PickList::class, groups={"pick.list.index"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route(name: "store", methods: ["POST"])]
    public function store(
        Request $request,
        ShipmentPickListService $pickListService
    ): JsonResponse {
        $form = $this->createForm(PickListType::class)
                     ->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->respondValidatorFailed($form);
        }

        $pickListService->create($form->getData());

        return $this->respond(
            context: ['groups' => ['pick.list.index']]
        );
    }

    /**
     * @param Request                   $request
     * @param QueryBuilderFilterService $service
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Pick List")
     * @OA\Response(
     *     response=200,
     *     description="Create bug report for a pick list",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=PickList::class, groups={"pick.list.index"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route(path: "/bug-reports", name: "bug.report.index", methods: ["GET"])]
    public function showBugReports(
        Request $request,
        QueryBuilderFilterService $service
    ): JsonResponse {
        return $this->respondWithPagination(
            $service->filter(PickListBugReport::class, $request->query->all()),
            context: ['groups' => ['pick.list.bug.report.read']]
        );
    }

    /**
     * @param PickList                 $pickList
     * @param PickListBugReportService $bugReportService
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Pick List")
     * @OA\Response(
     *     response=200,
     *     description="Create bug report for a pick list",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=PickList::class, groups={"pick.list.index"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route(path: "/{id}/bug-reports", name: "bug.report.store", methods: ["POST"])]
    public function storeBugReport(
        PickList $pickList,
        PickListBugReportService $bugReportService
    ): JsonResponse {
        return $this->respond(
            $bugReportService->create($pickList),
            context: ['groups' => ['pick.list.bug.report.read']]
        );
    }

    /**
     * @param int                            $pickListId
     * @param PickListBugReport              $pickListBugReport
     * @param PickListBugReportStatusService $bugReportStatusService
     *
     * @return JsonResponse
     *
     * @OA\Tag(name="Pick List")
     * @OA\Response(
     *     response=200,
     *     description="Create bug report for a pick list",
     *     @OA\JsonContent(
     *         type="object",
     *         @OA\Property(property="succeed", type="boolean"),
     *         @OA\Property(property="message", type="string"),
     *         @OA\Property(
     *            property="results",
     *            type="array",
     *            @OA\Items(ref=@Model(type=PickList::class, groups={"pick.list.index"})),
     *         ),
     *         @OA\Property(property="metas", type="object", @OA\Property(property="key", type="string"))
     *     )
     * )
     */
    #[Route(path: "/{pickListId}/bug-reports/{id}/update-status", name: "bug.report.update.status", methods: ["POST"])]
    public function bugReportUpdateStatus(
        int $pickListId,
        PickListBugReport $pickListBugReport,
        PickListBugReportStatusService $bugReportStatusService
    ): JsonResponse {
        $bugReportStatusService->update($pickListBugReport);

        return $this->respond(
            $pickListBugReport,
            context: ['groups' => ['pick.list.bug.report.read']]
        );
    }
}
