import unittest
import requests
import json

HOST = "http://localhost:8000"
HEADERS = {"Content-Type": "application/json"}


def setup():
    # Admin signup
    resAdmin = requests.post(
        f"{HOST}/admin/signup",
        json={"username": "madiba", "password": "madiba"},
        headers=HEADERS,
    )

    # User signup
    resCustomer = requests.post(
        f"{HOST}/user/signup",
        json={
            "first_name": "Madiba",
            "last_name": "Hudson-Quansah",
            "email": "madiba@gmail.com",
            "password": "madiba",
        },
        headers=HEADERS,
    )

    # Staff signup
    for i in range(1, 4):
        resStaff = requests.post(
            f"{HOST}/admin/staff/signup",
            json={
                "first_name": "Madiba",
                "last_name": "Hudson-Quansah",
                "position": "Waiter",
                "email": f"madiba{i}@gmail.com",
                "salary": 400.33,
                "password": "madiba",
            },
            headers=HEADERS,
        )


def parseResponse(res):
    return json.dumps(res.json(), indent=4)


class AdminTests(unittest.TestCase):
    def testAdminSignup(self):
        res = requests.post(
            f"{HOST}/admin/signup",
            json={"username": "madiba", "password": "madiba"},
            headers=HEADERS,
        )
        self.assertTrue(res.status_code == 200)
        self.assertIn("token", res.json())

    def testAdminLogin(self):
        res = requests.post(
            f"{HOST}/admin/login",
            json={"username": "madiba", "password": "madiba"},
            headers=HEADERS,
        )
        self.assertTrue(res.status_code == 200)
        self.assertIn("token", res.json())

    def testGetAllCustomers(self):
        res = requests.get(f"{HOST}/admin/customers/all", headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertEqual(
            res.json(),
            [
                {
                    "customer_id": 1,
                    "first_name": "Madiba",
                    "last_name": "Hudson-Quansah",
                    "email": "madiba@gmail.com",
                    "passhash": "$2y$10$wf4bvr8qjcam4vN4sYDtE.nld6nKyPEr7nQfyUtrg7ZAbn3OOvuuW",
                }
            ],
        )

    def testGetCustomerById(self):
        res = requests.get(f"{HOST}/admin/customers/id/1", headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertEqual(
            res.json(),
            {
                "customer_id": 1,
                "first_name": "Madiba",
                "last_name": "Hudson-Quansah",
                "email": "madiba@gmail.com",
                "passhash": "$2y$10$wf4bvr8qjcam4vN4sYDtE.nld6nKyPEr7nQfyUtrg7ZAbn3OOvuuW",
            },
        )

    def testAddCustomer(self):
        new_customer = {
            "first_name": "John",
            "last_name": "Doe",
            "email": "john@example.com",
            "password": "password123",
        }
        res = requests.post(
            f"{HOST}/admin/customers/add", json=new_customer, headers=HEADERS
        )
        self.assertTrue(res.status_code == 200)
        self.assertIn("customer_id", res.json())

    def testUpdateCustomer(self):
        updated_customer = {
            "customer_id": 1,
            "first_name": "Jane",
            "last_name": "Doe",
            "email": "jane@example.com",
            "password": "newpassword",
        }
        res = requests.put(
            f"{HOST}/admin/customers/update", json=updated_customer, headers=HEADERS
        )
        self.assertTrue(res.status_code == 200)
        self.assertEqual(res.json()["first_name"], "Jane")
        self.assertEqual(res.json()["email"], "jane@example.com")

    def testDeleteCustomer(self):
        res = requests.delete(f"{HOST}/admin/customers/delete/1", headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertEqual(res.json(), {"message": "Customer deleted successfully"})


class InventoryTests(unittest.TestCase):
    def testGetAllInventory(self):
        res = requests.get(f"{HOST}/admin/inventory/all", headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertEqual(
            res.json(),
            [
                {
                    "item_id": 1,
                    "name": "Tomato",
                    "quantity": 50,
                    "low_stock_threshold": 10,
                },
                {
                    "item_id": 2,
                    "name": "Onion",
                    "quantity": 30,
                    "low_stock_threshold": 5,
                },
                {
                    "item_id": 3,
                    "name": "Chicken Breast",
                    "quantity": 20,
                    "low_stock_threshold": 5,
                },
            ],
        )

    def testGetInventoryById(self):
        res = requests.get(f"{HOST}/admin/inventory/id/1", headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertEqual(
            res.json(),
            {"item_id": 1, "name": "Tomato", "quantity": 50, "low_stock_threshold": 10},
        )

    def testGetLowStockInventory(self):
        res = requests.get(f"{HOST}/admin/inventory/low-stock", headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertEqual(
            res.json(),
            [
                {
                    "item_id": 2,
                    "name": "Onion",
                    "quantity": 30,
                    "low_stock_threshold": 5,
                },
                {
                    "item_id": 3,
                    "name": "Chicken Breast",
                    "quantity": 20,
                    "low_stock_threshold": 5,
                },
            ],
        )


class MenuItemTests(unittest.TestCase):
    def testGetAllMenuItems(self):
        res = requests.get(f"{HOST}/admin/menu-items/all", headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertEqual(
            res.json(),
            [
                {
                    "item_id": 1,
                    "name": "Margherita Pizza",
                    "description": "Classic pizza with tomato sauce, mozzarella, and basil",
                    "price": 12.99,
                },
                {
                    "item_id": 2,
                    "name": "Chicken Alfredo Pasta",
                    "description": "Fettuccine pasta with grilled chicken in a creamy alfredo sauce",
                    "price": 15.49,
                },
                {
                    "item_id": 3,
                    "name": "House Salad",
                    "description": "Mixed greens, tomatoes, cucumbers, and balsamic vinaigrette",
                    "price": 8.99,
                },
            ],
        )

    def testGetMenuItemById(self):
        res = requests.get(f"{HOST}/admin/menu-items/id/1", headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertEqual(
            res.json(),
            {
                "item_id": 1,
                "name": "Margherita Pizza",
                "description": "Classic pizza with tomato sauce, mozzarella, and basil",
                "price": 12.99,
            },
        )


class OrderTests(unittest.TestCase):
    def testGetAllOrders(self):
        res = requests.get(f"{HOST}/admin/orders/all", headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertEqual(
            res.json(),
            [
                {
                    "order_id": 1,
                    "customer_id": 1,
                    "total_amount": 27.98,
                    "status": "Completed",
                    "placed_at": "2023-04-15T12:34:56",
                    "fulfilled_at": "2023-04-15T13:15:00",
                },
                {
                    "order_id": 2,
                    "customer_id": 2,
                    "total_amount": 42.75,
                    "status": "Pending",
                    "placed_at": "2023-04-16T09:20:00",
                },
            ],
        )

    def testGetOrderById(self):
        res = requests.get(f"{HOST}/admin/orders/id/1", headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertEqual(
            res.json(),
            {
                "order_id": 1,
                "customer_id": 1,
                "total_amount": 27.98,
                "status": "Completed",
                "placed_at": "2023-04-15T12:34:56",
                "fulfilled_at": "2023-04-15T13:15:00",
            },
        )

    def testDeleteOrder(self):
        res = requests.delete(f"{HOST}/admin/orders/deleteById/1", headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertEqual(res.json(), {"message": "Order deleted successfully"})


class StaffTests(unittest.TestCase):
    def testGetAllStaff(self):
        res = requests.get(f"{HOST}/admin/staff/all", headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertEqual(
            res.json(),
            [
                {
                    "staff_id": 1,
                    "first_name": "Madiba",
                    "last_name": "Hudson-Quansah",
                    "position": "Waiter",
                    "email": "madiba1@gmail.com",
                    "salary": 400.33,
                },
                {
                    "staff_id": 2,
                    "first_name": "Madiba",
                    "last_name": "Hudson-Quansah",
                    "position": "Waiter",
                    "email": "madiba2@gmail.com",
                    "salary": 400.33,
                },
                {
                    "staff_id": 3,
                    "first_name": "Madiba",
                    "last_name": "Hudson-Quansah",
                    "position": "Waiter",
                    "email": "madiba3@gmail.com",
                    "salary": 400.33,
                },
            ],
        )

    def testGetStaffById(self):
        res = requests.get(f"{HOST}/admin/staff/id/1", headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertEqual(
            res.json(),
            {
                "staff_id": 1,
                "first_name": "Madiba",
                "last_name": "Hudson-Quansah",
                "position": "Waiter",
                "email": "madiba1@gmail.com",
                "salary": 400.33,
            },
        )

    def testStaffSignup(self):
        new_staff = {
            "first_name": "Jane",
            "last_name": "Doe",
            "position": "Manager",
            "email": "jane@example.com",
            "salary": 50000,
            "password": "password123",
        }
        res = requests.post(
            f"{HOST}/admin/staff/signup", json=new_staff, headers=HEADERS
        )
        self.assertTrue(res.status_code == 200)
        self.assertIn("staff_id", res.json())

    def testStaffLogin(self):
        res = requests.post(
            f"{HOST}/admin/staff/login",
            json={"email": "madiba1@gmail.com", "password": "madiba"},
            headers=HEADERS,
        )
        self.assertTrue(res.status_code == 200)
        self.assertIn("token", res.json())

    def testUpdateStaff(self):
        updated_staff = {
            "staff_id": 1,
            "first_name": "John",
            "last_name": "Doe",
            "position": "Manager",
            "email": "john@example.com",
            "salary": 55000,
        }
        res = requests.put(
            f"{HOST}/admin/staff/update", json=updated_staff, headers=HEADERS
        )
        self.assertTrue(res.status_code == 200)
        self.assertEqual(res.json()["first_name"], "John")
        self.assertEqual(res.json()["email"], "john@example.com")

    def testDeleteStaff(self):
        res = requests.delete(f"{HOST}/admin/staff/delete/1", headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertEqual(res.json(), {"message": "Staff member deleted successfully"})


class TableTests(unittest.TestCase):
    def testGetAllTables(self):
        res = requests.get(f"{HOST}/admin/tables/all", headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertEqual(
            res.json(),
            [
                {"table_id": 1, "capacity": 4, "is_available": True},
                {"table_id": 2, "capacity": 6, "is_available": True},
                {"table_id": 3, "capacity": 2, "is_available": True},
            ],
        )

    def testAddTable(self):
        new_table = {"capacity": 8, "is_available": True}
        res = requests.post(f"{HOST}/admin/tables/add", json=new_table, headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertIn("table_id", res.json())

    def testUpdateTable(self):
        updated_table = {"table_id": 1, "capacity": 6, "is_available": False}
        res = requests.put(
            f"{HOST}/admin/tables/update", json=updated_table, headers=HEADERS
        )
        self.assertTrue(res.status_code == 200)
        self.assertEqual(res.json()["capacity"], 6)
        self.assertEqual(res.json()["is_available"], False)

    def testDeleteTable(self):
        res = requests.delete(f"{HOST}/admin/tables/delete/1", headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertEqual(res.json(), {"message": "Table deleted successfully"})


class UserTests(unittest.TestCase):
    def testUserSignup(self):
        new_user = {
            "first_name": "Madiba",
            "last_name": "Hudson-Quansah",
            "email": "madiba@gmail.com",
            "password": "madiba",
        }
        res = requests.post(f"{HOST}/user/signup", json=new_user, headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertIn("user_id", res.json())

    def testUserLogin(self):
        res = requests.post(
            f"{HOST}/user/login",
            json={"email": "madiba@gmail.com", "password": "madiba"},
            headers=HEADERS,
        )
        self.assertTrue(res.status_code == 200)
        self.assertIn("token", res.json())

    def testGetUserOrder(self):
        res = requests.get(f"{HOST}/user/order/id/1", headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertEqual(
            res.json(),
            {
                "order_id": 1,
                "customer_id": 1,
                "total_amount": 27.98,
                "status": "Completed",
                "placed_at": "2023-04-15T12:34:56",
                "fulfilled_at": "2023-04-15T13:15:00",
            },
        )

    def testAddUserOrder(self):
        new_order = {
            "customer_id": 1,
            "menu_items": [1, 2],
            "total_amount": 28.48,
            "status": "Pending",
        }
        res = requests.post(f"{HOST}/user/order/add", json=new_order, headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertIn("order_id", res.json())

    def testCancelUserOrder(self):
        res = requests.post(
            f"{HOST}/user/order/cancel", json={"order_id": 2}, headers=HEADERS
        )
        self.assertTrue(res.status_code == 200)
        self.assertEqual(res.json(), {"message": "Order cancelled successfully"})


class UserTests(unittest.TestCase):
    def testAddUserReservation(self):
        new_reservation = {
            "customer_id": 1,
            "table_id": 3,
            "party_size": 2,
            "reserved_at": "2023-05-01T19:00:00",
        }
        res = requests.post(
            f"{HOST}/user/reserve/add", json=new_reservation, headers=HEADERS
        )
        self.assertTrue(res.status_code == 200)
        self.assertIn("reservation_id", res.json())

    def testCancelUserReservation(self):
        res = requests.delete(f"{HOST}/user/reserve/cancel/1", headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertEqual(res.json(), {"message": "Reservation cancelled successfully"})

    def testGetAvailableTables(self):
        res = requests.get(f"{HOST}/user/tables/available", headers=HEADERS)
        self.assertTrue(res.status_code == 200)
        self.assertEqual(
            res.json(),
            [
                {"table_id": 1, "capacity": 4, "is_available": True},
                {"table_id": 2, "capacity": 6, "is_available": True},
                {"table_id": 3, "capacity": 2, "is_available": True},
            ],
        )


if __name__ == "__main__":
    setup()
    unittest.main()
