<?php


namespace SimpleDi\Registry;


use SimpleDi\App;
use SimpleDi\Exceptions\NotInitialSimpleDiException;
use SimpleDi\Exceptions\NotRegisterException;

class AutoResolver
{
    /**
     * @param array $items
     * @return array
     * @throws NotRegisterException|NotInitialSimpleDiException
     */
    public static function resolve(array $items): array
    {
        $container = SimpleDi::getInstance()->container;
        $registry = SimpleDi::getInstance()->registry;
        $rootClass = SimpleDi::getInstance()->rootClass;
        $dependencyInstances = [];
        foreach ($items as $className){
            $instance = $container->resolver($className);
            if ($instance!=null){
                $dependencyInstances[] = $instance;
            }else{
                // need remove in future.
                // so just ignore it.
                if (isset($rootClass) && is_subclass_of($className, $rootClass)){
                    $instance = call_user_func(array($className, 'getInstance'));
                    $container->register($className, $instance);
                    $dependencyInstances[] = $instance;
                }else{
                    //must be in Registry
                    if ($registry->isRegister($className)){
                        list($bindClass, $scope) = $registry->getBindingScope($className);
                        if ($scope==SimpleDi::TRANSIENT){
                            $dependencyInstances[] = App::resolver($bindClass);
                        }else{
                            // if container save the instance, drop it out.
                            if (is_object($bindClass)){
                                $instance = $bindClass;
                            }else{
                                $instance = App::resolver($bindClass);
                                $container->register($className, $instance);
                            }

                            $dependencyInstances[] = $instance;
                        }
                    }else{
                        throw new NotRegisterException('not found class '.$className.' is not mark as @Export');
                    }
                }
            }
        }

        return $dependencyInstances;
    }
}