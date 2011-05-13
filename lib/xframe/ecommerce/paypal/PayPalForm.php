<?php

namespace xframe\ecommerce\paypal;
use xframe\ecommerce\CustomerOrder;

/**
 * PayPalForm
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package paypal
 */
class PayPalForm {
    /**
     * @var CustomerOrder
     */
    private $order;
    private $payPalAccount;
    private $domain;
    private $failURL;
    private $successURL;
    private $ipnURL;
    private $location;
    private $currencyCode;
    private $action;
    
    /**
     *
     * @param order $order
     * @param string $paypalAccount
     * @param string $domain
     * @param string $failURL
     * @param string $successURL
     * @param string $ipnURL
     * @param string $location
     * @param string $currencyCode 
     */
    public function __construct(CustomerOrder $order,
                                $paypalAccount,
                                $domain,
                                $failURL,
                                $successURL,
                                $ipnURL,
                                $location,
                                $currencyCode,
                                $action) {
        $this->order  = $order;
        $this->payPalAccount = $paypalAccount;
        $this->domain = $domain;
        $this->failURL = $failURL;
        $this->successURL = $successURL;
        $this->ipnURL = $ipnURL;
        $this->location = $location;
        $this->currencyCode = $currencyCode;
        $this->action = $action;
    }
    
    public function getFields() {        
        $fields = array(
            'custom' => $this->order->getId(),
            'cmd' => '_cart',
            'business' => $this->payPalAccount,
            'domain' => $this->domain,
            'currency_code' => $this->currencyCode,
            'no_shipping' => '1',
            'cancel_return' => "http://".$this->domain.$this->failURL,
            'return' => "http://".$this->domain.$this->successURL,
            'notify_url' => "http://".$this->domain.$this->ipnURL,
            'handling_cart' => number_format($this->order->getDeliveryCost() / 100, 2),
            'lc' => $this->location,
            'rm' => '2',
            'upload' => '1'
        );
        
        $i = 1;
        foreach ($this->order->getOrderItems() as $item) {
            $fields['item_name_'.$i] = $item->getName();
            $fields['amount_'.$i] = number_format($item->getPrice() / 100, 2);
            $fields['quantity_'.$i] = $item->getQuantity();
            $i++;
        }
            
        return $fields;
    }
    
    /**
     * @return string
     */
    public function getAction() {
        return $this->action;
    }
}

