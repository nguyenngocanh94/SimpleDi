<?php

use SimpleDi\Generator\AutoGenerator;

require_once "../lib/SimpleDi/Generator/Parser.php";
require_once "../lib/SimpleDi/Generator/AutoGenerator.php";

if (isset($argc)) {
    if ($argc != 3){
        print "require two parameters. first is src code directory, second is the locale of auto mark file \n";
        exit(1);
    }

    $generator = new AutoGenerator($argv[1],$argv[2]);
    $generator->write();
}
