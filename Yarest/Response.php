<?php

namespace Yarest;


/**
 * Yarest HTTP response class.
 *
 * # response codes
 * - 2xx Success
 * - 200 OK
 * - 201 Created
 * - 202 Accepted
 * - 204 No Content
 * - 4xx Client Error
 * - 400 Bad Request
 * - 401 Unauthorized
 * - 403 Forbidden
 * - 404 Not Found
 * - 405 Method Not Allowed
 * - 412 Precondition Failed
 * - 429 Too Many Requests
 * - 5xx Server Error
 * - 500 Internal Server Error
 * - 501 Not Implemented
 * - 503 Service Unavailable
 *
 * # response headers
 * - Allow: GET, HEAD
 * - Content-Type
 * - ETag
 * - Status
 * - WWW-Authenticate
 * - Double Submit Cookies
 * 
 * @package Yarest
 * @author Ilan Frumer <ilanfrumer@gmail.com>
 */


class Response
{
    /**
     * [$status description]
     * header('HTTP/1.1: 404 Not Found')
     * @var string
     */
    private $status  = '404 Not Found';

    // header('Content-type: application/json; charset=utf-8");
    /**
     * [$type description]
     * header('Allow: GET, POST')
     * @var string
     */
    private $type    = 'application/json';

    /**
     * [$charset description]
     * @var string
     */
    private $charset = 'utf-8';

    /**
     * [$allow description]
     * @var [type]
     */
    private $allow   = null;

    /**
     * [$body description]
     * @var string
     */
    private $body    = '';


    /**
     * [__construct description]
     */
    public function __construct()
    {
        ob_start();

        set_exception_handler(function ($exception) {
            $this->status = '500 Application Level Exception';
            $this->type   = 'text/html';

            var_dump($exception);
            die();
        });

        set_error_handler(function ($a, $b, $c, $d, $e) {
            
            $this->status = '500 Application Level Error';
            $this->type   = 'text/html';

            $error = array();
            $error['number']  = $a;
            $error['message'] = $b;
            $error['file']    = $c;
            $error['line']    = $d;
            $error['context'] = $e;
            var_dump($error);
            
            die();
        });
    }
    
    /**
     * [setStatus description]
     * @param [type] $status
     * @param [type] $message
     */
    public function setStatus($status, $message = null)
    {
        if ($message) {
            $this->status  = "$status $message";
        } else {
            $this->status  = (string) $status;
        }
        return $this;
    }

    /**
     * [setContentType description]
     * @param [type] $type
     * @param [type] $charset
     */
    public function setContentType($type, $charset = null)
    {
        $this->type    = $type;
        if ($charset) {
            $this->charset = $charset;
        }

        return $this;
    }

    /**
     * [setAllowed description]
     * @param array $methods
     */
    public function setAllowed(array $methods = array('GET'))
    {
        $this->allow = $methods;
    }

    /**
     * [appendBody description]
     * @param  [type] $body
     * @return [type]
     */
    public function appendBody($body)
    {
        $this->body.= $body;
        return $this;
    }

    /**
     * [setBody description]
     * @param [type] $body
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * [setHeaders description]
     */
    private function setHeaders()
    {

        header("HTTP/1.1: $this->status");
        header("Content-type: $this->type; charset=$this->charset");
        
        if ($this->allow) {
            header('Allow: '.implode(', ', $this->allow));
        }

    }

    /**
     * [__toString description]
     * @return string
     */
    public function __toString()
    {
        $this->setHeaders();
        flush();

        if (is_array($this->body)) {
            $this->body = json_encode($this->body);
        }

        return $this->body;
    }
}
