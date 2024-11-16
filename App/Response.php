<?php

namespace App;

class Response
{
    public static function json(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
        exit();
    }
}