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
    private $callbacks = array();

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
    /**
     * [__call description]
     * @param  [type] $method [description]
     * @param  [type] $args   [description]
     * @return [type]         [description]
     */
    public function __call($method, $args)
    {
        switch ($method) {
            case 'before':
            case 'inject':
            case 'after':
            case 'error':
            case 'notFound':
                if (count($args) == 1) {
                    Helpers\Arguments::checkCallable($args[0]);
                    $this->callbacks[$method] = $args[0];
                } else {
                    throw new \InvalidArgumentException('Method $method expects only 1 argument');
                }
                return $this;
            default:
                throw new \BadMethodCallException("Bad method: $method", 1);
        }
    }

    public function run($callback, array $inject)
    {
        try {

            if (is_callable($callback)) {
                call_user_func_array($callback, $inject);
            } elseif (array_key_exists($callback, $this->callbacks)) {
                call_user_func_array($this->callbacks[$callback], $inject);
            }

        } catch (Exception\Halt $error) {

            //let it pass
            
        } catch (\Exception $error) {
            
            if (array_key_exists('error', $this->callbacks)) {
                call_user_func_array($this->callbacks['error'], array($error));
            } else {
                // pass it up if there is no user defined error handler
                throw $error;
            }
        }
    }
}
