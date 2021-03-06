<?php

namespace Yarest;

/**
 * Yarest HTTP request class.
 *
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */
class Request extends ReadOnlyArray
{
    
    /**
     * [server description]
     * @param  [type] $property
     * @return [type]
     */
    private static function server($property)
    {
        if (isset($_SERVER[$property])) {
            return $_SERVER[$property];
        } else {
            return null;
        }
    }

    /**
     * [__construct description]
     */
    public function __construct()
    {

        $host           = self::server('HTTP_HOST');
        $php_self       = self::server('PHP_SELF');
        $document_root  = self::server('DOCUMENT_ROOT');
        $request_uri    = self::server('REQUEST_URI');
        $request_method = self::server('REQUEST_METHOD');
        $request_token  = self::server('HTTP_X_AUTH_TOKEN');
        $query_string   = self::server('QUERY_STRING');
        $https          = self::server('HTTPS');

        // strip the file name
        $root = Helpers\Uri::uriToArray(dirname($php_self));
        $document_root = Helpers\Uri::uriToArray($document_root);

        // absolute path of the root folder
        $path = Helpers\Uri::arrayToUri(array_merge($document_root, $root));

        $path = empty($path) ? "/" : "/$path/";

        $root = Helpers\Uri::arrayToUri($root);

        // strip the query string
        $request_uri = parse_url($request_uri, PHP_URL_PATH);

        // relative to root
        $request_uri = Helpers\Uri::substrURI($request_uri, $root);

        // array representation of the end point
        $request_uri = Helpers\Uri::uriToArray($request_uri);
        
        // array representation of the virtual host
        $virtual_host = Helpers\Uri::uriToArray($root);

        // http://stackoverflow.com/questions/4042962/php-http-or-https-how-can-one-tell
        $protocol = is_null($https) || $https === "Off" ? "http" : "https";

        // Query string into array
        parse_str($query_string, $query);

        // get the request body
        $body = $this->parseInput();

        $this->values['host']     = $host;
        $this->values['path']     = $path;
        $this->values['method']   = $request_method;
        $this->values['virtual']  = $virtual_host;
        $this->values['uri']      = $request_uri;
        $this->values['body']     = $body;
        $this->values['token']    = $request_token;
        $this->values['protocol'] = $protocol;
        $this->values['query']    = $query;
    }

    private function parseInput()
    {
        if (!empty($_GET)) {
            return $_GET;
        }

        if (!empty($_POST)) {
            return $_POST;
        }

        $input = @file_get_contents('php://input');

        $params = json_decode($input, true);
        // $params = json_decode(preg_replace('/\'/', '"', $input), true);

        return $params ?: array();
    }
}
