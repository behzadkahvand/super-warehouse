<?php

namespace App\Service\PickList\BugReport\Status;

use App\Service\StatusTransition\StateTransitionHandlerService;

abstract class AbstractPickListBugReportStatus implements PickListBugReportStatusInterface
{
    public function __construct(protected StateTransitionHandlerService $transitionHandlerService)
    {
    }
}
