<?php

use App\Messaging\Messages\Command\Async\AsyncMessage;

if (!function_exists('camel_case')) {
    function camel_case(string $string): string
    {
        return lcfirst(pascal_case($string));
    }
}

if (!function_exists('pascal_case')) {
    function pascal_case(string $string): string
    {
        return str_replace([' ', '_', '-'], '', ucwords(strtolower($string), ' _-'));
    }
}

if (!function_exists('snake_case')) {
    function snake_case(string $string): string
    {
        $camelCased = preg_replace('~(?<=\\w)([A-Z])~u', '_$1', $string);

        if ($camelCased === null) {
            throw new RuntimeException(
                sprintf(
                    'preg_replace returned null for value "%s"',
                    $string
                )
            );
        }

        return mb_strtolower($camelCased);
    }
}

if (!function_exists('async_message')) {
    function async_message(object $message): AsyncMessage
    {
        return AsyncMessage::wrap($message);
    }
}

if (!function_exists('retry')) {
    function retry(callable $closure, int $times = 3, bool $sleep = true, int $sleepTimeInMillisecond = 100)
    {
        while ($times) {
            try {
                return $closure();
            } catch (Throwable $e) {
                $times--;

                if ($sleep) {
                    usleep(1000 * $sleepTimeInMillisecond);
                }

                if (0 === $times) {
                    throw $e;
                }
            }
        }
    }
}

if (!function_exists('is_datetime')) {
    function is_datetime($value): bool
    {
        try {
            new DateTime($value);

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}

if (!function_exists('sentry_catch_exception')) {
    function sentry_catch_exception(Throwable $exception): void
    {
        \Sentry\captureException($exception);
    }
}

if (!function_exists('get_class_name_from_namespace')) {
    function get_class_name_from_namespace(string $classNameWithNamespace): string
    {
        return substr($classNameWithNamespace, strrpos($classNameWithNamespace, '\\') + 1);
    }
}

if (!function_exists('get_class_name_from_object')) {
    function get_class_name_from_object(object $object): string
    {
        $classNameWithNamespace = get_class($object);

        return get_class_name_from_namespace($classNameWithNamespace);
    }
}

if (!function_exists('class_basename')) {
    function class_basename(object $entity): string
    {
        return basename(str_replace('\\', '/', get_class($entity)));
    }
}

if (!function_exists('object_to_array')) {
    function object_to_array(object $entity): array
    {
        $reflectionClass = new ReflectionClass(get_class($entity));
        $array           = [];

        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            if ($property->isInitialized($entity)) {
                $array[$property->getName()] = $property->getValue($entity);
            }
            $property->setAccessible(false);
        }

        return $array;
    }
}
