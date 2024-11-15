<?php

require_once __DIR__.'/../db/db.php';

class TableService
{
    public function GetAll()
    {
        global $db;
        $stmt = $db->prepare('select * from `table`');
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to fetch tables'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => $stmt->fetchAll(),
        ];
    }

    public function Add($data)
    {
        global $db;
        $stmt = $db->prepare('insert into `table` (table_number, seating_capacity, location) values (?, ?, ?)');
        $stmt->bindParam(1, $data['table_number']);
        $stmt->bindParam(2, $data['seating_capacity']);
        $stmt->bindParam(3, $data['location']);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to add table'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 201 Created',
            'data' => $this->GetById($db->lastInsertId())['data'],
        ];
    }

    public function Update($data)
    {
        global $db;
        $res = $this->GetById($data['table_id']);
        if (! isset($res['data']) || ! is_array($res['data']) || count($res['data']) === 0) {
            return [
                'header' => 'HTTP/1.1 404 Not Found',
                'data' => ['error' => 'Table not found'],
            ];
        }

        $stmt = $db->prepare('update `table` set table_number = ?, seating_capacity = ?, location = ? where table_id = ?');
        $stmt->bindParam(1, $data['table_number']);
        $stmt->bindParam(2, $data['seating_capacity']);
        $stmt->bindParam(3, $data['location']);
        $stmt->bindParam(4, $data['table_id']);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to update tables'],
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
                'data' => ['error' => 'Table not found'],
            ];
        }

        $stmt = $db->prepare('delete from `table` where table_id = ?');
        $stmt->bindParam(1, $id);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to delete table'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => ['message' => 'Table deleted successfully'],
        ];
    }

    public function GetAvailable()
    {
        global $db;
        $stmt = $db->prepare('select * from `table` where table_id not in (select table_id from reservations)');
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to fetch available tables'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => $stmt->fetchAll(),
        ];
    }

    public function GetById($id)
    {
        global $db;
        $stmt = $db->prepare('select * from `table` where table_id = ?');
        $stmt->bindParam(1, $id);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to fetch table'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => $stmt->fetch(),
        ];
    }
}
