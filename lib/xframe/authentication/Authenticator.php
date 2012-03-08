<?php
namespace xframe\authentication;

interface Authenticator {

    /**
     * @param string $identity
     * @param string $credential
     * @return \xframe\authentication\Result
     */
    public function authenticate($identity, $credential);

}

