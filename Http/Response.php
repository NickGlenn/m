<?php

namespace m\Http;


/**
 * An object representation of the HTTP response.
 * 
 * @package m\Http
 */
class Response
{

    /**
     * @var string The response body
     */
    protected $_body = '';

    /**
     * @var array The response headers
     */
    protected $_headers = array();

    /**
     * @var \m\Http\SessionInterface Session object for flashing
     */
    protected $_session;

    /**
     * @var int The response status code
     */
    protected $_status = 200;

    /**
     * @var array HTTP response codes and messages
     */
    protected static $_codes = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        // Successful 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported'
    );

    /**
     * Constructor.
     *
     * @param string $body
     * @param int $status
     * @param array $headers
     */
    public function __construct($body = '', $status = 200, array $headers = array())
    {
        $this->_body = $body;
        $this->_status = (integer) $status;
        $this->_headers = $headers;
    }

    /**
     * Sets a session object for flash messaging.
     *
     * @param \m\Http\SessionInterface $session
     * @return \m\Http\SessionInterface
     */
    public function setSession(SessionInterface $session)
    {
        $this->_session = $session;

        return $this;
    }

    /**
     * Returns the set session object.
     *
     * @return \m\Http\SessionInterface
     * @throws \Exception
     */
    public function getSession()
    {
        if (!isset($this->_session))
            throw new \Exception('m\Http\Response: No session object available to flash "'.$key.'" to.');

        return $this->_session;
    }

    /**
     * Pass flash data to the next page.
     *
     * @param string $key
     * @param mixed $value
     * @return \m\Http\Response
     * @throws \Exception
     */
    public function pass($key, $value)
    {
        if (!isset($this->_session))
            throw new \Exception('m\Http\Response: No session object available to flash "'.$key.'" to.');

        $this->_session->flash($key, $value);

        return $this;
    }

    /**
     * Add an array of data to pass to the next page.
     *
     * @param array $data
     * @return \m\Http\Response
     * @throws \Exception
     */
    public function passMany(array $data)
    {
        if (!isset($this->_session))
            throw new \Exception('m\Http\Response: No session object available to flash "'.$key.'" to.');

        $this->_session->flashMany($data);

        return $this;
    }

    /**
     * Returns the current status code.
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * Sets the response status code.
     *
     * @param int $code
     * @return \m\Http\Response
     */
    public function setStatus($code)
    {
        $this->_status = (integer) $code;

        return $this;
    }

    /**
     * Sets a header.
     *
     * @param string $key
     * @param mixed $value
     * @return \m\Http\Response
     */
    public function setHeader($key, $value)
    {
        $this->_headers[$key] = $value;

        return $this;
    }

    /**
     * Returns the value of the requested header or null if it does not exist.
     *
     * @param string $key
     * @return mixed|null
     */
    public function getHeader($key)
    {
        return isset($this->_headers[$key]) ? $this->_headers[$key] : null;
    }

    /**
     * Returns true if the requested header exists.
     *
     * @param string $key
     * @return bool
     */
    public function hasHeader($key)
    {
        return isset($this->_headers[$key]);
    }

    /**
     * Returns the keys of the set headers.
     *
     * @return array
     */
    public function getHeaderKeys()
    {
        return array_keys($this->_headers);
    }

    /**
     * Clears a set header.
     *
     * @param string $key
     * @return \m\Http\Response
     */
    public function clearHeader($key)
    {
        unset($this->_headers[$key]);

        return $this;
    }

    /**
     * Clears all of the set headers.
     *
     * @return \m\Http\Response
     */
    public function clearHeaders()
    {
        $this->_headers = array();

        return $this;
    }

    /**
     * Write content to the body.
     *
     * @param string $content
     * @return \m\Http\Response
     */
    public function write($content)
    {
        $this->_body = (string) $content;

        return $this;
    }

    /**
     * Append content to the existing body.
     *
     * @param string $content
     * @return \m\Http\Response
     */
    public function append($content)
    {
        $this->_body .= (string) $content;

        return $this;
    }

    /**
     * Returns the body.
     *
     * @return string
     */
    public function read()
    {
        return $this->_body;
    }

    /**
     * Automatically sets the response headers and status for
     * a redirect.
     *
     * @param string $url
     * @param int $status
     * @return \m\Http\Response
     */
    public function redirect($url, $status = 302)
    {
        $this->_headers['Location'] = (string) $url;
        $this->_status = (integer) $status;

        return $this;
    }

    /**
     * Sets the caching headers for the response.
     *
     * @param string|int|false $expires
     * @return \m\Http\Response
     */
    public function cache($expires)
    {
        if ($expires === false) {
            $this->_headers['Expires'] = 'Mon, 26 Jul 1997 05:00:00 GMT';
            $this->_headers['Cache-Control'] = array(
                'no-store, no-cache, must-revalidate',
                'post-check=0, pre-check=0',
                'max-age=0'
            );
            $this->_headers['Pragma'] = 'no-cache';
        } else {
            $expires = is_string($expires) ? strtotime($expires) : $expires;
            $this->_headers['Expires'] = gmdate('D, d M Y H:i:s', $expires).' GMT';
            $this->_headers['Cache-Control'] = 'max-age='.($expires - time());
        }
        return $this;
    }

    /**
     * Returns the status message for the given code or null
     * if a message was not found.
     *
     * @param int $code
     * @return string|null
     */
    public function getStatusMessage($code)
    {
        return isset(static::$_codes[$code]) ? static::$_codes[$code] : null;
    }

    /**
     * Returns the entire array of status messages.
     *
     * @return array
     */
    public function getStatusMessages()
    {
        return static::$_codes;
    }

    /**
     * Sends the HTTP status header.
     *
     * @param string|null $protocol
     * @return \m\Http\Response
     */
    public function sendStatus($protocol = null)
    {
        if (null === $protocol) {
            $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
        }
        header($protocol.' '.$this->_status.' '.$this->getStatusMessage($this->_status));

        return $this;
    }

    /**
     * Sends all of the set HTTP headers.
     *
     * @return \m\Http\Response
     */
    public function sendHeaders()
    {
        foreach ($this->_headers as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    header($key.': '.$v, false);
                }
            } else {
                header($key.': '.$value);
            }
        }

        return $this;
    }

    /**
     * Sends the response and ends the application.
     *
     * @param string|null $protocol
     */
    public function send($protocol = null)
    {
        // Clean up any open buffers
        if (ob_get_length() > 0) {
            ob_end_clean();
        }

        // Send the status code and headers
        if (!headers_sent())
            $this->sendStatus($protocol)->sendHeaders();

        // Send the body
        echo $this->_body;

        // Kill the script
        exit;

    }

}