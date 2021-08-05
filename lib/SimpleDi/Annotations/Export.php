<?php
namespace SimpleDi\Annotations;
use Doctrine\Common\Annotations\Annotation;


/**
 * Class Export
 * @Annotation
 * @Target({"CLASS"})
 */
class Export extends Annotation
{
    public string $typeOf = "";
    public string $scope = "SINGLETON";
}