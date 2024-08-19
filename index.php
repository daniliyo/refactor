<?php

interface iCart
{
    public function calcVat();

    public function notify();

    public function makeOrder($discount = 1.0);
}

class Cart implements iCart
{
    public $items;
    public $order;

    public function calcVat()
    {
        $vat = 0;
        foreach ($this->items as $item) {
            $vat += $item->getPrice() * 0.18;
        }
        return $vat;
    }

    public function notify()
    {
        $this->sendMail();
    }

    public function sendMail()
    {
        $m = new SimpleMailer('cartuser', 'j049lj-01');
        $p = 0;
        foreach ($this->items as $item) {
            $p += $item->getPrice() * 1.18;
        }
        $ms = "<p> <b>" . $this->order->id() . "</b> " . $p . " .</p>";

        $m->sendToManagers($ms);
    }

    public function makeOrder($discount)
    {
        $p = 0;
        foreach ($this->items as $item) {
            $p += $item->getPrice() * 1.18 * $discount;
        }
        $this->order = new Order($this->items, $p);
        $this->sendMail();
    }
}
