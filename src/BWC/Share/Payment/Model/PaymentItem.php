<?php

namespace BWC\Share\Payment\Model;

use BWC\Share\Object\DeserializeTrait;

class PaymentItem
{
    use DeserializeTrait;

    /** @var  string */
    public $title;

    /** @var  double */
    public $amount;

    /** @var  double */
    public $VATAmount;

    /** @var  integer */
    public $VATPercent;

    /** @var  string */
    public $unit;

    /** @var  integer */
    public $quantity;

    /** @var  string */
    public $iconURL;

    /**
     * @return double
     */
    public function getVatAmount()
    {
        if (null === $this->VATAmount) {
            return round($this->amount * $this->VATPercent / 100, 2);
        } else {
            return $this->VATAmount;
        }
    }
}