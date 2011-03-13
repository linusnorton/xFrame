<?php

namespace xframe\ecommerce;

/**
 * A customer has a telephone number, email address and many physical addresses
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package ecommerce
 *
 * @MappedSuperclass
 */
class Customer {

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;
    /**
     * @Column(type="string")
     */
    private $name;    
    /**
     * @Column(type="string")
     */
    private $telephoneNumber;    
    /**
     * @Column(type="string")
     */
    private $emailAddress;    

    /**
     *
     * @param string $name
     * @param string $telephoneNumber
     * @param string $emailAddress
     * @param addresses $addresses 
     */
    function __construct($name, 
                         $telephoneNumber, 
                         $emailAddress) {
        $this->name = $name;
        $this->telephoneNumber = $telephoneNumber;
        $this->emailAddress = $emailAddress;
    }

    /**
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param integer $id 
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name 
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getTelephoneNumber() {
        return $this->telephoneNumber;
    }

    /**
     * @param string $name 
     */
    public function setTelephoneNumber($telephoneNumber) {
        $this->telephoneNumber = $telephoneNumber;
    }

    /**
     * @return string
     */
    public function getEmailAddress() {
        return $this->emailAddress;
    }

    /**
     * @param string $emailAddress 
     */
    public function setEmailAddress($emailAddress) {
        $this->emailAddress = $emailAddress;
    }
       
}


