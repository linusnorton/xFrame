<?php
/**
 * Simple fixed-length bit string that makes it easy to manipulate binary data.
 * Uses a space-efficient packed integer array internally.
 * @author Daniel Dyer
 */
class BitString {

    private $length;
    private $wordSize;

    /**
     * Store the bits packed in an array of ints (word size depends on the system -
     * 32-bit or 64-bit).
     */
    private $data;


    /**
     * Creates a bit string of the specified length with all bits
     * initially set to zero (off).
     * @param integer $length The number of bits.
     */
    public function __construct($length) {
        if ($length < 0) {
            throw new FrameEx('Length must be non-negative.');
        }
        $this->length = $length;
        $this->wordSize = PHP_INT_SIZE * 8;
        $this->data = array_fill(0, ceil($length / $this->wordSize), 0);
    }


    /**
     * @return integer The length of this bit string.
     */
    public function getLength() {
        return $this->length;
    }


    /**
     * Returns the bit at the specified index.
     * @param integer $index The index of the bit to look-up (0 is the least-significant bit).
     * @return $boolean A boolean indicating whether the bit is set or not.
     */
    public function getBit($index) {
        $this->assertValidIndex($index);
        $word = floor($index / $this->wordSize);
        $offset = $index % $this->wordSize;
        return ($this->data[$word] & (1 << $offset)) != 0;
    }


    /**
     * Sets the bit at the specified index.
     * @param integer $index The index of the bit to set (0 is the least-significant bit).
     * @param boolean $set A boolean indicating whether the bit should be set or not.
     */
    public function setBit($index, $set) {
        $this->assertValidIndex($index);
        $word = floor($index / $this->wordSize);
        $offset = $index % $this->wordSize;
        if ($set) {
            $this->data[$word] = ($this->data[$word] | (1 << $offset));
        }
        else { // Unset the bit.
            $this->data[$word] = ($this->data[$word] & ~(1 << $offset));
        }
    }

    /**
     * Sets the values of multiple bits, starting at the specified index.
     * @param integer $startIndex The index of the first bit to be set.
     * @param string $bits A string of ones and zeros.
     */
    public function setBitsHighToLow($startIndex, $bits) {
        $chars = str_split($bits);
        $index = $startIndex;
        foreach ($chars as $char) {
            $this->setBit($index, $char == '1');
            $index--;
        }
    }


    /**
     * Sets the values of multiple bits, starting at the specified index.
     * @param integer $startIndex The index of the first bit to be set.
     * @param string $bits A string of ones and zeros.
     */
    public function setBitsLowToHigh($startIndex, $bits) {
        $chars = str_split($bits);
        $index = $startIndex;
        foreach ($chars as $char) {
            $this->setBit($index, $char == '1');
            $index++;
        }
    }


    /**
     * Returned an unsigned 8-bit value (0-255) formed by taking eight
     * bits starting at the specified most significant bit.
     */
    public function getByte($mostSignificantBit) {
        $byte = 0;
        for ($i = 0; $i < 8; $i++) {
            $byte += ($this->getBit($mostSignificantBit - $i) << (7 - $i));
        }
        return $byte;
    }


    public function getBytes($mostSignificantBit, $count) {
        $bytes = array();
        for ($i = 0; $i < $count; $i++) {
            $bytes[] = $this->getByte($mostSignificantBit - (8 * $i));
        }
        return $bytes;
    }


    /**
     * Inverts the value of the bit at the specified index.
     * @param integer $index The bit to flip (0 is the least-significant bit).
     */
    public function flipBit($index) {
        $this->assertValidIndex($index);
        $word = floor($index / $this->wordSize);
        $offset = $index % $this->wordSize;
        $this->data[$word] = ($this->data[$word] ^ (1 << $offset));
    }


    /**
     * Helper method to check whether a bit index is valid or not.
     * @param integer $index The index to check.
     */
    private function assertValidIndex($index) {
        if ($index >= $this->length || $index < 0) {
            throw new FrameEx('Invalid index: '.$index.' (length: '.$this->length.')');
        }
    }


    /**
     * The data in the bit string is treated as big-endian.  The first character of the hex
     * string represents the four most significant bits.
     */
    public function toHexString() {
        $wordCount = sizeof($this->data);
        $length = $this->length;
        $hex = '';
        for ($word = 0; $word < $wordCount; $word++) {
            for ($byte = 0; $byte < PHP_INT_SIZE && $length > 0; $byte++) {
                $hexChars = dechex(($this->data[$word] >> $byte * 8) & 0xFF);
                $hexChars = str_pad($hexChars, 2, '0', STR_PAD_LEFT);
                $hex = $hexChars.$hex;
                $length -= 8;
            }
        }
        return $hex;
    }


    /**
     * Creates a textual representation of this bit string in big-endian
     * order (index 0 is the right-most bit).
     * @return string This bit string rendered as a String of 1s and 0s.
     */
    public function toBinaryString() {
        $string = '';
        for ($i = $this->length - 1; $i >= 0; $i--) {
            $string .= $this->getBit($i) ? '1' : '0';
        }
        return $string;
    }


    /**
     * Converts this bit string into a packed character string (each character
     * stores 8 bits of data).
     * The data in the bit string is treated as big-endian.  The first character of the returned
     * string represents the eight most significant bits. 
     */
    public function __toString() {
        $wordCount = sizeof($this->data);
        $length = $this->length;
        $string = '';
        for ($word = 0; $word < $wordCount; $word++) {
            for ($byte = 0; $byte < PHP_INT_SIZE && $length > 0; $byte++) {
                $string = chr(($this->data[$word] >> $byte * 8) & 0xFF).$string;
                $length -= 8;
            }
        }
        return $string;
    }


    /**
     * The data is treated as big-endian.  The first (left-most) character of the string
     * argument is converted into the most significant bits of the resultant bit string.
     */
    public static function fromString($string) {
        $bitCount = strlen($string) * 8;
        $bitString = new BitString($bitCount);
        $index = $bitCount - 1;
        foreach (str_split($string) as $char) {
            $bitString->setBitsHighToLow($index, str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT));
            $index -= 8;
        }
        return $bitString;
    }
}

