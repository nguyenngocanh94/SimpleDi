# Simple DI
An easily setup dependency injection library that help you can set up Dependency injection in your project in 1 minute.
## Documentation
At your `index.php` or any start endpoint file. put the configuration for `SimpleDi`
```php
// you can put your `array` that act as your old DI container to it as param
$container = new \SimpleDi\Container\SdContainer();
$autoMarks = [
    YourInterface::class=> YourImplementClass:class
];
return [
    'container'=> $container,
    'registry'=> $autoMarks
];
```
and at anywhere you want to get instance:
```php
 // at my routes table reader.
$controller = App::resolver($controllerName);
```