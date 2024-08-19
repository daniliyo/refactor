<?php

interface iCart
{
    public function calcVat();

    public function notify();

    public function makeOrder($discount = 1.0);
}

/*
У нас есть переменные в расчете суммы - 0.18 и 1.18 - Enum как альтернатива константам
*/
enum vatOddEnum
{
    case ForCalc = 0.18;
    case ForMail = 1.18;
}

class Cart implements iCart
{
    
    
    // Пользователю не стоит уметь напрямую влиять на items
    protected $items;
    // Пользователю не нужно знать про эту переменную
    protected Order $order;
    
    // new Cart(new SimpleMailer('cartuser', 'j049lj-01'));
    public function __construct(protected SimpleMailer $m) {}
    /*
    Альтернатива
    interface MailerInterface(){...};
    class SimpleMailer interface MailerInterface { ... }
    public function __construct(protected MailerInterface $m) {}
    */
    
    public function getPriceItems(vatOddEnum $odd, $discount = 1){
        $result = 0;
        foreach ($this->items as $item) {
            $result += $item->getPrice() * $odd * $discount;
        }
        return $result;
    }
    
    // Для контроля установки товаров
    public function setItems(Array $items)
    {
        $this->items = $items;
    } 

    public function calcVat()
    {
        // В данном месте дублируется ункциональность - вынесен в отдельный метод
        return $this->getPriceItems(vatOddEnum::ForCalc);
    }

    // Это внутренний метод, пользователю он не понадобится напрямую 
    protected function notify()
    {
        $this->sendMail();
    }

    // Это внутренний метод, пользователю он не понадобится напрямую 
    protected function sendMail()
    {
        // данная запись создает жесткую связь внутри класса
        // если планируется расширение, данный объект лучше передавать через конструктор или метод установщик
        // $m = new SimpleMailer('cartuser', 'j049lj-01');
        
        // В данном месте дублируется ункциональность - вынесен в отдельный метод
        $p = $this->getPriceItems(vatOddEnum::ForMail);
        
        $ms = "<p> <b>" . $this->order->id() . "</b> " . $p . " .</p>";

        $m->sendToManagers($ms);
    }

    public function makeOrder($discount)
    {
        // В данном месте дублируется ункциональность - вынесен в отдельный метод
        $p = $this->getPriceItems(vatOddEnum::ForCalc, $discount);
        
        $this->order = new Order($this->items, $p);
        $this->sendMail();
    }
}
