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
    public function __construct(CustomerOrder $order, $productID, $price, $quantity) {
        $this->order = $order;
        $this->productID = $productID;
        $this->price = $price;
        $this->quantity = $quantity;
    }
   
        
}
