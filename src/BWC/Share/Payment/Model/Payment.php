<?php

namespace BWC\Share\Payment\Model;

use BWC\Share\Payment\Model\PaymentItem;
use BWC\Share\Object\DeserializeTrait;

class Payment
{
    use DeserializeTrait;

    /** @var  double */
    public $totalAmount;

    /** @var  string */
    public $currency;

    /** @var  string */
    public $description;

    /** @var PaymentItem[] */
    public $items = [];


    /**
     * @return double|int
     */
    public function getTotalVat()
    {
        $totalVat = 0;
        foreach ($this->items as $item) {
            $totalVat += $item->getVatAmount();
        }
        return $totalVat;
    }

    /**
     * @param array $arr
     */
    public function deserializeExtra($arr)
    {
        $this->items = [];
        if (isset($arr['items']) && is_array($arr['items'])) {
            foreach ($arr['items'] as $itemData) {
                $this->items[] = PaymentItem::deserialize($itemData);
            }
        }
    }
}