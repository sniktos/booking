<?php

use App\Database;
use App\TicketService;

require_once __DIR__ . '/autoload.php';

$eventId = 1;
$eventDate = '2024-12-01 20:00:00';
$ticketAdultPrice = 500;
$ticketAdultQuantity = 1;
$ticketKidPrice = 300;
$ticketKidQuantity = 3;
$ticketDiscountPrice = 300;
$ticketDiscountQuantity = 3;
$ticketGroupPrice = 300;
$ticketGroupQuantity = 3;

$service = new TicketService();
$service->createOrder(
    $eventId,
    $eventDate,
    $ticketAdultPrice,
    $ticketAdultQuantity,
    $ticketKidPrice,
    $ticketKidQuantity,
    $ticketDiscountPrice,
    $ticketDiscountQuantity,
    $ticketGroupPrice,
    $ticketGroupQuantity
);