<?php

namespace xframe\ecommerce;

/**
 * A delivery option is a method of delivering a set of products, which has a
 * cost.
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package ecommerce
 * 
 * @MappedSuperclass
 */
class DeliveryOption {
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
     * @Column(type="integer")
     */
    private $price;
    
    /**
     *
     * @param string $name
     * @param int $price
     * @param boolean $taxInclusive 
     */
    function __construct($name, $price) {
        $this->name = $name;
        $this->price = $price;
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
     * @return integer
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * @param integer $price 
     */
    public function setPrice($price) {
        $this->price = $price;
    }
}


