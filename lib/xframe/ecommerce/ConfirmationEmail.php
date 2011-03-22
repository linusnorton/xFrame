<?php

namespace xframe\ecommerce;

/**
 * Address
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package ecommerce
 *
 */
class ConfirmationEmail {
    /**
     * @var CustomerOrder
     */
    private $order;
    private $siteName;
    private $url;
    private $currency;
    private $prefix;

    /**
     *
     * @param CustomerOrder $order
     * @param string $siteName
     * @param string $url
     * @param string $currency
     * @param string $prefix
     */
    public function __construct(CustomerOrder $order,
                                $siteName,
                                $url,
                                $currency,
                                $prefix = "") {
        $this->order = $order;
        $this->siteName = $siteName;
        $this->url = $url;
        $this->currency = $currency;
        $this->prefix = $prefix;
    }

    public function send($to) {
        $subject = "Your order with ".$this->siteName;
        $content = $this->build();
        $headers = "Content-Type: text/plain; charset=UTF-8";

        if (!mail($to, $subject, $content, $headers)) {
            throw new \Exception("Could not send confirmation email to ".$to);
        }
    }

    protected function build() {
        return "{$this->order->getCustomer()->getName()},

Thank you for placing your order with {$this->siteName}. This receipt
confirms that we have your order and have begun processing it.

Your order refernce number is: {$this->prefix}{$this->order->getId()}

Name:\t\t{$this->order->getCustomer()->getName()} ({$this->order->getCustomer()->getEmailAddress()})
Address:\t{$this->order->getAddress()->getAddress1()}
\t\t{$this->order->getAddress()->getAddress2()}
\t\t{$this->order->getAddress()->getCity()}, {$this->order->getAddress()->getCounty()}, {$this->order->getAddress()->getPostCode()}, {$this->order->getAddress()->getCountry()}

Telephone:\t{$this->order->getCustomer()->getTelephoneNumber()}
Order Items:\t{$this->order->getOrderString($this->currency)}
Sub Total:\t{$this->currency}".number_format($this->order->getSubTotal() / 100, 2)."
Postage:\t{$this->currency}".number_format($this->order->getDeliveryCost() / 100, 2)."
Total:\t\t{$this->currency}".number_format($this->order->getTotal() / 100, 2)."

Where applicable prices include tax.

Thank you for placing your order with {$this->siteName}.
{$this->url}
";

    }
}
