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

    const ENC_SHA1 = "SHA1";
    const ENC_MD5 = "MD5";

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
     * Set the identity that is to be authenticated
     * @param string $ident The identity e.g. email, username
     * @return DbAuth
     */
    public function setIdentity($ident, array $validator=null) {


        if (is_array($validator)) {

            if (isset($validator['params'])) {
                $params[0] = $ident;
                while (list($k, $v) = each($validator['params'])) {
                    $params[] = $v;
                }
            } else {
                $params = $ident;
            }

            if (call_user_func_array(array($validator[0], $validator[1]), $params)) {
                $this->identity = $ident;
                return $this;
            } else {
                throw new FrameEx("Identity failed validation", IDENTITY_FAILED_VALIDATION);
            }

        } else {
            $this->identity = $ident;
            return $this;
        }


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
     * @param string $cred The credential e.g. password or token or key code
     * @param $string $enc Optional encryption scheme. Support md5() and sha1()
     * @return DbAuth
     */
    public function setCredential($cred, $enc=null) {

        if (empty($cred)) {
            throw new FrameEx("Credential cannot be an empty string", CREDENTIAL_EMPTY);
        }

        if ($enc == DbAuth::ENC_SHA1) {
            $this->credential = sha1($cred);
        } elseif ($enc == DbAuth::ENC_MD5) {
            $this->credential = md5($cred);
        } else {
            $this->credential = $cred;
        }
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
            throw new FrameEx("A credential must be set before you can perform an authorisation request", CREDENTIAL_NOT_SET);
        }

        try {
            $ident = $this->getIdentity();
        } catch (FrameEx $ex) {
            throw new FrameEx("An identity must be set before you can perform an authorisation request", IDENTITY_NOT_SET);
        }

        try {

            $sql = "SELECT `".addslashes($this->identityKey)."`, `".addslashes($this->identityInstanceColumn)."` AS identity, `".addslashes($this->credentialInstanceColumn)."` AS credential FROM `".addslashes($this->authRequestDbTable)."` WHERE `".$this->identityInstanceColumn."` = :ident;";

            $stmt = DB::dbh()->prepare($sql);
            $stmt->bindValue(":ident", $this->getIdentity());
            $stmt->execute();

            $res = $stmt->fetchAll();

            if (count($res) == 1) {
                if ($res[0]["credential"] == $this->getCredential()) {
                    $this->authorisedId = $res[0][$this->identityKey];
                } else {

                    throw new FrameEx("The credential supplied does not match the credential for the authenticated identity", CREDENTIAL_NOT_MATCH);
                }


            } elseif (count($res)  > 1) {

                throw new FrameEx("More than one result was returned for the identity and credential provided", IDENTITY_RESULTS_AMBIGUOUS);

            } else {

                throw new FrameEx("No match was found for the identity and credential provided", IDENTITY_NO_RESULTS);
            }

            return $this;
        } catch (FrameEx $ex) {
            throw new FrameEx($ex->getMessage(), $ex->getCode());
        }
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
        if (!array_key_exists($namespace, $_SESSION)) {
            $_SESSION[$namespace] = $this->getAuthIdentity();
        } else {
            throw new FrameEx("Authenticated Id is already set. Force a manual overwrite to set a new value (bad if you need to do this tho)", IDENTITY_ALREADY_SET);
        }
    }

}