import { BASE_URL } from "./constants.js";
import { readFromCookieStorage, readFromSessionStorage } from "./utils.js";

// Sample order data based on  SQL schema
const orders = [
  {
    order_id: 1,
    customer_id: 101,
    staff_id: 201,
    order_time: "2024-11-08T10:00",
    total_amount: 49.99,
    status: "Pending",
  },
  {
    order_id: 2,
    customer_id: 102,
    staff_id: 202,
    order_time: "2024-11-08T11:30",
    total_amount: 79.99,
    status: "Completed",
  },
  {
    order_id: 3,
    customer_id: 103,
    staff_id: null,
    order_time: "2024-11-08T12:45",
    total_amount: 29.99,
    status: "Cancelled",
  },
];

async function getOrders() {
  const req = await fetch(`${BASE_URL}/admin/orders/all`, {
    headers: {
      Cookie: readFromCookieStorage("PHPSESSID"),
    },
  });

  if (!req.ok) {
    console.log({ req });
    return;
  }

  const json = await req.json();
  console.log({ json });
  return json;
}

(async function () {
  const orders = await getOrders();
  console.log({ orders });
})();

// Populate the orders table
function loadOrderTable() {
  const tableBody = document.getElementById("orderTableBody");
  tableBody.innerHTML = ""; // Clear existing rows

  orders.forEach((order) => {
    const row = document.createElement("tr");
    row.classList.add("border-t");
    row.innerHTML = `
          <td class="py-2 px-4">${order.order_id}</td>
          <td class="py-2 px-4">${order.customer_id}</td>
          <td class="py-2 px-4">${order.staff_id ?? "N/A"}</td>
          <td class="py-2 px-4">${order.order_time}</td>
          <td class="py-2 px-4">$${order.total_amount.toFixed(2)}</td>
          <td class="py-2 px-4">${order.status}</td>
          <td class="py-2 px-4">
            <button onclick="viewOrder(${order.order_id})" class="bg-blue-500 text-white py-1 px-2 rounded-md mr-2">View</button>
            <button onclick="editOrder(${order.order_id})" class="bg-yellow-500 text-white py-1 px-2 rounded-md mr-2">Edit</button>
            <button onclick="deleteOrder(${order.order_id})" class="bg-red-500 text-white py-1 px-2 rounded-md">Delete</button>
          </td>
        `;
    tableBody.appendChild(row);
  });
}

// View Order Information
function viewOrder(orderId) {
  const order = orders.find((o) => o.order_id === orderId);
  alert(
    `Order Info:\nCustomer ID: ${order.customer_id}\nStaff ID: ${
      order.staff_id ?? "N/A"
    }\nOrder Time: ${order.order_time}\nTotal Amount: $${order.total_amount}\nStatus: ${order.status}`,
  );
}

// Edit Order
function editOrder(orderId) {
  const order = orders.find((o) => o.order_id === orderId);
  document.getElementById("editOrderId").value = order.order_id;
  document.getElementById("editCustomerId").value = order.customer_id;
  document.getElementById("editStaffId").value = order.staff_id ?? "";
  document.getElementById("editOrderTime").value = order.order_time;
  document.getElementById("editTotalAmount").value = order.total_amount;
  document.getElementById("editStatus").value = order.status;

  document.getElementById("editModal").classList.remove("hidden");
}

// Save Edited Order
document
  .getElementById("editOrderForm")
  .addEventListener("submit", function (event) {
    event.preventDefault();

    const orderId = parseInt(document.getElementById("editOrderId").value);
    const order = orders.find((o) => o.order_id === orderId);

    order.customer_id = parseInt(
      document.getElementById("editCustomerId").value,
    );
    order.staff_id = document.getElementById("editStaffId").value
      ? parseInt(document.getElementById("editStaffId").value)
      : null;
    order.order_time = document.getElementById("editOrderTime").value;
    order.total_amount = parseFloat(
      document.getElementById("editTotalAmount").value,
    );
    order.status = document.getElementById("editStatus").value;

    loadOrderTable();
    closeEditModal();
  });

// Delete Order
function deleteOrder(orderId) {
  const confirmed = confirm("Are you sure you want to delete this order?");
  if (confirmed) {
    const orderIndex = orders.findIndex((o) => o.order_id === orderId);
    orders.splice(orderIndex, 1);
    loadOrderTable();
  }
}

// Close Edit Modal
function closeEditModal() {
  document.getElementById("editModal").classList.add("hidden");
}

// Initial table load
loadOrderTable();
