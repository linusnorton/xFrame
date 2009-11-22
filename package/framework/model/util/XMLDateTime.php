<?php
/**
 * @author Linus Norton <linusnorton@gmail.com>
 * @package util
 *
 * This class encapsulates a date time and provides functions for comparison etc
 */
class XMLDateTime extends DateTime implements XML {

    /**
     * Check to see if the given time is before this time
     * @param DateTime $t time to compare
     */
    public function before(DateTime $t) {
        return $this->getTimestamp() < $t->getTimestamp();
    }

    /**
     * Check to see if the given time is after this time
     * @param DateTime $t time to compare
     */
    public function after(DateTime $t) {
        return $this->getTimestamp() > $t->getTimestamp();
    }

    /**
     * Calculate the number of days difference between two timestamps.  If they represent
     * different times on the same day, the difference is zero.
     * @param DateTime $other The timestamp to compare against this object.
     * @return integer
     */
    public function diffDays(DateTime $other) {

        if ($this->before($other)) {
            $firstDate = getdate($this->getTimestamp());
            $secondDate = getdate($other->getTimestamp());
        }
        else {
            $firstDate = getdate($other->getTimestamp());
            $secondDate = getdate($this->getTimestamp());
        }

        if ($firstDate['year'] != $secondDate['year']) {
            $lastDayOfYear = getdate(mktime(0, 0, 0, 12, 31, $firstDate['year']));
            // Days from first date to the end of that year.
            $days = $lastDayOfYear['yday'] - $firstDate['yday'];

            for ($year = $firstDate['year'] + 1; $year < $secondDate['year']; $year++) {
                $leapYear = date('L', $year.'01-01');
                $days += $leapYear ? 365 : 366;
            }
            // Days from start of year until second date.
            $days += $secondDate['yday'] + 1;
            return $days;
        }
        else {
            return $secondDate['yday'] - $firstDate['yday'];
        }
    }

    /**
     * @return integer The day of the month (1-31).
     */
    public function getDayOfMonth() {
        return $this->format("j");
    }

    /**
     * @return integer The month of the year (1-12).
     */
    public function getMonthOfYear() {
        return $this->format("n");
    }


    /**
     * @return XMLDateTime note it will have a timezone of UTC
     */
    public function diff(DateTime $datetime) {
        $date = new XMLDateTime("now", new DateTimeZone("UTC"));
        $date->setTimestamp($this->getTimestamp() - $datetime->getTimestamp());
        return $date;
    }

    /**
     * Return a new timestamp that is generated using the offset from this timeset
     *
     * @param mixed $date
     * @return XMLDateTime
     */
    public function offset($offset) {
        $date = new XMLDateTime($this->format("Y-m-d H:i:s"), $this->getTimeZone());
        $date->modify($offset);
        return $date;
    }

    /**
     * @param Timestamp $endDate
     * @return int
     */
    public function getMonthDiff(DateTime $endDate) {

        $fromYear = $this->format("Y");
        $fromMonth = $this->format("n");

        $toYear = $endDate->format("Y");
        $toMonth = $endDate->format("n");

        // -1 as august to sept is not 1 month diff (it's x days diff)
        return ($fromYear < $toYear)?(($toMonth + 12) - $fromMonth) - 1:($toMonth - $fromMonth) - 1;
    }

    /**
     * @param Timestamp $endDate
     * @return int
     */
    public function getDayDiff(DateTime $endDate) {

        $fromMonth = $this->format("n");
        $fromDay = $this->format("j");
        $daysInMonth = $this->format("t");

        $toMonth = $endDate->format("n");
        $toDay = $endDate->format("j");

        // +1 as the dates are inclusive
        return ($daysInMonth - $fromDay) + $toDay + 1;
    }


    /**
     * @param array $formatString array of characters to be used by the date function and inserted as attributes
     */
    public function getXML(array $formatString = array(), $tagName = "date") {
        if (empty($formatString)) {
            $formatString = array("Y","m","d","H","i","s","j","S","F");
        }

        $xml = "<".$tagName;
        foreach ($formatString as $formatChar) {
            $xml .= " {$formatChar}='".$this->format($formatChar)."'";
        }
        $xml .= ">".$this."</".$tagName.">";

        return $xml;
    }

    /**
     * Default method of output: MySQL datetime
     *
     **/
    public function __toString() {
        return $this->format("Y-m-d H:i:s");
    }

}
?>