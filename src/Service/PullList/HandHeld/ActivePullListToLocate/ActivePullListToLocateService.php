<?php

namespace App\Service\PullList\HandHeld\ActivePullListToLocate;

use App\Entity\Admin;
use App\Entity\PullList;
use App\Repository\PullListRepository;

class ActivePullListToLocateService
{
    public function __construct(private PullListRepository $pullListRepository)
    {
    }

    public function get(Admin $admin): ?PullList
    {
        $latestActivePullListCount = $this->pullListRepository->latestActivePullListCount($admin);

        if ($latestActivePullListCount > 0) {
            return null;
        }

        return $this->pullListRepository->activePullListToLocate($admin);
    }
}
