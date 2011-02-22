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
class Category {

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
     * @OneToMany(targetEntity="Product", mappedBy="category")
     */
    private $products;
    /**
     * @ManyToOne(targetEntity="Category", inversedBy="children")
     * @JoinColumn(name="parent", referencedColumnName="id")
     */
    private $parent;
    /**
     * @OneToMany(targetEntity="Category", mappedBy="parent")
     */
    private $children;

    /**
     * Constructor
     *
     * @param string $name
     * @param array $products of {@link Product}
     * @param Category $parent
     * @param array $children of {@link Category}
     */
    public function __construct($name,
                                array $products,
                                Category $parent = null,
                                array $children = array()) {
        $this->name = $name;
        $this->products = $products;
        $this->parent = $parent;
        $this->children = $children;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return array of {@link Product}
     */
    public function getProducts() {
        return $this->products;
    }

    /**
     * @return Category
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * @return array of {@link Category}
     */
    public function getChildren() {
        return $this->children;
    }

}
