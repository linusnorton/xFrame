<?php

/**
 * Simulated database sequences (like in Oracle or PostgreSQL) for MySQL. This
 * is based on a class by Daniel Dyer.
 * @package util
 * @author Linus Norton <linusnorton@gmail.com>
 */
class Sequence {
    protected $tableName;

    public function __construct($tableName) {
        $this->tableName = $tableName;
    }

    /**
     * Increments a specific sequence and returns the new value.
     */
    public function next($sequenceName) {
        // Sequence increments until it hits its pre-configured maximum value and then it
        // wraps back round to zero.
        $stmt = DB::dbh()->prepare('UPDATE `'.addslashes($this->tableName).'` '
                                   .'SET `last_value`=@val:=IF(`last_value` = `max_value`, 0, `last_value` + 1) '
                                   .'WHERE `name` = :name;');
        $stmt->bindValue(':name', $sequenceName);
        $success = $stmt->execute();
        if (!$success) {
            throw new FrameEx('Unknown sequence: '.$sequenceName);
        }

        $stmt = DB::dbh()->prepare('SELECT @val;');
        $stmt->execute();
        $value = $stmt->fetchColumn();
        if ($value === false) {
            throw new FrameEx('Unknown sequence: '.$sequenceName);
        }
        return $value;
    }


    /**
     * Find out the next value of the sequence without incrementing it.
     * @return integer
     */
    public function peekNextValue($sequenceName) {
        // Sequence increments until it hits its pre-configured maximum value and then it
        // wraps back round to zero.
        $stmt = DB::dbh()->prepare('SELECT `last_value`, `max_value` FROM `'.addslashes($this->tableName).'` '
                                   .'WHERE `name` = :name;');
        $stmt->bindValue(':name', $sequenceName);
        $stmt->execute();
        $values = $stmt->fetch();
        if ($values === false) {
            throw new FrameEx('Unknown sequence: '.$sequenceName);
        }
        return $values[0] == $values[1] ? 0 : $values[0] + 1;
    }


    /**
     * Returns the most recently used value of a given sequence (does not increment the
     * sequence).
     * @return integer
     */
    public function getLastValue($sequenceName) {
        $stmt = DB::dbh()->prepare('SELECT `last_value` FROM `'.addslashes($this->tableName).'` '
                                   .'WHERE `name` = :name;');

        $stmt->bindValue(':name', $sequenceName);
        $stmt->execute();
        $value = $stmt->fetchColumn();
        if ($value === false) {
            throw new FrameEx('Unknown sequence: '.$sequenceName);
        }
        return $value;
    }

    /**
     * The maximum possible value for a given sequence.
     * @return integer
     */
    public function getMaxValue($sequenceName) {
        $stmt = DB::dbh()->prepare('SELECT `max_value` FROM `sequence` '
                                   .'WHERE `name` = :name;');

        $stmt->bindValue(':name', $sequenceName);
        $stmt->execute();
        $value = $stmt->fetchColumn();
        if ($value === false) {
            throw new FrameEx('Unknown sequence: '.$sequenceName);
        }
        return $value;
    }

    /**
     * Add sequence to the table
     * @param string $sequenceName
     * @param int $startingValue
     * @param int $maxValue
     */
    public function create($sequenceName, $startingValue = 1, $maxValue = 4294967295) {
        $stmt = DB::dbh()->prepare('INSERT INTO `'.addslashes($this->tableName).'` (`name`, `last_value`, `max_value`) '
                                   .'VALUES (:name, :last_value, :max_value);');
        $stmt->bindValue(':name', $sequenceName);
        $stmt->bindValue(':last_value', $startingValue == 0 ? $maxValue : $startingValue - 1);
        $stmt->bindValue(':max_value', $maxValue);
        $success = $stmt->execute();
        if (!$success) {
            throw new FrameEx('Unable to create sequence: '.$sequenceName);
        }
    }

    /**
     * Drop the sequence
     * @param string $sequenceName
     */
    public function drop($sequenceName) {
        $stmt = DB::dbh()->prepare('DELETE FROM `'.addslashes($this->tableName).'` '
                                   .'WHERE `name` = :name;');

        $stmt->bindValue(':name', $sequenceName);
        $success = $stmt->execute();
        if (!$success) {
            throw new FrameEx('Unable to remove sequence: '.$sequenceName);
        }
    }

    /**
     * Static constructor to enable a slightly shorter use. Rather than
     *
     * $sequence = new Sequence("sequence");
     * $nextValue = $sequence->next("mySequence);
     *
     * Becomes
     *
     * $nextValue = Sequence::get("sequence")->next("mySequence");
     *
     *
     * @param string $tableName
     * @return Sequence
     */
    public static function get($tableName) {
        return new Sequence($tableName);
    }
}
