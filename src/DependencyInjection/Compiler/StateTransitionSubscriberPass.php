<?php

namespace App\DependencyInjection\Compiler;

use App\Service\StatusTransition\StateSubscriberNotifier;
use App\Service\StatusTransition\Subscribers\StateSubscriberInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class StateTransitionSubscriberPass
 */
final class StateTransitionSubscriberPass implements CompilerPassInterface
{
    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $refMap = [];
        foreach ($container->findTaggedServiceIds('app.state.transition.subscribers') as $id => $tags) {
            $class = $container->getDefinition($id)->getClass();
            $reflection = $container->getReflectionClass($class);

            if (!$reflection->implementsInterface(StateSubscriberInterface::class)) {
                throw new \RuntimeException(sprintf(
                    'Class %s must implement %s interface in order to be used as state transition subscriber',
                    $class,
                    StateSubscriberInterface::class
                ));
            }

            $refMap[$class] = new Reference($id);
        }

        $container->getDefinition(StateSubscriberNotifier::class)
                  ->setArgument('$container', ServiceLocatorTagPass::register($container, $refMap));
    }
}
