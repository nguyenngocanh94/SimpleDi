<?php


use SimpleDi\Generator\AutoGenerator;

require(__DIR__.'/../vendor/autoload.php');

if (isset($argc)) {
    if ($argc < 3 || $argc > 4){
        print "require two parameters. first is src code directory, second is the locale of auto mark file \n";
        exit(1);
    }
    require(__DIR__ . '/../../../../vendor/autoload.php');
    $generator = new AutoGenerator($argv[1],$argv[2]);
    $generator->write();
}
