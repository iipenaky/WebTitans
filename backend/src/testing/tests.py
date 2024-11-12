#!/usr/bin/env python3
import requests
from requests import get
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
    resAdmin = requests.post(
        f"{HOST}/admin/signup",
        json={"username": "madiba", "password": "madiba"},
        headers=HEADERS,
    )
    resCustomer = requests.post(
        f"{HOST}/user/signup",
        json=data["user"]["signup"]["input"],
        headers=HEADERS,
    )

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
        )


def parseResponse(res):
    return json.dumps(res, indent=4)


class AdminTests(unittest.TestCase):
    def testAllCustomers(self):
        res = get(f"{HOST}/admin/customers/all")
        d = res.json()
        self.assertTrue(res.status_code == 200)
        self.assertEqual(d, data["admin"]["customers"]["all"]["expected"])

    def testCustomerById(self):
        res = get(f"{HOST}/admin/customers/1")
        d = res.json()
        self.assertTrue(res.status_code == 200)
        self.assertEqual(d, data["admin"]["customers"]["all"]["expected"][0])


if __name__ == "__main__":
    unittest.main()
    # setup()
    # response = requests.get(f"{HOST}/admin/customers/all")
    # print(parseResponse(response.json()))
    # print(parseResponse(response))
