<?php
/**
 * Implements an authentication interface to allow validation of an identity and
 * an optional credential
 * @author Dominic Webb
 */
interface AuthenticationAdapter {


    public function authenticate();

    public function getAuthIdentity();

    public function persistAuthIdentity($namespace);

    public function hasIdentity();

    public function setIdentity($ident);

    public function getIdentity();

}
