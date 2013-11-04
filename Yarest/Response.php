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
     * header('HTTP/1.1 404 : Not Found')
     * @var string
     */
    private $status  = '404 : Not Found';

    // header('Content-type: application/json; charset=utf-8");
    /**
     * [$type description]
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
     * header('Allow: GET, POST')
     * @var [type]
     */
    private $allowed  = null;

    /**
     * [$body description]
     * @var string
     */
    private $body    = '';
    
    /**
     * [setStatus description]
     * @param [type] $status
     * @param [type] $message
     */
    public function setStatus($status, $message = null)
    {
        if (!is_null($message)) {
            $this->status  = "$status : $message";            
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
    public function setAllowed(array $methods)
    {        
        $this->allowed = $methods;
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
     * [getHeaders description]
     */
    public function getHeaders()
    {
        $headers = array();

        $headers[] = "HTTP/1.1 $this->status";
        $headers[] = "Content-type: $this->type; charset=$this->charset";
        

        if ($this->allowed) {            
            $list = implode(", ", $this->allowed);
            $headers[] = "Allow: $list";
        }

        return $headers;
    }

    /**
     * [getBody description]
     * @return [type]
     */
    public function getBody()
    {
        if (is_array($this->body)) {
            return json_encode($this->body);
        } else {
            return $this->body;
        }
    }
}
