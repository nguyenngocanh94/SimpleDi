<?php


namespace SimpleDi\Common;


class StringUtils
{
    static function endsWith(string $haystack, string $needle) : bool{
        $length = strlen( $needle );
        if( !$length ) {
            return true;
        }
        return substr( $haystack, -$length ) === $needle;
    }
}