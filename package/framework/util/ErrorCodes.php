<?php
/**
 * Description of ErrorCodes
 *
 * @author Dominic Webb <dominic@rewrite3.com>
 */
class ErrorCodes {
    
    public static function getDesc ($code) {

            $sql = "SELECT `desc` FROM error_codes WHERE `code` = :code ;";
            $stmt = DB::dbh()->prepare($sql);
            $stmt->bindValue(":code", $code);
            $stmt->execute();

            $res = $stmt->fetchAll();

            return $res[0]['desc'];

    }
}
?>
