<?php
/**
 * DefaultResultsTransformer turrns the Results object into XML
 *
 * @author Linus Norton <linus.norton@assertis.co.uk>
 */
class DefaultResultsTransformer implements XMLTransformer {

    /**
     * This method returns the results and pagination in an XML string
     * @return string $xml
     */
    public function getXML(Transformable $object) {
        $xml = "<{$object->getCustomTag()}>";
        $xml .= "<results>";

        //add the xml of the results
        foreach ($object->getResults() as $record) {
            if ($record instanceof XML) {
                $xml .= $record->getXML();
            }
        }

        $xml .= "</results>";
        $xml .= $this->getPaginationXML($object);
        $xml .= "<maxNumResults>{$object->getMaxNumResults()}</maxNumResults>" ;
        $xml .= "<numResults>{$object->getNumResults()}</numResults>" ;
        $xml .= "<currentResultNumber>{$object->getCurrentResultNumber()}</currentResultNumber>" ;
        $xml .= "<resultsPerPage>{$object->getResultsPerPage()}</resultsPerPage>" ;
        $xml .= "</{$object->getCustomTag()}>";

        return $xml;
    }

    /**
     * Get the pagination, in the future it would be nice to have some additional
     * pagination methods
     * @param TraversableObject $object
     * @return string
     */
    private function getPaginationXML(Transformable $object) {
        $xml = "";
        if (ctype_digit("{$object->getResultsPerPage()}")) {
            $xml .= "<pagination>";

            for ($i = 0; $i < $object->getMaxNumResults() ; $i += $object->getResultsPerPage()) {
                $selected = ($i == $object->getCurrentResultNumber()) ? " selected='true'" : "";
                $end = ($i + $object->getResultsPerPage() < $object->getMaxNumResults()) ? $i + $object->getResultsPerPage() : $object->getMaxNumResults();

                $xml .= "
                <page{$selected}>
                    <start>{$i}</start>
                    <end>{$end}</end>
                </page>";
            }

            $xml .= "</pagination>";
        }

        return $xml;

    }

}
