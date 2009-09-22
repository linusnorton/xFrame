<?php
/**
 * Match error codes thrown by exceptions to an alternate description.
 *
 * An optional trigger will be returned. Usefull for indicating that something
 * should be triggered by this alternate description.
 *
 * @author Dominic Webb <dominic.webb@assertis.co.uk>
 */
class ErrorCodes {

    /**
     * Get the alternate description for an error code
     * @param integer $code The Exception code that was thrown
     * @param resource  The resource type for doing the code lookup e.g. database, xml file or csv file. Defaults to database
     * @return array $result
     *
     * @todo add code for parsing an XML and CSV file containing the code to alt
     *  descriptions
     */
    public static function getDesc ($code, $resource="db") {

        if ($resource == "db") {
            $sql = "SELECT `desc`, `trigger` FROM error_codes WHERE `code` = :code LIMIT 1;";
            $stmt = DB::dbh()->prepare($sql);
            $stmt->bindValue(":code", $code);
            $stmt->execute();
            $result = $stmt->fetchAll();
            return $result[0];

        } elseif ($resource == "xml") {
            return null;
        } elseif ($resource == "csv") {
            return null;
        }
    }
}
?>
