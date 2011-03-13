<?php

namespace xframe\ecommerce;

/**
 * A basket holds products
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package ecommerce
 */
class Basket {
    /**
     * @var array of {@link Product}
     */
    private $products;

    /**
     * @var DeliveryOption
     */
    private $deliveryOption;
    
    /**
     * @var boolean
     */
    private $taxInclusive;
    
    /**
     * Constructor
     * @param array $products
     * @param DeliveryOption $deliveryOption
     * @param type $taxInclusive 
     */
    public function __construct(array $products = array(), 
                                DeliveryOption $deliveryOption = null,
                                $taxInclusive = true) {
        $this->products = $products;
        $this->deliveryOption = $deliveryOption;
        $this->taxInclusive = $taxInclusive;
    }
    
    /**
     * @return array
     */
    public function getProducts() {
        return $this->products;
    }

    /**
     * @param array $products 
     */
    public function setProducts(array $products) {
        $this->products = $products;
    }

    /**
     * @return DeliveryOption
     */
    public function getDeliveryOption() {
        return $this->deliveryOption;
    }

    /**
     * @param DeliveryOption $deliveryOption 
     */
    public function setDeliveryOption(DeliveryOption $deliveryOption) {
        $this->deliveryOption = $deliveryOption;
    }
    
    /**
     * Reset the delivery option to null
     */
    public function clearDeliveryOption() {
        $this->deliveryOption = null;
    }

    /**
     * @return boolean
     */
    public function getTaxInclusive() {
        return $this->taxInclusive;
    }

    /**
     * @param boolean $taxInclusive 
     */
    public function setTaxInclusive($taxInclusive) {
        $this->taxInclusive = $taxInclusive;
    }

    /**
     * Add a product to the basket
     * @param Product $product
     * @param int $quantity
     */
    public function add(Product $product, $quantity = 1) {
        $productID = $product->getId();
        
        if (array_key_exists($productID, $this->products)) {
            $this->products[$productID]['quantity'] += $quantity;
        }
        else if ($quantity > 0){
            $this->products[$productID] = array(
                'object' => $product,
                'quantity' => (int) $quantity
            );
        }        
    }
    
    /**
     * Remove a product from the basket
     * @param Product $product
     * @param int $quantity
     */
    public function remove(Product $product, $quantity = 0) { 
        $this->removeById($product->getId());
    }
    
    /**
     * Remove the product from the basket using it's product id
     * @param type $productID
     * @param type $quantity 
     */
    public function removeById($productID, $quantity = 0) {
        //if 0 remove all
        if ($quantity == 0) {
            unset($this->products[$productID]);
        }
        else if (array_key_exists($productID, $this->products)) {
            
            $this->products[$productID]['quantity'] -= $quantity;
            if ($this->products[$productID]['quantity'] < 1) {
                unset($this->products[$productID]);
            }
        }
    }
    
    /**
     * @return integer
     */
    public function getNumberItems() {
        $total = 0;
        foreach ($this->products as $basketItem) {
            $total += $basketItem['quantity'];
        }
        
        return $total;        
    }
    
    /**
     * Get the basket sub total
     * @return integer
     */
    public function getSubTotal() {
        $total = 0;
        foreach ($this->products as $basketItem) {
            $total += $basketItem['object']->getPrice() * $basketItem['quantity'];
        }

        return $total;
    }
    
    public function getTotal() {
        $total = 0;
        foreach ($this->products as $basketItem) {
            $total += $basketItem['object']->getPrice() * $basketItem['quantity'];
        }
        
        if ($this->deliveryOption != null) {
            $total += $this->deliveryOption->getPrice();
        }
        
        return $total;        
    }
}

