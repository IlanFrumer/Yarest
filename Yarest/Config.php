<?php

namespace Yarest;

/**
 * Yarest configuration class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Config extends Pimple
{
    /**
     * 
     * @param  array $config User configuration to override defaults
     */
    
    public function __construct(array $config = array())
    {
        $defaults = Helpers::getConfig('defaults');

        parent::__construct(array_merge($defaults, $config));

        $this['alias'] = $this->share(function () {
            return Helpers::getConfig('alias');
        });
    }
}
