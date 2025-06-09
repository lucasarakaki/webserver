<?php

namespace App\Class;

class Request
{
    /**
     * Request method
     *
     * @var string
     */
    protected $method = null;
    /**
     * Request uri
     *
     * @var string
     */
    protected $uri = null;
    /**
     * Request parameters
     *
     * @var array
     */
    protected $parameters = [];
    /**
     * Request headers
     *
     * @var array
     */
    protected $headers = [];

    /**
     * @param mixed $method
     * @param mixed $uri
     * @param mixed $headers
     */
    public function __construct( $method, $uri, $headers )
    {
        $this->headers = $headers;
        $this->method  = strtoupper( $method );

        @list( $this->uri, $params ) = explode( '?', $uri );

        parse_str( $params, $this->parameters );
    }

    /**
     * @param  mixed     $header
     * @return Request
     */
    public static function withHeaderString( $header ): Request
    {
        $lines = explode( "\n", $header );

        list( $method, $uri ) = explode( ' ', array_shift( $lines ) );

        $headers = [];

        foreach ( $lines as $line ) {
            $line = trim( $line );

            if ( strpos( $line, ': ' ) !== false ) {
                list( $key, $value ) = explode( ': ', $line );

                $headers[$key] = $value;
            }
        }

        // Create new Request object
        return new static( $method, $uri, $headers );
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @param  mixed   $key
     * @param  null    $default
     * @return mixed
     */
    public function getHeaders( $key, $default = null ): mixed
    {
        if (  ! isset( $this->headers[$key] ) ) {
            return $default;
        }

        return $this->headers[$key];
    }

    /**
     * @param  mixed   $key
     * @param  null    $default
     * @return mixed
     */
    public function getParams( $key, $default = null ): mixed
    {
        if (  ! isset( $this->parameters[$key] ) ) {
            return $default;
        }

        return $this->parameters[$key];
    }
}
