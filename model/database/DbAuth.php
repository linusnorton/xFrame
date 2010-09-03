<?php
/**
 * Authorise a user against a credential and identity
 * @author Dominic Webb <dominic.webb@assertis.net>, Linus Norton <linusnorton@gmail.com>
 */
class DbAuth implements AuthenticationAdapter {
    private $authorisedId;
    private $identity;
    private $credential;
    private $table;
    private $identityColumn;
    private $credentialColumn;
    private $identityKey;

    /**
     *
     * @param string $table The table we are going to query form the authorisation
     * @param string $identityColumn The returned column name that will give us the instance identity
     * @param string $credentialColumn
     * @param string $identityKey
     */
    public function __construct($table,
                                $identityColumn,
                                $credentialColumn,
                                $identityKey){
        $this->table = $table;
        $this->identityColumn = $identityColumn;
        $this->credentialColumn = $credentialColumn;
        $this->identityKey = $identityKey;
    }


    /**
     * Set the identity that is to be authenticated
     * @param string $identity The identity e.g. email, username
     * @return DbAuth
     */
    public function setIdentity($identity) {
        $this->identity = $identity;
        return $this;
    }


    /**
     * Return the identity value that has been set
     * @return string
     */
    public function getIdentity() {
        return $this->identity;
    }

    /**
     * Set the credential to be used in authentications
     * @param string $cred The credential e.g. password or token or key code
     * @return DbAuth
     */
    public function setCredential($credential) {
        $this->credential = sha1($credential);
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
        $credential = $this->getCredential();
        Assert::isNotEmpty($credential, "You must set a password before authentication");

        $identity = $this->getIdentity();
        Assert::isNotEmpty($identity, "You must set an username before authentication");

        $criteria = new Criteria(Restriction::is($this->credentialColumn, $credential));
        $criteria->addAnd(Restriction::is($this->identityColumn, $identity));

        $records = TableGateway::loadMatching($this->table, $criteria);

        if ($records->count() == 0) {
            return false;
        }

        return $this->authorisedId = $records->current()->__get($this->identityKey);
    }


    /**
     * Determines whether the authorisation atempt produced a valid result
     * @return boolean
     */
    public function hasIdentity() {
        return (!$this->authorisedId) ? false : true;
    }

    /**
     * @return mixed
     */
    public function getAuthIdentity() {
        return $this->authorisedId;
    }

    /**
     * @param string $namespace
     */
    public function persistAuthIdentity ($namespace) {
        $_SESSION[$namespace] = $this->getAuthIdentity();
    }

}