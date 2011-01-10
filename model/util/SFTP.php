<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 * @package util
 *
 * This class provides SFTP connectivity
 */

class SFTP {
    private $connection;
    private $host;
    private $port;
    private $username;
    private $password;

    public function __construct($username,
                                $password,
                                $host,
                                $port = 22) {
        $this->username = $username;
        $this->password = $password;
        $this->host = $host;
        $this->port = $port;
    }

    public function connect($retries = 1, $retryTimeout = 10) {
        $attepmts = 1;
        
        $this->connection = @ssh2_connect($this->host, $this->port);
        
        //if could not connect try again in 30
        while (!$this->connection && $attepmts < $retries) {
            sleep($retryTimeout);
            $this->connection = @ssh2_connect($this->host, $this->port);
            $attepmts++;
        }

        if (!$this->connection) {
            throw new FrameEx("Could not connect to {$this->host} on port {$this->port}");
        }

        if (!@ssh2_auth_password($this->connection, $this->username, $this->password)) {
            throw new FrameEx("Invalid username or password");
        }
    }

    public function put($localFilename, $remoteFilename, $retries = 1) {
        if (!@ssh2_scp_send($this->connection, $localFilename, $remoteFilename)) {
            throw new FrameEx("Failed to put {$localFilename}");
        }
    }

    public function get($remoteFilename, $localFilename, $retries = 1) {
        if (!@ssh2_scp_recv($this->connection, $remoteFilename, $localFilename)) {
            throw new FrameEx("Failed to get {$remoteFilename}");
        }

    }

}