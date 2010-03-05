<?php
/**
 * DefaultRecordTransformer
 *
 * @author Linus Norton <linus.norton@assertis.co.uk>
 */
class DefaultRecordTransformer implements XMLTransformer {

    /**
     * Turn a record into a string
     * @param Record $object
     * @return string
     */
    public function getXML(Transformable $object) {
        $xml = "\n<record table='{$object->getTableName()}' id='{$object->getId()}'>";

        foreach ($object->getAttributes() as $key => $value) {
            // Need this to make sure the magic getter is called for lazy loading
            $value = $object->$key;
            $xml .= "\n\t<{$key}>";

            if ($value instanceof XML) {
                $xml .= $value->getXML();
            }
            else if (is_bool($value)) {
                $xml .= ($value) ? "true" : "false";
            }
            else if (is_array($value)) {
                $xml .= ArrayUtil::getXML($value);
            }
            else {
                $xml .= htmlspecialchars($value, ENT_COMPAT, "UTF-8", false);
            }

            $xml .= "</{$key}>";
        }

        $xml .= "\n</record>";

        return $xml;
    }
}
