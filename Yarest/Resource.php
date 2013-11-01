<?php

namespace Yarest;

/**
 * Yarest Resource class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */

/**
 * Annotations:
 *
 *
 * Alias:
 *
 *
 * Arguments:
 */
abstract class Resource extends \Pimple
{

    final public function __construct (array $values = array())
    {
        $this->values = $values;
    }
}
