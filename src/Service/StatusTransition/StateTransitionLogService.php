<?php

namespace App\Service\StatusTransition;

use App\Document\StatusTransitionLog\AdminLogData;
use App\Document\StatusTransitionLog\StatusTransitionLog;
use App\Entity\Admin;
use App\Service\StatusTransition\Exceptions\ODMStateLogClassNotFoundException;
use DateTime;
use Doctrine\ODM\MongoDB\DocumentManager;

class StateTransitionLogService
{
    private static array $logs = [];

    private ?Admin $user = null;

    public function __construct(private DocumentManager $documentManager)
    {
    }

    public function addLog(TransitionableInterface $entityObject, string $statusFrom, string $statusTo): self
    {
        $logCollectionClass = $this->findCollectionClass($entityObject);

        static::$logs[$logCollectionClass][] = [
            'entityId'   => $entityObject->getId(),
            'statusFrom' => $statusFrom,
            'statusTo'   => $statusTo,
        ];

        return $this;
    }

    protected function getLogs(): array
    {
        return static::$logs;
    }

    protected function clearLogs(): self
    {
        static::$logs = [];

        return $this;
    }

    public function setUser(?Admin $user): self
    {
        $this->user = $user;

        return $this;
    }

    protected function getUser(): ?AdminLogData
    {
        $userInfo = null;

        if ($this->user) {
            $userInfo = (new AdminLogData())
                ->setId($this->user->getId())
                ->setName($this->user->getName() . ' ' . $this->user->getFamily())
                ->setUsername($this->user->getUsername());
        }

        return $userInfo;
    }

    public function persist(): void
    {
        $loggedBy        = $this->getUser();
        $currentDateTime = new DateTime();

        foreach ($this->getLogs() as $collectionClass => $transitionLogs) {
            foreach ($transitionLogs as $transitionLog) {
                $documentClassInstance = $this->documentFactory($collectionClass);

                $documentClassInstance->setEntityId($transitionLog['entityId'])
                                      ->setStatusFrom($transitionLog['statusFrom'])
                                      ->setStatusTo($transitionLog['statusTo'])
                                      ->setUpdatedBy($loggedBy)
                                      ->setUpdatedAt($currentDateTime);

                $this->documentManager->persist($documentClassInstance);
            }
        }

        $this->documentManager->flush();

        $this->clearLogs();
    }

    private function findCollectionClass(TransitionableInterface $entityObject): string
    {
        $className = get_class_name_from_namespace(get_parent_class($entityObject) ?: get_class($entityObject)) . "StatusLog";

        $documentNamespace = "App\\Document\\StatusTransitionLog\\$className";
        if (!class_exists($documentNamespace)) {
            throw new ODMStateLogClassNotFoundException("$documentNamespace not found!");
        }

        return $documentNamespace;
    }

    private function documentFactory(string $collectionClass): StatusTransitionLog
    {
        return new $collectionClass();
    }
}
