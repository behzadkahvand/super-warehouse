<?php

namespace App\Annotations;

use Attribute;
use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @NamedArgumentConstructor
 * @Target({"PROPERTY"})
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Integrationable
{
    public function __construct(public array $groups = [])
    {
    }
}
