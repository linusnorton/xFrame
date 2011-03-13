<?php

namespace xframe\ecommerce;

/**
 * An order has customer details and several order items
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package ecommerce
 *
 * @MappedSuperclass
 */
class CustomerOrder {
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;
    /**
     * @ManyToOne(targetEntity="Customer")
     */
    private $customer;    
    /**
     * @OneToOne(targetEntity="Address")
     */
    private $address;    
    /**
     * @OneToMany(targetEntity="OrderItem", mappedBy="order")
     */
    private $orderItems;    
    /**
     * @Column(type="integer")
     */
    private $deliveryCost;  
    /**
     * @Column(type="integer")
     */
    private $deliveryZone;  
    /**
     * @Column(type="boolean")
     */
    private $complete;  
    /**
     * @Column(type="datetime")
     */
    private $created;  
    /**
     * @Column(type="datetime")
     */
    private $lastUpdated;  

    /**
     *
     * @param Customer $customer
     * @param Address $address
     * @param string $deliveryCost
     * @param string $deliveryZone
     * @param boolean $complete
     * @param string $created
     * @param string $lastUpdated
     * @param array $orderItems 
     */
    public function __construct(Customer $customer, 
                                Address $address, 
                                $deliveryCost, 
                                $deliveryZone, 
                                $complete, 
                                $created, 
                                $lastUpdated, 
                                array $orderItems = array()) {
        $this->customer = $customer;
        $this->address = $address;
        $this->orderItems = $orderItems;
        $this->deliveryCost = $deliveryCost;
        $this->deliveryZone = $deliveryZone;
        $this->complete = $complete;
        $this->created = $created;
        $this->lastUpdated = $lastUpdated;
    }

    public function addBasketItems(array $basketItems) {
        
        foreach ($basketItems as $item) {
            $this->orderItems[] = new OrderItem(
                $this,
                $item["object"]->getId(), 
                $item["object"]->getPrice(), 
                $item["quantity"]
            );
        }
    }
    
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getCustomer() {
        return $this->customer;
    }

    public function setCustomer($customer) {
        $this->customer = $customer;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function getOrderItems() {
        return $this->orderItems;
    }

    public function setOrderItems($orderItems) {
        $this->orderItems = $orderItems;
    }

    public function getDeliveryCost() {
        return $this->deliveryCost;
    }

    public function setDeliveryCost($deliveryCost) {
        $this->deliveryCost = $deliveryCost;
    }

    public function getDeliveryZone() {
        return $this->deliveryZone;
    }

    public function setDeliveryZone($deliveryZone) {
        $this->deliveryZone = $deliveryZone;
    }
    public function getComplete() {
        return $this->complete;
    }

    public function setComplete($complete) {
        $this->complete = $complete;
    }

    public function getCreated() {
        return $this->created;
    }

    public function setCreated($created) {
        $this->created = $created;
    }

    public function getLastUpdated() {
        return $this->lastUpdated;
    }

    public function setLastUpdated($lastUpdated) {
        $this->lastUpdated = $lastUpdated;
    }

}


