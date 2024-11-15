<?php

require_once __DIR__.'/../db/db.php';
require_once __DIR__.'/./OrderService.php';

class StaffService
{
    public function GetAllOfPosition($position)
    {
        global $db;
        $stmt = $db->prepare('SELECT * FROM staff WHERE position = :position');
        $stmt->bindParam(':position', $position);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to fetch staff'],
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
        $stmt = $db->prepare('SELECT * FROM staff');
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to fetch all staff'],
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
        $stmt = $db->prepare('SELECT * FROM staff WHERE staff_id = :id');
        $stmt->bindParam(':id', $id);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => "Failed to fetch staff with id $id"],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => $stmt->fetch(),
        ];
    }

    public function Update($staff)
    {
        global $db;
        $res = $this->GetById($staff['staff_id']);
        if (! isset($res['data']) || ! is_array($res['data']) || count($res['data']) === 0) {
            return [
                'header' => 'HTTP/1.1 404 Not Found',
                'data' => ['error' => 'Staff not found'],
            ];
        }

        $stmt = $db->prepare(
            'UPDATE staff SET first_name = :first_name, last_name = :last_name, position = :position, email = :email, salary = :salary, passhash = :passhash WHERE staff_id = :id'
        );
        $stmt->bindParam(':id', $staff['staff_id']);
        $stmt->bindParam(':first_name', $staff['first_name']);
        $stmt->bindParam(':last_name', $staff['last_name']);
        $stmt->bindParam(':position', $staff['position']);
        $stmt->bindParam(':email', $staff['email']);
        $stmt->bindParam(':salary', $staff['salary']);
        $stmt->bindParam(':passhash', $staff['passhash']);
        $stmt->execute();

        return $this->GetById($staff['staff_id']);
    }

    public function Delete($id)
    {
        global $db;

        $res = $this->GetById($id);
        if (! isset($res['data']) || ! is_array($res['data']) || count($res['data']) === 0) {
            return [
                'header' => 'HTTP/1.1 404 Not Found',
                'data' => ['error' => 'Staff not found'],
            ];
        }

        // Find all orders that the staff member is associated with
        try {
            $OrderService = new OrderService;
            $orders = $OrderService->GetByStaffId($id)['data'];

            // If the staff member is associated with any orders
            $db->beginTransaction();
            if (count($orders) > 0) {
                // If the staff member is the only waiter, we cannot delete them
                if (count($this->GetAllOfPosition('Waiter')) == 1) {
                    return [
                        'header' => 'HTTP/1.1 400 Bad Request',
                        'data' => ['error' => 'Cannot delete the only waiter'],
                    ];
                }

                // Assign the orders to another waiter randomly
                $staff = $this->GetAllOfPosition('Waiter')['data'];

                $otherStaff = array_filter($staff, function ($s) use ($id) {
                    return $s['staff_id'] != $id;
                });

                $newStaff = array_rand($otherStaff);
                $newStaff = $otherStaff[$newStaff];

                $newStaffId = $newStaff['staff_id'];

                // Update the orders
                foreach ($orders as $order) {
                    $oldOrder = $OrderService->GetById($order['order_id'])['data'];
                    $oldOrder['staff_id'] = $newStaffId;
                    $res = $OrderService->Update($oldOrder);

                    if ($res['header'] != 'HTTP/1.1 200 OK') {
                        $db->rollBack();

                        return [
                            'header' => 'HTTP/1.1 500 Internal Server Error',
                            'data' => ['error' => 'Failed to reassign orders'],
                        ];
                    }
                }
            }

            $stmt = $db->prepare('DELETE FROM staff WHERE staff_id = :id');
            $stmt->bindParam(':id', $id);
            if (! $stmt->execute()) {
                return [
                    'header' => 'HTTP/1.1 500 Internal Server Error',
                    'data' => ['error' => 'Failed to delete staff'],
                ];
            }
            $db->commit();

            return [
                'header' => 'HTTP/1.1 200 OK',
                'data' => ['message' => 'Staff deleted successfully'],
            ];
        } catch (Exception $e) {
            $db->rollBack();

            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to delete staff'],
            ];
        }
    }
}
