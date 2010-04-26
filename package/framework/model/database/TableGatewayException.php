<?php

/**
 * TableGatewayException is derived from a PDOException.
 *
 * @author Linus Norton <linusnorton@gmail.com>
 */
class TableGatewayException extends FrameEx {

    /**
     * Original PDOException is transformed into a FrameEx
     * @param PDOException $ex
     */
    public function __construct(PDOException $ex, $severity) {
        parent::__construct($ex->getMessage(), $ex->getCode(), $severity, $ex);
    }
}

