<?php

namespace xframe\ecommerce\paypal;

/**
 * PayPalCURL
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package paypal
 */
class PayPalCURL {
    /**
     * @var PayPalForm
     */
    private $form;
    /**
     * @var string $url 
     */
    private $url;
    
    /**
     * @param PayPalForm $form
     * @param string $url 
     */
    public function __construct(PayPalForm $form, $url) {
        $this->form = $form;
        $this->url = $url;
    }
    
    /**
     * Use CURL to go to PayPal
     */
    public function send() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->form->getFields());
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
        curl_exec($ch);
        curl_close($ch);
    }
}

