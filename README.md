# Simple DI
An easily setup dependency injection library that help you can set up Dependency injection in your project faster.
## Documentation

### Step I.
**Mark with your class** as ```@Export()``` to Simple DI release this class need to be keep on container.
In any other class. if you want to inject by property, **mark this property as** ``@Import()``

```php
use SimpleDi\Annotations\Export;
use SimpleDi\Annotations\Import;

/**
* @Export()
 */
class TrainingPlanController extends AbstractController
{
    /**
     * @Import()
     * @var MessageFactory
     */
    protected MessageFactory $mf;
    /**
     * @Import()
     * @var CourseService
     */
    protected CourseService $courseService;
}
```
We have an hacked for class that ended with **Controller**. 
It doesn't need mark as ``@Export`` at all.

### Step II

I have a small script that find any class that mark with ``@Export`` and create your own `$register` array. so you don't need to create by hand anymore.
Stand at your project root. then run this command:
- PROJECT = root project folder
- FILE = the file that store registry map
```bash
PROJECT=application FILE=proxy/registry.php ./vendor/ericsnguyen/simple-di/bin/generate
```
it would print some error, but ignore it, after run, double check it.

### Step III
At your `index.php` or any start endpoint file. put the configuration for `SimpleDi`
```php
// you can put your `array` that act as your old DI container to it as param
$container = new class implements \SimpleDi\Container\IContainer(){
    // in my library we have SdContainer class that implements IContainer
    // but you can implement one if you want.
};

$register = [
    YourInterface::class=> YourImplementClass:class
];

SimpleDi::build([
       // I have a variable $GLOBALS act as an instances holder for my container
      'container'=>new SdContainer($GLOBALS),
      // an $register array.
      'registry'=> include PROJECT_ROOT."/proxy/registry.php",
       // it is optional -> get any singleton existed in your project, but you can ignore it.
      'root_class'=> Singleton::class
]);
```

and at anywhere you want to get this instance:
```php
 // at my routes table reader.
$controller = App::resolver($controllerName);
```