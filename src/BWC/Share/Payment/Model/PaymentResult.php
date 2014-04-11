<?php

namespace BWC\Share\Payment\Model;

use BWC\Share\Object\DeserializeTrait;

class PaymentResult
{
    use DeserializeTrait;

    const STATUS_ACCEPTED = 1;
    const STATUS_DECLINED = 2;
    const STATUS_PENDING = 3;
    const STATUS_CANCELLED = 4;
    const STATUS_ERROR = 5;

    /** @var  int */
    public $status;

    /** @var string */
    public $transactionID;

    /** @var string */
    public $ticket;

    /** @var string */
    public $ccn;

    /** @var string */
    public $expirationDate;

    /** @var string */
    public $orderID;

    /** @var  string */
    public $message;

    /** @var  array */
    public $data;

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return $this->status === self::STATUS_ACCEPTED;
    }
}