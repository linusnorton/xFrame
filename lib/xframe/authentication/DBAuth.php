<?php

namespace xframe\authentication;

/**
 * Uses a PDO connect to auth given credentials
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package authentication
 */
class DBAuth implements Authenticator {
    private $pdo;
    private $table;
    private $identityColumn;
    private $credentialColumn;
    
    /**
     * @param PDO $pdo
     * @param string $table
     * @param string $identityColumn
     * @param string $credentialColumn 
     */
    function __construct(\PDO $pdo, $table, $identityColumn, $credentialColumn) {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->identityColumn = $identityColumn;
        $this->credentialColumn = $credentialColumn;
    }

    /**
     * @param string $identity
     * @param string $credential
     * @return boolean
     */
    public function authenticate($identity, $credential) {
        $stmt = $pdo->prepare(
            "SELECT 1 FROM `{$this->table}`
             WHERE `{$this->identityColumn}` = :identity
             AND `{$this->credentialColumn}` = :credential
             LIMIT 1"
        );
                               
        $stmt->bindValue("identity", $identity);
        $stmt->bindValue("credential", $credential);
        $stmt->execute();
        
        return $stmt->numResults() > 0;
    }
}

