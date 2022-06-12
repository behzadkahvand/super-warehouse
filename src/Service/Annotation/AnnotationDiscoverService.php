<?php

namespace App\Service\Annotation;

use Doctrine\Common\Annotations\Reader;
use Laminas\Code\Reflection\ClassReflection;
use Laminas\Code\Reflection\MethodReflection;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionProperty;
use Reflector;

class AnnotationDiscoverService
{
    public function __construct(private Reader $annotationReader)
    {
    }

    public function getClassPropertiesAnnotation(string $class, string $annotationClass): iterable
    {
        $reflectionClass = new ReflectionClass($class);

        foreach ($reflectionClass->getProperties() as $property) {
            yield from $this->getAnnotations($property, $annotationClass);
        }
    }

    public function getClassMethodsAnnotation(string $class, string $annotationClass): iterable
    {
        $reflectionClass = new ReflectionClass($class);

        foreach ($reflectionClass->getMethods() as $method) {
            yield from $this->getAnnotations($method, $annotationClass);
        }
    }

    public function getClassAnnotation(string $class, string $annotationClass): iterable
    {
        $reflectionClass = new ReflectionClass($class);

        yield from $this->getAnnotations($reflectionClass, $annotationClass);
    }

    protected function getAnnotations(Reflector $reflection, string $annotationClass): iterable
    {
        foreach ($reflection->getAttributes($annotationClass, ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            yield $reflection => $attribute->newInstance();
        }

        $annotation = $this->getSuitableAnnotation($reflection, $annotationClass);

        if ($annotation) {
            yield $reflection => $annotation;
        }
    }

    private function getSuitableAnnotation(Reflector $reflection, string $annotationClass): ?object
    {
        $reflectionClass = get_class($reflection);

        switch ($reflectionClass) {
            case ReflectionProperty::class:
                return $this->annotationReader->getPropertyAnnotation($reflection, $annotationClass);
            case MethodReflection::class:
                return $this->annotationReader->getMethodAnnotation($reflection, $annotationClass);
            case ClassReflection::class:
                return $this->annotationReader->getClassAnnotation($reflection, $annotationClass);
            default:
                return null;
        }
    }
}
