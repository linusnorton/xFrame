<?php
/**
 * Authorise a user against a credential and identity
 * @author Dominic Webb <dominic.webb@assertis.net>
 */
class DbAuth implements AuthenticationAdapter {

    private $authorisedId;
    private $identity;
    private $credential;
    private $authRequestDbTable;
    private $identityInstanceColumn;
    private $credentialInstanceColumn;
    private $identityKey;

    /**
     *
     * @param string $table The table we are going to query form the authorisation
     * @param string $identityColumn The returned column name that will give us the instance identity
     */
    public function __construct($table, $identityColumn, $credentialColumn, $identityKey){
        $this->authRequestDbTable = $table;
        $this->identityInstanceColumn = $identityColumn;
        $this->credentialInstanceColumn = $credentialColumn;
        $this->identityKey = $identityKey;
    }


    /**
     * Set the identity to be authenticated
     * @param string $ident
     * @return DbAuth
     */
    public function setIdentity($ident) {
        $this->identity = $ident;
        return $this;
    }


    /**
     * Return the identity value that has been set
     * @return $this->identity | false
     */
    public function getIdentity() {
        return $this->identity;
    }


    /**
     * Set the credential to be used in authentications
     * @param string $cred
     * @return DbAuth
     */
    public function setCredential($cred) {
        $this->credential = $cred;
        return $this;
    }


    /**
     * Return the credential value that has been set
     * @return $this->credential | false
     */
    public function getCredential() {
        return $this->credential;
    }


    /**
     * Perform the authorisation request
     * @return DbAuth
     */
    public function authenticate() {

        try {
            $cred = $this->getCredential();
        } catch (FrameEx $ex) {
            throw new FrameEx("A credential must be set before you can perform an authorisation request", 98);
        }

        try {
            $ident = $this->getIdentity();
        } catch (FrameEx $ex) {
            throw new FrameEx("An identity must be set before you can perform an authorisation request", 99);
        }

        try {

            $sql = "SELECT `".addslashes($this->identityKey)."` FROM `".addslashes($this->authRequestDbTable)."` WHERE `".$this->identityInstanceColumn."` = :ident AND `".$this->credentialInstanceColumn."` = :cred;";

            $stmt = DB::dbh()->prepare($sql);
            $stmt->bindValue(":ident", $this->getIdentity());
            $stmt->bindValue(":cred", $this->getCredential());
            $stmt->execute();

            $res = $stmt->fetchAll();

            if (count($res) == 1) {
                $this->authorisedId = $res[0][$this->identityKey];

            } elseif (count($res)  > 1) {

                throw new FrameEx("More than one result was returned for the identity and credential provided", 101);

            } else {

                throw new FrameEx("No match was found for the identity and credential provided", 103);
            }

            return $this;
        } catch (FrameEx $ex) {
            throw new FrameEx($ex->getMessage());
        }
    }


    /**
     * Determines wether the authorisation atempt produced a valid result
     * @return true | false
     */
    public function hasIdentity() {
        return (!$this->authorisedId) ? false : true;
    }

    public function getAuthIdentity() {
        return $this->authorisedId;
    }

    public function persistAuthIdentity ($namespace) {
        if (!array_key_exists($namespace, $_SESSION)) {
            $_SESSION[$namespace] = $this->getAuthIdentity();
        } else {
            throw new FrameEx("Authenticated Id is already set. Force a manual overwrite to set a new value (bad if you need to do this tho)", 104);
        }
    }

    public static function createCustomerAuthRecord(Customer $customer, $table, array $identity, array $credential, $idKey) {
        try {
            $stmt  = DB::dbh()->prepare("INSERT INTO `".addslashes($table)."` (:id, :identity, :credential) VALUES (:idV, :identityV, :credentialV)");
            $stmt->bindValue(":id", $idKey);
            $stmt->bindValue(":credential", $credential['column']);
            $stmt->bindValue(":identity", $identity['column']);

            $stmt->bindValue(":idV", $customer->id);
            $stmt->bindValue(":credentialV", $credential['value']);
            $stmt->bindValue(":identityV", $identity['value']);
            $stmt->execute();
        } catch (FrameEx $ex) {
            throw new FrameEx("CREATE_AUTH_RECORD_FAILED");
        }
    }
}
