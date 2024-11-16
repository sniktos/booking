<?php

namespace App;

class API
{
    private string $host = 'https://api.site.com';

    public function book($event_id, $event_date, $ticket_adult_price, $ticket_adult_quantity,
                         $ticket_kid_price, $ticket_kid_quantity, $ticket_discount_price, $ticket_discount_quantity,
                         $ticket_group_price, $ticket_group_quantity, $barcode): array
    {
        $response = $this->sendRequest('book', [
            'event_id' => $event_id,
            'event_date' => $event_date,
            'ticket_adult_price' => $ticket_adult_price,
            'ticket_adult_quantity' => $ticket_adult_quantity,
            'ticket_kid_price' => $ticket_kid_price,
            'ticket_kid_quantity' => $ticket_kid_quantity,
            'ticket_discount_price' => $ticket_discount_price,
            'ticket_discount_quantity' => $ticket_discount_quantity,
            'ticket_group_price' => $ticket_group_price,
            'ticket_group_quantity' => $ticket_group_quantity,
            'barcode' => $barcode,
        ]);

        return rand(0, 1) === 1
            ? ['message' => 'order successfully booked']
            : ['error' => 'barcode already exists'];
    }

    public function approve(string $barcode)
    {
        $response = $this->sendRequest('approve', [
            'barcode' => $barcode
        ]);

        switch (rand(0, 4)) {
            case 0:
                return ['message' => 'order successfully aproved'];
            case 1:
                return ['error' => 'event cancelled'];
            case 2:
                return ['error' => 'no tickets'];
            case 3:
                return ['error' => 'no seats'];
            case 4:
                return ['error' => 'fan removed'];
        }
    }

    private function sendRequest(string $endpoint, array $data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($curl, CURLOPT_URL, "{$this->host}/{$endpoint}");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);

        $out = curl_exec($curl);

        curl_close($curl);

        return json_decode($out, true);
    }
}