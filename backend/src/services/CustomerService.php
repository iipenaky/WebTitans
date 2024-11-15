<?php

require_once __DIR__.'/../db/db.php';

/**
 * A service class handles all the business logic of the application performing the actual database operations and returning responses.
 * Each service class has methods that correspond to the CRUD operations of the entity it is responsible for, or more complex operations that involve multiple entities and often string many methods in the service class together
 * Here we use the $db global variable to access the database connection and perform operations on the database. This $db variable is a PDO object which differs
 * from mysqli in that it is more object-oriented and allows for more secure database operations, thus we bind paramters with the bindParam method instead of the
 * bind_param method in mysqli
 */
class CustomerService
{
    public function GetAll()
    {
        global $db;
        $stmt = $db->prepare('SELECT * FROM customer');
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to fetch customers'],
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
        $stmt = $db->prepare('SELECT * FROM customer WHERE customer_id = :id');
        $stmt->bindParam(':id', $id);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to fetch customer'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => $stmt->fetch(),
        ];
    }

    public function Add($customer)
    {
        global $db;
        $stmt = $db->prepare(
            'INSERT INTO customer ( first_name, last_name, phone_number, email, address) VALUES (:first_name, :last_name, :phone_number, :email, :address)'
        );
        $stmt->bindParam(':first_name', $customer['first_name']);
        $stmt->bindParam(':last_name', $customer['last_name']);
        $stmt->bindParam(':phone_number', $customer['phone_number']);
        $stmt->bindParam(':email', $customer['email']);
        $stmt->bindParam(':address', $customer['address']);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to add customer'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 201 Created',
            'data' => $this->GetById($db->lastInsertId())['data'],
        ];

    }

    public function Update($customer)
    {
        global $db;
        $res = $this->GetById($customer['customer_id']);
        if (! isset($res['data']) || ! is_array($res['data']) || count($res['data']) === 0) {
            return [
                'header' => 'HTTP/1.1 404 Not Found',
                'data' => ['error' => 'Customer not found'],
            ];
        }
        $stmt = $db->prepare(
            'UPDATE customer SET first_name = :first_name, last_name = :last_name, phone_number = :phone_number, email = :email, address = :address WHERE customer_id = :id'
        );
        $stmt->bindParam(':id', $customer['customer_id']);
        $stmt->bindParam(':first_name', $customer['first_name']);
        $stmt->bindParam(':last_name', $customer['last_name']);
        $stmt->bindParam(':phone_number', $customer['phone_number']);
        $stmt->bindParam(':email', $customer['email']);
        $stmt->bindParam(':address', $customer['address']);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to update customer'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => $this->GetById($customer['customer_id'])['data'],
        ];
    }

    public function Delete($id)
    {
        global $db;
        $res = $this->GetById($id);
        if (! isset($res['data']) || ! is_array($res['data']) || count($res['data']) === 0) {
            return [
                'header' => 'HTTP/1.1 404 Not Found',
                'data' => ['error' => 'Customer not found'],
            ];
        }
        $stmt = $db->prepare('DELETE FROM customer WHERE customer_id = :id');
        $stmt->bindParam(':id', $id);
        if (! $stmt->execute()) {
            return [
                'header' => 'HTTP/1.1 500 Internal Server Error',
                'data' => ['error' => 'Failed to delete customer'],
            ];
        }

        return [
            'header' => 'HTTP/1.1 200 OK',
            'data' => ['message' => 'Customer deleted successfully'],
        ];
    }
}
