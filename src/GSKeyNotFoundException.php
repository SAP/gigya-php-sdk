<?php

namespace Gigya\PHP;

/**
 * Gigya Socialize Key Not Found Exception
 */
class GSKeyNotFoundException extends GSException
{
    public function __construct($key)
    {
        parent::__construct('GSObject does not contain a value for key ' . $key);
    }
}