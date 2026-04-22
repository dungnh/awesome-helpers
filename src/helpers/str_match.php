<?php

function str_match($string, $pattern)
{
    // Keep helper behavior stable across Laravel versions where Str::match semantics changed.
    if (@preg_match($pattern, $string) === false) {
        $pattern = '#'.preg_quote($pattern, '#').'#';
    }

    return preg_match($pattern, $string) === 1;
}
