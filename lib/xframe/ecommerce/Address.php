<?php

namespace xframe\ecommerce;

/**
 * Address
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package ecommerce
 *
 * @MappedSuperclass
 */
class Address {

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
    private $address1;    
    /**
     * @Column(type="string")
     */
    private $address2;    
    /**
     * @Column(type="string")
     */
    private $city;    
    /**
     * @Column(type="string")
     */
    private $county;    
    /**
     * @Column(type="string")
     */
    private $country;    
    /**
     * @Column(type="string")
     */
    private $postcode;    
    /**
     * @ManyToOne(targetEntity="Customer")
     */
    private $customer;    

    /**
     *
     * @param string $name
     * @param string $address1
     * @param string $address2
     * @param string $city
     * @param string $county
     * @param string $country
     * @param string $postcode
     * @param Customer $customer 
     */
    public function __construct($name, 
                                $address1, 
                                $address2, 
                                $city, 
                                $county, 
                                $country, 
                                $postcode, 
                                Customer $customer) {
        $this->name = $name;
        $this->address1 = $address1;
        $this->address2 = $address2;
        $this->city = $city;
        $this->county = $county;
        $this->country = $country;
        $this->postcode = $postcode;
        $this->customer = $customer;
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getAddress1() {
        return $this->address1;
    }

    public function setAddress1($address1) {
        $this->address1 = $address1;
    }

    public function getAddress2() {
        return $this->address2;
    }

    public function setAddress2($address2) {
        $this->address2 = $address2;
    }

    public function getCity() {
        return $this->city;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function getCounty() {
        return $this->county;
    }

    public function setCounty($county) {
        $this->county = $county;
    }

    public function getCountry() {
        return $this->country;
    }

    public function setCountry($country) {
        $this->country = $country;
    }

    public function getPostcode() {
        return $this->postcode;
    }

    public function setPostcode($postcode) {
        $this->postcode = $postcode;
    }

    public function getCustomer() {
        return $this->customer;
    }

    public function setCustomer($customer) {
        $this->customer = $customer;
    }


}


