<?php

namespace xframe\ecommerce\paypal;
use xframe\ecommerce\Basket;

/**
 * PayPalForm
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package paypal
 */
class PayPalForm {
    /**
     * @var Customerbasket
     */
    private $basket;
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
     * @param Basket $basket
     * @param string $paypalAccount
     * @param string $domain
     * @param string $failURL
     * @param string $successURL
     * @param string $ipnURL
     * @param string $location
     * @param string $currencyCode 
     */
    public function __construct(Basket $basket,
                                $paypalAccount,
                                $domain,
                                $failURL,
                                $successURL,
                                $ipnURL,
                                $location,
                                $currencyCode,
                                $action) {
        $this->basket  = $basket;
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
            'cmd' => '_cart',
            'business' => $this->payPalAccount,
            'domain' => $this->domain,
            'currency_code' => $this->currencyCode,
            'no_shipping' => '1',
            'cancel_return' => "http://".$this->domain.$this->failURL,
            'return' => "http://".$this->domain.$this->successURL,
            'notify_url' => "http://".$this->domain.$this->ipnURL,
            'handling_cart' => number_format($this->basket->getDeliveryOption()->getPrice() / 100, 2),
            'lc' => $this->location,
            'rm' => '2',
            'upload' => '1'
        );
        
        $i = 1;
        foreach ($this->basket->getProducts() as $item) {
            $fields['item_name_'.$i] = $item['object']->getName();
            $fields['amount_'.$i] = number_format($item['object']->getPrice() / 100, 2);
            $fields['quantity_'.$i] = $item['quantity'];;
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

