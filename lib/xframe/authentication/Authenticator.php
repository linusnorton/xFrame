<?php
namespace xframe\authentication;

interface Authenticator {

    /**
     * @param string $identity
     * @param string $credential
     */
    public function authenticate($identity, $credential);
    
    /**
     * @return \xframe\authentication\Result
     */
    public function getResult();

}

