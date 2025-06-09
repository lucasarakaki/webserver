<?php

namespace App\Class;

use App\Class\Request;
use App\Class\Response;
use App\Class\ExceptionServer;

class Server
{
    /**
     * Server host
     *
     * @var string
     */
    protected $host = null;
    /**
     * Server port
     *
     * @var int
     */
    protected $port = null;
    /**
     * Server socket
     *
     * @var resource|\Socket|false
     */
    protected $socket = null;

    /**
     * @param mixed $host
     * @param mixed $port
     */
    public function __construct( $host, $port )
    {
        $this->host = $host;
        $this->port = (int) $port;

        $this->createSocket();
        $this->bind();
    }

    /**
     * @return resource|\Socket|false
     */
    protected function createSocket(): void
    {
        $this->socket = socket_create( AF_INET, SOCK_STREAM, 0 );
    }

    /**
     * @return mixed
     */
    protected function bind(): void
    {
        if (  ! socket_bind( $this->socket, $this->host, $this->port ) ) {
            throw new ExceptionServer( 'Could not bind: '.$this->host.':'.$this->port.' - '.socket_strerror( socket_last_error() ) );
        }
    }

    /**
     * @param  mixed  $callback
     * @return void
     */
    public function listen( $callback ): void
    {
        // check if the callback is valid. Throw an exception
        // if not.
        if (  ! is_callable( $callback ) ) {
            throw new ExceptionServer( 'The given argument should be callable.' );
        }

        // Now here comes the thing that makes this process
        // long, infinite, never ending..
        while ( 1 ) {
            // listen for connections
            socket_listen( $this->socket );

            // try to get the client socket resource
            // if false we got an error close the connection and skip
            if (  ! $client = socket_accept( $this->socket ) ) {
                socket_close( $client );
                continue;
            }

            // create new request instance with the clients header.
            // In the real world of course you cannot just fix the max size to 1024..
            $request = Request::withHeaderString( socket_read( $client, 1024 ) );

            // execute the callback
            $response = call_user_func( $callback, $request );

            // check if we really recived an Response object
            // if not return a 404 response object
            if (  ! $response || ! $response instanceof Response ) {
                $response = Response::error( 404 );
            }

            // make a string out of our response
            $response = (string) $response;

            // write the response to the client socket
            socket_write( $client, $response, strlen( $response ) );

            // close the connetion so we can accept new ones
            socket_close( $client );
        }
    }

}
