<?php

namespace App;

class Database
{
    private \SQLite3 $db;

    public function __construct()
    {
        $this->db = new \SQLite3(__DIR__ . '/../data/booking.sqlite');
    }

    public function __destruct()
    {
        $this->db->close();
        unset($this->db);
    }

    public function lastInsertRowId()
    {
        return $this->db->lastInsertRowID();
    }

    public function prepare(string $sql)
    {
        return $this->db->prepare($sql);
    }

    public function storeOrder($event_id, $event_date, $ticket_adult_quantity, $ticket_kid_quantity,
                               $ticket_discount_quantity, $ticket_group_quantity, $barcode, $equal_price)
    {
        $stmt = $this->db->prepare("
            INSERT INTO orders 
                (event_id, event_date, ticket_adult_quantity, ticket_kid_quantity, ticket_discount_quantity, 
                 ticket_group_quantity, barcode, equal_price, user_id, created) 
            VALUES (:event_id, :event_date, :ticket_adult_quantity, :ticket_kid_quantity, :ticket_discount_quantity,
                    :ticket_group_quantity, :barcode, :equal_price, :user_id, :created)
        ");
        $stmt->bindValue('event_id', $event_id);
        $stmt->bindValue('event_date', $event_date);
        $stmt->bindValue('ticket_adult_quantity', $ticket_adult_quantity);
        $stmt->bindValue('ticket_kid_quantity', $ticket_kid_quantity);
        $stmt->bindValue('ticket_discount_quantity', $ticket_discount_quantity);
        $stmt->bindValue('ticket_group_quantity', $ticket_group_quantity);
        $stmt->bindValue('barcode', $barcode);
        $stmt->bindValue('equal_price', $equal_price);
        $stmt->bindValue('user_id', 1);
        $stmt->bindValue('created', date('Y-m-d H:i:s'));

        $stmt->execute();

        return $this->db->lastInsertRowID();
    }
}