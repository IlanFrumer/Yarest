<?php

namespace Yarest;

/**
 * Yarest Route class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Route
{
    /**
     * [$pattern description]
     * @var array
     */
    
    public $pattern;
    /**
     * [$namespace description]
     * @var string
     */
    public $namespace;

    /**
     * [$folder description]
     * @var string
     */
    public $folder;

    /**
     * [$callbacks description]
     * @var array
     */
    public $callbacks = array();

    /**
     * [__construct description]
     * @param string|array $pattern
     * @param string|array $namespace
     * @param string|array $folder
     */
    public function __construct($pattern, $namespace, $folder)
    {
        $pattern   = Helpers\Uri::stripAsterisk($pattern);
        $pattern   = Helpers\Uri::uriToArray($pattern);

        $namespace = Helpers\Uri::namespaceToArray($namespace);
        $namespace = Helpers\Uri::arrayToNamespace($namespace);

        $folder    = Helpers\Uri::uriToArray($folder);
        $folder    = Helpers\Uri::arrayToURI($folder);

        $this->pattern   = $pattern;
        $this->namespace = $namespace;
        $this->folder    = $folder;
    }
}
