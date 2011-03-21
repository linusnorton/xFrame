<?php


namespace xframe\ecommerce;

/**
 * A number of products associated with an Order
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package ecommerce
 *
 * @MappedSuperclass
 */
class OrderItem {
    
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;
    /**
     * @ManyToOne(targetEntity="CustomerOrder", inversedBy="orderItems")
     * @JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;
    /**
     * @Column(type="integer")
     */
    private $productID;  
    /**
     * @Column(type="string")
     */
    private $name;  
    /**
     * @Column(type="integer")
     */
    private $price;
    /**
     * @Column(type="integer")
     */
    private $quantity;
    
    /**
     *
     * @param Order $order
     * @param int $productID
     * @param int $price
     * @param int $quantity 
     */
    public function __construct(CustomerOrder $order, 
                                $productID, 
                                $name,
                                $price, 
                                $quantity) {
        $this->order = $order;
        $this->productID = $productID;
        $this->name = $name;
        $this->price = $price;
        $this->quantity = $quantity;
    }
   
    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param int $id 
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return CustomerOrder
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * @param CustomerOrder $order 
     */
    public function setOrder(CustomerOrder $order) {
        $this->order = $order;
    }

    /**
     * @return int
     */
    public function getProductID() {
        return $this->productID;
    }

    /**
     * @param int $productID 
     */
    public function setProductID($productID) {
        $this->productID = $productID;
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
     * @return int
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * @param int $price 
     */
    public function setPrice($price) {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * @param int $quantity 
     */
    public function setQuantity($quantity) {
        $this->quantity = $quantity;
    }
}
