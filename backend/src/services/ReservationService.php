<?php

require_once __DIR__.'/../db/db.php';
require_once __DIR__.'/./TableService.php';

class ReservationService
{
    public function GetByCustomerId($id)
    {
        global $db;
        $stmt = $db->prepare('select * from reservations where customer_id= ?');
        $stmt->bindParam(1, $id);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 404 Not Found',
                'data' => ['error' => "Reservation with customer id $id not found"],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => $stmt->fetchAll(),
        ];
    }

    public function GetAll()
    {
        global $db;
        $stmt = $db->prepare('select * from reservations');
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to fetch reservations'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => $stmt->fetchAll(),
        ];
    }

    public function Add($reservation)
    {
        if (mysql_datetime(time()) > mysql_datetime_from_mystring($reservation['reservation_datetime'])) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Reservation date and time must be in the future'],
            ];

        }

        $TableService = new TableService;
        $available = array_map(function ($item) {
            return $item['table_id'];
        }, $TableService->GetAvailable()['data']);

        if (! in_array($reservation['table_id'], $available)) {

            return [
                'header' => 'HTTP/1.1 400 Bad Request',
                'data' => ['error' => 'Table is not available'],
            ];
        }

        $table = $TableService->GetById($reservation['table_id'])['data'];
        if ($table['seating_capacity'] < $reservation['number_of_guests']) {

            return [
                'header' => 'HTTP/1.1 400 Bad Request',
                'data' => ['error' => 'Table does not have enough seating capacity'],
            ];
        }

        global $db;
        $stmt = $db->prepare('insert into reservations (customer_id, table_id, reservation_datetime, number_of_guests, special_requests) values (?, ?, ?, ?, ?)');
        $stmt->bindParam(1, $reservation['customer_id']);
        $stmt->bindParam(2, $reservation['table_id']);
        $stmt->bindParam(3, $reservation['reservation_datetime']);
        $stmt->bindParam(4, $reservation['number_of_guests']);
        $stmt->bindParam(5, $reservation['special_requests']);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to add reservation'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 201 Created',
            'data' => ['message' => 'Reservation successfully updated', 'reservation' => $this->GetById($db->lastInsertId())['data']],
        ];
    }

    public function Update($reservation)
    {
        global $db;
        $res = $this->GetById($reservation['reservation_id']);
        if (! isset($res['data']) || ! is_array($res['data']) || count($res['data']) === 0) {
            return [
                'header' => 'HTTP/1.1 404 Not Found',
                'data' => ['error' => 'Reservation not found'],
            ];
        }

        $stmt = $db->prepare('update reservations set customer_id = ?, table_id = ?, reservation_datetime = ?, number_of_guests = ?, special_requests = ? where reservation_id = ?');
        $stmt->bindParam(1, $reservation['customer_id']);
        $stmt->bindParam(2, $reservation['table_id']);
        $stmt->bindParam(3, $reservation['reservation_datetime']);
        $stmt->bindParam(4, $reservation['number_of_guests']);
        $stmt->bindParam(5, $reservation['special_requests']);
        $stmt->bindParam(6, $reservation['reservation_id']);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to update reservation'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => $this->GetById($db->lastInsertId())['data'],
        ];
    }

    public function Delete($id)
    {
        global $db;
        $res = $this->GetById($id);
        if (! isset($res['data']) || ! is_array($res['data']) || count($res['data']) === 0) {
            return [
                'header' => 'HTTP/1.1 404 Not Found',
                'data' => ['error' => 'Reservation not found'],
            ];
        }

        $stmt = $db->prepare('delete from reservations where reservation_id = ?');
        $stmt->bindParam(1, $id);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to delete reservation'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => ['message' => 'Reservation deleted successfully'],
        ];
    }

    public function GetById($id)
    {
        global $db;
        $stmt = $db->prepare('select * from reservations where reservation_id = ?');
        $stmt->bindParam(1, $id);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 404 Not Found',
                'data' => ['error' => "Reservation with id $id not found"],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => $stmt->fetch(),
        ];
    }
}
