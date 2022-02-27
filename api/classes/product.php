<?php 

class Product {
    public $id;
    public $name;
    public $quantity;
    public $price;
    public $description;

    function __construct($id, $name,$quantity, $price, $description) {
        $this->id = $id;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->price = $price;
        $this->description = $description;

    }

}

?>