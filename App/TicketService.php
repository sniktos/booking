<?php

namespace App;

class TicketService
{
    private API $api;
    private Database $db;

    public function __construct()
    {
        $this->api = new API();
        $this->db = new Database();
    }

    public function createOrder($event_id, $event_date, $ticket_adult_price, $ticket_adult_quantity, 
                                $ticket_kid_price, $ticket_kid_quantity, $ticket_discount_price, $ticket_discount_quantity,
                                $ticket_group_price, $ticket_group_quantity)
    {
        $barcode = $this->generateBarcode();

        $bookingResult = $this->api->book(
            $event_id,
            $event_date,
            $ticket_adult_price,
            $ticket_adult_quantity,
            $ticket_kid_price,
            $ticket_kid_quantity,
            $ticket_discount_price,
            $ticket_discount_quantity,
            $ticket_group_price,
            $ticket_group_quantity,
            $barcode
        );

        while (isset($bookingResult['error'])) {
            $barcode = $this->generateBarcode();
            $bookingResult = $this->api->book(
                $event_id,
                $event_date,
                $ticket_adult_price,
                $ticket_adult_quantity,
                $ticket_kid_price,
                $ticket_kid_quantity,
                $ticket_discount_price,
                $ticket_discount_quantity,
                $ticket_group_price,
                $ticket_group_quantity,
                $barcode
            );
        }

        $approveResponse = $this->api->approve($barcode);
        if (!isset($approveResponse['error'])) {
            $this->db->storeOrder(
                $event_id,
                $event_date,
                $ticket_adult_quantity,
                $ticket_kid_quantity,
                $ticket_discount_quantity,
                $ticket_group_quantity,
                $barcode,
                $this->getEqualPrice($ticket_adult_price, $ticket_adult_quantity, $ticket_kid_price, $ticket_kid_quantity,
                            $ticket_discount_quantity, $ticket_discount_price, $ticket_group_quantity, $ticket_group_price)
            );

            $ticket_data = [
                [$ticket_adult_quantity, 1],
                [$ticket_kid_quantity, 2],
                [$ticket_discount_quantity, 3],
                [$ticket_group_quantity, 4]
            ];

            $this->storeTicket($this->db->lastInsertRowId(), $ticket_data);



            Response::json(['status' => 'success']);
        } else {
            Response::json($approveResponse);
        }
    }

    public function storeTicket($order_id, $ticket_data)
    {
        foreach ($ticket_data as [$quantity, $type_id]) {
            for ($i = 0; $i < $quantity; $i++) {
                $barcode = $this->generateBarcode();

                $stmt = $this->db->prepare("
                        INSERT INTO tickets (order_id, ticket_type_id, barcode)
                            VALUES (:order_id, :ticket_type_id, :barcode)");

                $stmt->bindValue('order_id', $order_id);
                $stmt->bindValue('ticket_type_id', $type_id);
                $stmt->bindValue('barcode', $barcode);

                $stmt->execute();
            }
        }


    }

    private function generateBarcode(): string
    {
        return mt_rand().mt_rand();
    }

    private function getEqualPrice($ticket_adult_price, $ticket_adult_quantity, $ticket_kid_price, $ticket_kid_quantity,
                       $ticket_discount_price, $ticket_discount_quantity, $ticket_group_price, $ticket_group_quantity): float
    {
        return ($ticket_adult_price * $ticket_adult_quantity) + ($ticket_kid_price * $ticket_kid_quantity)
            + ($ticket_discount_price * $ticket_discount_quantity) + ($ticket_group_price * $ticket_group_quantity);
    }
}