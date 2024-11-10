<?php

require_once __DIR__."/../db/db.php";
require_once __DIR__."/./OrderService.php";

class StaffService
{
    public function GetNumberOfStaff()
    {
        global $db;
        $stmt = $db->prepare("SELECT COUNT(*) FROM staff");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function GetAllOfPosition($position)
    {
        global $db;
        $stmt = $db->prepare("SELECT * FROM staff WHERE position = :position");
        $stmt->bindParam(":position", $position);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function GetAll()
    {
        global $db;
        $stmt = $db->prepare("SELECT * FROM staff");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function GetById($id)
    {
        global $db;
        $stmt = $db->prepare("SELECT * FROM staff WHERE staff_id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function Update($staff)
    {
        global $db;
        $stmt = $db->prepare(
            "UPDATE staff SET first_name = :first_name, last_name = :last_name, position = :position, email = :email, salary = :salary, passhash = :passhash WHERE staff_id = :id"
        );
        $stmt->bindParam(":id", $staff["staff_id"]);
        $stmt->bindParam(":first_name", $staff["first_name"]);
        $stmt->bindParam(":last_name", $staff["last_name"]);
        $stmt->bindParam(":position", $staff["position"]);
        $stmt->bindParam(":email", $staff["email"]);
        $stmt->bindParam(":salary", $staff["salary"]);
        $stmt->bindParam(":passhash", $staff["passhash"]);
        $stmt->execute();
        return $this->GetById($staff["staff_id"]);
    }

    public function Delete($id)
    {
        global $db;

        // Find all orders that the staff member is associated with
        $OrderService = new OrderService();
        $orders = $OrderService->GetByStaffId($id);

        // If the staff member is associated with any orders
        if (count($orders) > 0) {
            // If the staff member is the only waiter, we cannot delete them
            if (count($this->GetAllOfPosition("Waiter")) == 1) {
                return false;
            }

            // Assign the orders to another waiter randomly
            $staff = $this->GetAllOfPosition("Waiter");

            $otherStaff = array_filter($staff, function ($s) use ($id) {
                return $s["staff_id"] != $id;
            });

            $newStaff = array_rand($otherStaff);
            $newStaff = $otherStaff[$newStaff];

            $newStaffId = $newStaff["staff_id"];

            // Update the orders
            foreach ($orders as $order) {
                $oldOrder = $OrderService->GetById($order["order_id"]);
                $oldOrder["staff_id"] = $newStaffId;
                $OrderService->Update($oldOrder);
            }
        }

        $stmt = $db->prepare("DELETE FROM staff WHERE staff_id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return true;
    }
}
