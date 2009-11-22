<?php
/**
 * @author Jason Paige <j@jasonpaige.co.uk>
 * @package util
 * This object encapsulates the field values for a form and the respective error messages
 */

class Form implements XML {
    private $field;
    private $hasErrors;

    /**
     * Populates the fields for the form
     *
     * @param Request $request
     */
    public function __construct(Request $request = null) {
        if ($request != null) {
            foreach ($request->getParams() as $id => $parameter) {
                $this->add($id, $parameter);
            }
            $this->hasErrors = false;
        }
    }

    /**
     * Adds a field to the form with optional error message
     *
     * @param mixed $request
     * @param mixed $value
     * @param string $errorMessage
     */
    public function add($id, $value, $errorMessage = null) {
        $this->field[$id] = array("value" => $value, "error" => $errorMessage);
        if ($errorMessage != null) {
            $this->hasErrors = true;
        }
    }

    /**
     * Returns whether this form currently contains any errors
     *
     * @return boolean
     */
    public function hasErrors() {
        return $this->hasErrors;
    }
/*
    public function doCurlPostBack($location) {
        $qString = "";
        foreach ($this->field as $id => $field) {
            $qString .= "field[{$id}]=".urlencode($field['value'])."&";
            if ($field['errorMessage'] != '') {
                $qString .= "error[{$id}]=".urlencode($field['errorMessage'])."&";
            }
        }
        $ch = curl_init($location);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $qString);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
        curl_exec($ch);
        curl_close($ch);
        die();
    }
*/


    /**
     * Returns to a location complete with field values and errors in the query string
     *
     * @param string $location
     */
    public function doGetPostBack($location) {
        $qString = "";
        foreach ($this->field as $id => $field) {
            $qString .= "field[{$id}]=".urlencode($field['value'])."&";
            if ($field['errorMessage'] != '') {
                $qString .= "error[{$id}]=".urlencode($field['errorMessage'])."&";
            }
        }
        header("Location: {$location}?{$qString}");
        die();
    }

    /**
     * Returns to a location complete with field values and errors in the session
     *
     * @param string $location
     */
    public function doSessionPostBack($location) {
        $_SESSION['field'] = array();
        $_SESSION['error'] = array();
        foreach ($this->field as $id => $field) {
            $_SESSION['field'][$id] = $field['value'];
            if ($field['error'] != '') {
                $_SESSION['error'][$id] = $field['error'];
            }
        }

        header("Location: {$location}");
        die();
    }

    public function getXML($defaultValues = array()) {
        $xml = "<form>";
        if (is_array($_SESSION["field"])) {
            foreach ($_SESSION["field"] as $fieldName => $fieldValue) {
                $xml .= $this->getFieldXML($fieldName, $fieldValue, $defaultValues[$fieldName]);
            }
        }
        if (is_array($defaultValues)) {
            foreach ($defaultValues as $fieldName => $fieldValue) {
                if (!isset($_SESSION['field'][$fieldName])) {
                    $xml .= $this->getFieldXML($fieldName, $fieldValue);
                }
            }
        }
        if (is_array($_SESSION["error"])) {
            foreach ($_SESSION["error"] as $errorId => $errorMessage) {
                $xml .= $this->getFieldXML($errorId, $errorMessage, null, "e");
            }
        }
        $xml .= "</form>";

        return $xml;
    }

    /**
     * @param string $fieldName
     * @param mixed $fieldValue
     * @param mixed $defaultValue
     * @param string $prefix because field names may be invalid (start with a number) we prefix with a valid character
     */
    private function getFieldXML($fieldName, $fieldValue, $defaultValue = "", $prefix = "f") {
        $xml = "<{$prefix}-{$fieldName}>";
        if (is_array($fieldValue)) {
            foreach ($fieldValue as $key => $value) {
                $xml .= $this->getFieldXML($key, $value);
            }
        }
        else if (isset($fieldValue)) {
            $xml .= htmlentities($fieldValue);
        }
        else if (is_array($defaultValue)) {
            foreach ($defaultValue as $key => $value) {
                $xml .= $this->getFieldXML($key, $value);
            }
        }
        else if ($defaultValue != "") {
            $xml .= htmlentities($defaultValue);
        }
        $xml .= "</{$prefix}-{$fieldName}>";

        return $xml;
    }

    public function __toString() {
        return serialize($this);
    }
}