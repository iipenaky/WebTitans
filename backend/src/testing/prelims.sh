#!/usr/bin/env bash

HOST="http://localhost:8000"

# Create admin
curl --request POST \
  --url "$HOST/admin/signup" \
  --header 'Content-Type: application/json' \
  --data '{
  "username": "madiba",
  "password": "madiba"
}'

# Create customer
curl --request POST \
  --url "$HOST/user/signup" \
  --header 'Content-Type: application/json' \
  --data '{
  "first_name": "Madiba",
  "last_name": "Hudson-Quansah",
  "email": "madiba@gmail.com",
  "password": "madiba"
}'

# Create three staff
for i in {1..3}; do
  curl --request POST \
    --url "$HOST/admin/staff/signup" \
    --header 'Content-Type: application/json' \
    --data '{
  "first_name": "Madiba",
  "last_name": "Hudson-Quansah",
  "position": "Waiter",
  "email": "'"madiba$i@gmail.com"'",
  "salary": 400.33,
  "password": "madiba"
}'
done
