<?php
namespace SimpleDi\Annotations;

use Doctrine\Common\Annotations\AnnotationReader as DocReader;

class AnnotationReader
{
    static function isMarkImport(string $class) : bool{
        $reader = new DocReader;
        $reflector = new \ReflectionClass($class);
        $annotations = $reader->getClassAnnotations($reflector);
        foreach ($annotations as $annotation){
            if ($annotation instanceof Import){
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $class
     * @return Export|null
     *
     * @throws \ReflectionException
     */
    static function getMarkExport(string $class) : ?Export{
        $reader = new DocReader;
        $reflector = new \ReflectionClass($class);
        $annotations = $reader->getClassAnnotations($reflector);
        foreach ($annotations as $annotation){
            if ($annotation instanceof Export){
                return $annotation;
            }
        }
        return null;
    }
}