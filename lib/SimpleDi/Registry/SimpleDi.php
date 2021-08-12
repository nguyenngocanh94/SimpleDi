<?php


namespace SimpleDi\Registry;


use Closure;
use SimpleDi\Container\IContainer;
use SimpleDi\Exceptions\NotInitialSimpleDiException;



class SimpleDi
{
    const SINGLETON = 'SINGLETON';
    const TRANSIENT = 'TRANSIENT';

    private static ?SimpleDi $_instance = null;

    private array $_configArray;

    /**
     * @var string
     * @deprecated just for my project
     */
    public string $rootClass;

    private array $bindingArray;

    public IContainer $container;

    public Registry $registry;

    /**
     * SimpleDi constructor.
     * @param array $config
     */
    private function __construct(array $config){
        $this->_configArray  = $config;
        $this->bindingArray = $this->_configArray['registry'];
        $this->container = $this->_configArray['container'];
        $this->rootClass = $this->_configArray['root_class'];
        $this->registry = new Registry($this->bindingArray);
    }

    /**
     * @return SimpleDi|null
     */
    public static function getInstance(): ?SimpleDi
    {
        if (self::$_instance == null){
            throw new NotInitialSimpleDiException('must call build before do anything');
        }
        return self::$_instance;
    }

    /**
     * the initial of SimpleDI see the docs to start building it
     * the array parameter:
     * return [
     *  'container'=> $container,
     *  'registry'=> $autoMarks,
     *  'root_class'=>$class
     *  ];
     * @param array $config
     * @return SimpleDi
     */
    public static function build(array $config) : SimpleDi{
        if (self::$_instance==null){
            self::$_instance = new SimpleDi($config);
        }

        return self::$_instance;
    }


    /**
     * @param string $source
     * @param Closure $target
     * @throws NotInitialSimpleDiException
     */
    public static function binding(string $source, Closure $target){
        if (self::$_instance==null){
            throw new NotInitialSimpleDiException();
        }
        self::$_instance->registry->binding($source, $source);
        self::$_instance->container->register($source, $target());
    }

    /**
     * @param string $source
     * @param object $target
     * @throws NotInitialSimpleDiException
     */
    public static function bindingInstance(string $source, object $target){
        if (self::$_instance==null){
            throw new NotInitialSimpleDiException();
        }
        self::$_instance->registry->binding($source, $source);
        self::$_instance->container->register($source, $target);
    }

    /**
     * @return Registry
     * @throws NotInitialSimpleDiException
     */
    public static function getRegistry() : Registry{
        if (self::$_instance==null){
            throw new NotInitialSimpleDiException();
        }
        return self::$_instance->registry;
    }

    public static function getContainer() : IContainer{
        if (self::$_instance==null){
            throw new NotInitialSimpleDiException();
        }
        return self::$_instance->container;
    }
}