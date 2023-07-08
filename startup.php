<?php

require_once(findSrcDirectory() . buildPath(["src","App"]) . "Foundation.php");
require_once(findSrcDirectory() . "vendor/autoload.php");

/**
 * Dumps the contents of the variable provided to the screen then terminates execution
 */
function dd($variable)
{
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";

    die;
}

/**
 * Concatenates all path entries into a single string separated by the relevant directory separator
 */
function buildPath(array $pathItems)
{
    return join(DIRECTORY_SEPARATOR, $pathItems) . DIRECTORY_SEPARATOR;
} 

/**
 * Builds a reference to the 'src' directory. This is used when the file is located within api/classes for example, and
 * is used to ensure that the file can be loaded
 */
function findSrcDirectory(): string
{
    $return = "";

    $maxAttempts = 5;
    $attempts = 0;
    
    $priorDirectoryString = ".." . DIRECTORY_SEPARATOR;

    while (!file_exists($return . "src")) {
        $return .= $priorDirectoryString;

        $attempts++;

        if ($attempts === $maxAttempts) {
            throw new InvalidArgumentException("Can't find src directory $return");
        }
    }

    return $return;
}