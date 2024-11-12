<?php

require_once __DIR__ . "/../db/db.php";
require_once __DIR__ . "/./TableService.php";

class ReservationService
{
    public function GetByCustomerId($id)
    {
        global $db;
        $stmt = $db->prepare("select * from reservations where customer_id= ?");
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function GetAll()
    {
        global $db;
        $stmt = $db->prepare("select * from reservations");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function Add($reservation)
    {
        if (mysql_datetime(time()) > mysql_datetime_from_mystring($reservation["reservation_datetime"])) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "Reservation date and time must be in the future"]);
            return null;
        }

        $TableService = new TableService();
        $available = array_map(function ($item) {
            return $item["table_id"];
        }, $TableService->GetAvailable());

        if (!in_array($reservation["table_id"], $available)) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "Table is not available"]);
            return null;
        }

        $table = $TableService->GetById($reservation["table_id"]);
        if ($table["seating_capacity"] < $reservation["number_of_guests"]) {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "Table does not have enough seating capacity"]);
            return null;
        }

        global $db;
        $stmt = $db->prepare("insert into reservations (customer_id, table_id, reservation_datetime, number_of_guests, special_requests) values (?, ?, ?, ?, ?)");
        $stmt->bindParam(1, $reservation["customer_id"]);
        $stmt->bindParam(2, $reservation["table_id"]);
        $stmt->bindParam(3, $reservation["reservation_datetime"]);
        $stmt->bindParam(4, $reservation["number_of_guests"]);
        $stmt->bindParam(5, $reservation["special_requests"]);
        $stmt->execute();
        return $this->GetById($db->lastInsertId());
    }

    public function Update($reservation)
    {
        global $db;
        $stmt = $db->prepare("update reservations set customer_id = ?, table_id = ?, reservation_datetime = ?, number_of_guests = ?, special_requests = ? where reservation_id = ?");
        $stmt->bindParam(1, $reservation["customer_id"]);
        $stmt->bindParam(2, $reservation["table_id"]);
        $stmt->bindParam(3, $reservation["reservation_datetime"]);
        $stmt->bindParam(4, $reservation["number_of_guests"]);
        $stmt->bindParam(5, $reservation["special_requests"]);
        $stmt->bindParam(6, $reservation["reservation_id"]);
        $stmt->execute();
        return $this->GetById($reservation["reservation_id"]);
    }


    public function Delete($id)
    {
        global $db;
        $stmt = $db->prepare("delete from reservations where reservation_id = ?");
        $stmt->bindParam(1, $id);
        if (!$stmt->execute()) {
            return false;
        }
        return true;
    }

    public function GetById($id)
    {
        global $db;
        $stmt = $db->prepare("select * from reservations where reservation_id = ?");
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch();
    }
}
