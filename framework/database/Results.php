<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 *
 * @package database
 *
 * Encapsulates an array of results that implement the XML interface. Allows easy pagination and XML access
 */
class Results {
    private $results;
    private $pagination;
    private $customTag;
    private $resultsPerPage;
    private $numResults;

    /**
     * @param array $results array of {@link XML} results of a database query
     * @param int $numResults total number of results the query could have returned (without LIMIT)
     * @param int $currentResultNumber use to calculate the current page
     * @param int $resultsPerPage
     * @param string $customTag custom XML tag that encapsulates the results
     */
    public function __construct(array $results, $numResults, $currentResultNumber = 0, $resultsPerPage = 10, $customTag = "query" ) {
        $this->results = $results;
        $this->numResults = $numResults;
        $this->currentResultNumber = $currentResultNumber;
        $this->pagination = $pagination;
        $this->customTag = $customTag;
        $this->resultsPerPage = $resultsPerPage;
    }

    public function getXML() {
        $xml = "<{$this->customTag}>";
        $xml .= "<results>";

        foreach ($this->results as $record) {
            if ($record instanceof XML) {
                $xml .= $record->getXML();
            }
        }

        $xml .= "</results>";
        $xml .= "<pagination>";

        for ($i = 0; $i < $this->pagination["num"] ; $i = $i + $this->resultsPerPage) {
            $selected = ($i == $this->currentResultNumber) ? " selected='true'" : "";
            $end = ($i + $this->resultsPerPage < $this->numResults) ? $i + $this->resultsPerPage : $this->numResults;

            echo "
            <page{$selected}>
                <start>{$i}</start>
                <end>{$end}</end>
            </page>";
        }

        $xml .= "</pagination>";
        $xml .= "</{$this->customTag}>";

        return $xml;
    }
}

?>
