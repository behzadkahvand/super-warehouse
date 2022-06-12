<?php

namespace App\Service\StatusTransition\Traits;

use App\DTO\AllowTransitionConfigData;
use App\Service\StatusTransition\AllowTransitions\StateAllowedTransitionInterface;
use Exception;

trait ReceiptFactoryTransitionTrait
{
    protected function receiptFactoryTransition(?object $receipt = null): AllowTransitionConfigData
    {
        $allowTransitionClassName = $this->getAllowTransitionClass($receipt);

        if (!class_exists($allowTransitionClassName)) {
            throw new Exception("Receipt allow transition class not found!");
        }
        $allowTransitionObject = new $allowTransitionClassName();
        if (!($allowTransitionObject instanceof StateAllowedTransitionInterface)) {
            throw new Exception("Receipt allow transition class is not valid!");
        }

        return $allowTransitionObject->__invoke();
    }

    protected function getAllowTransitionClass(
        ?object $receipt = null,
        string $dirNamespace = "App\\Service\\StatusTransition\\AllowTransitions\\Receipt\\"
    ): string {
        return ($dirNamespace) .
            ($receipt ? get_class_name_from_object($receipt) : get_class_name_from_namespace(static::class)) .
            "AllowedTransition";
    }
}
