<?php

namespace xframe\authentication;

/**
 * An order has customer details and several order items
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package authentication
 */
interface Authenticator {
    
    /**
     * @return boolean
     */
    public function authenticate($identity, $credential);
}

