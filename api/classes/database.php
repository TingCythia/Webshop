<?php 

class Database {
    
    public $db;
    public $selectedTable;
    public $selectedClass;

    function __construct($table, $class) {
        $dns = "mysql:host=localhost;dbname=final_shop";
        $user = "root";
        $password = "root";

        $this->db = new PDO($dns, $user, $password);
        $this->db->exec("set names utf8");

        $this->selectedTable = $table;
        $this->selectedClass = $class;
    }

    public function fetchAll($createInstanceFunction) {
        $query = $this->db->prepare("SELECT * FROM " . $this->selectedTable);

        $query->execute();

        $result = $query->fetchAll(PDO::FETCH_FUNC, $createInstanceFunction);

        return $result;
    }

    public function getOrderDetails($id) {
        $query = $this->db->prepare("SELECT orders.* , orders.id as orders_id , cards.* , cards.id as cards_id , products.name , customers.fName , customers.lName   FROM `orders`
            LEFT JOIN cards on cards.unique_code = orders.unique_code
            LEFT JOIN products on products.ID = cards.product_id
            LEFT JOIN customers on customers.ID = cards.customer_id
            WHERE orders.ID = $id");
        $query->execute();
        $result = $query->fetchAll();

        if(empty($result)){
            throw new Exception($this->selectedClass . " with ID " . $id . " not found...", 500);
            exit;
        }

        return $result;
    }
    public function fetchById($id, $createInstanceFunction) {
        $query = $this->db->prepare("SELECT * FROM " . $this->selectedTable . " WHERE ID=" . $id . ";");
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_FUNC, $createInstanceFunction);

        if(empty($result)){
            throw new Exception($this->selectedClass . " with ID " . $id . " not found...", 500);
            exit;
        }

        return $result[0];
    }

    public function insert($entity) {

        $columns = "";
        $values = [];

        foreach((array)$entity as $key => $value) {
            if($key != "id") {
                $columns .= $key . ",";
                array_push($values, $value);
            }
        }

        $columns = substr($columns, 0, -1);

        $query = $this->db->prepare("INSERT INTO ". $this->selectedTable ." (" .$columns. ") VALUES (?,?,?)");
        $query->execute($values);
        
        return "New " . $this->selectedClass . " saved!";
    }
    
    public function delete($id) {
        $query = $this->db->prepare("DELETE FROM ". $this->selectedTable ." WHERE id=" . $id . ";");
        $query->execute();

        if($query->rowCount() > 0) {
            return $this->selectedClass . " with id: " . $id . " is deleted!";
        } else {
            return "There are no " . $this->selectedClass . " with id: " . $id . "...";
        }
    }

    public function freeQuery($sqlQuery) {
        $query = $this->db->prepare($sqlQuery);
        $query->execute();
//        $result = $query->fetchAll(PDO::FETCH_FUNC, $createInstanceFunction);
        $result = $query->fetchAll();
        return $result;
    }
    public function lastInsertID() {
        return $this->db->lastInsertId();
    }


}

?>