<?php

namespace xframe\ecommerce;

/**
 * A product has a name and a price, optionally it can be related to a category
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package ecommerce
 *
 * @MappedSuperclass
 */
class Product {

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
     * @ManyToOne(targetEntity="Category", inversedBy="products")
     * @JoinColumn(name="category", referencedColumnName="id")
     */
    private $category;

    /**
     * Constructor
     * @param string $name
     * @param integer $price
     * @param Category $category
     */
    public function __construct($name, $price, Category $category = null) {
        $this->name = $name;
        $this->price = $price;
        $this->category = $category;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return integer
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * @return Category
     */
    public function getCategory() {
        return $this->category;
    }
}
