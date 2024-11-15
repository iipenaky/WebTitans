#!/usr/bin/env python3
import requests
from requests import get, post, put, delete
import json
import unittest

HOST = "http://localhost:8000"
HEADERS = {"Content-Type": "application/json"}

data = {
    "admin": {
        "customers": {
            "all": {
                "expected": [
                    {
                        "customer_id": 1,
                        "first_name": "Madiba",
                        "last_name": "Hudson-Quansah",
                        "email": "madiba@gmail.com",
                        "passhash": "$2y$10$wf4bvr8qjcam4vN4sYDtE.nld6nKyPEr7nQfyUtrg7ZAbn3OOvuuW",
                    }
                ]
            }
        }
    },
    "user": {
        "signup": {
            "input": {
                "first_name": "Madiba",
                "last_name": "Hudson-Quansah",
                "email": "madiba@gmail.com",
                "password": "madiba",
            }
        }
    },
}


def setup():
    # Admin setup
    requests.post(
        f"{HOST}/admin/signup",
        json={"username": "madiba", "password": "madiba"},
        headers=HEADERS,
    )

    # Customer setup
    requests.post(
        f"{HOST}/user/signup",
        json=data["user"]["signup"]["input"],
        headers=HEADERS,
    )

    # Staff setup
    for i in range(1, 4):
        requests.post(
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

    # Admin login
    res = requests.post(
        f"{HOST}/admin/login",
        json={"username": "madiba", "password": "madiba"},
    )
    print(res.headers["Set-Cookie"])
    print(parseResponse(res))
    HEADERS["Cookie"] = res.headers["Set-Cookie"]


def parseResponse(res):
    try:
        return json.dumps(res.json(), indent=4)
    except Exception:
        return str(res.text)


class AdminTests(unittest.TestCase):
    def testAllCustomers(self):
        res = get(f"{HOST}/admin/customers/all")
        self.assertEqual(res.status_code, 200)
        self.assertEqual(res.json(), data["admin"]["customers"]["all"]["expected"])

    def testCustomerById(self):
        res = get(f"{HOST}/admin/customers/id/1")
        self.assertEqual(res.status_code, 200)
        self.assertEqual(res.json(), data["admin"]["customers"]["all"]["expected"][0])

    def testAddCustomer(self):
        new_customer = {
            "first_name": "John",
            "last_name": "Doe",
            "email": "john.doe@example.com",
            "password": "password123",
        }
        res = post(f"{HOST}/admin/customers/add", json=new_customer, headers=HEADERS)
        self.assertEqual(res.status_code, 201)
        self.assertIn("customer_id", res.json())

    def testUpdateCustomer(self):
        updated_customer = {
            "customer_id": 1,
            "first_name": "MadibaUpdated",
            "last_name": "Hudson-QuansahUpdated",
            "email": "madiba.updated@gmail.com",
        }
        res = put(
            f"{HOST}/admin/customers/update", json=updated_customer, headers=HEADERS
        )
        self.assertEqual(res.status_code, 200)
        self.assertEqual(res.json()["message"], "Customer updated successfully")

    def testDeleteCustomer(self):
        res = delete(
            f"{HOST}/admin/customers/delete", json={"customer_id": 1}, headers=HEADERS
        )
        self.assertEqual(res.status_code, 200)
        self.assertEqual(res.json()["message"], "Customer deleted successfully")


class UserTests(unittest.TestCase):
    def testUserSignup(self):
        user_signup = {
            "first_name": "Jane",
            "last_name": "Doe",
            "email": "jane.doe@example.com",
            "password": "password123",
        }
        res = post(f"{HOST}/user/signup", json=user_signup, headers=HEADERS)
        self.assertEqual(res.status_code, 201)
        self.assertIn("user_id", res.json())

    def testUserLogin(self):
        user_login = {"email": "madiba@gmail.com", "password": "madiba"}
        res = post(f"{HOST}/user/login", json=user_login, headers=HEADERS)
        self.assertEqual(res.status_code, 200)
        self.assertIn("token", res.json())


class InventoryTests(unittest.TestCase):
    def testLowStockInventory(self):
        res = get(f"{HOST}/admin/inventory/low-stock", headers=HEADERS)
        self.assertEqual(res.status_code, 200)
        self.assertIsInstance(res.json(), list)


if __name__ == "__main__":
    setup()  # Ensure setup is run before tests
    unittest.main()
