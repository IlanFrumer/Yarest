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
            throw new Exception\ServerMissingException('$_SERVER['."'".$property."'".'] must be set', 1);
        }
    }

    /**
     * [__construct description]
     */
    public function __construct()
    {

        $server         = self::server('SERVER_NAME');
        $php_self       = self::server('PHP_SELF');
        $document_root  = self::server('DOCUMENT_ROOT');
        $request_uri    = self::server('REQUEST_URI');
        $request_method = self::server('REQUEST_METHOD');

        // strip the file name
        $rootUri = dirname($php_self);

        // strip the query string
        $requestUri = parse_url($request_uri, PHP_URL_PATH);

        // absolute path of the root folder
        $path = $document_root.$rootUri;

        // relative to root folder
        $endPointUri = Helpers\Uri::stripURI($requestUri, $rootUri);

        // array representation of the end point
        $endPoint    = Helpers\Uri::uriToStack($endPointUri);

        // array representation of the virtual host
        $virtualHost = Helpers\Uri::uriToStack($rootUri);

        $this->values['server']      = $server;
        $this->values['path']        = $path;
        $this->values['method']      = $request_method;
        $this->values['virtualHost'] = $virtualHost;
        $this->values['endPoint']    = $endPoint;
    }
}
