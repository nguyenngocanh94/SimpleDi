<?php


namespace SimpleDi\Container;


/**
 * implement it to making the container that could use in simpleDI
 * Interface IContainer
 */
interface IContainer
{
    function register(string $class, $instance) : void;
    function resolver(string $class);
    function has(string $class);
}