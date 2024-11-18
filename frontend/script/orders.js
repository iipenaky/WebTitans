import { BASE_URL } from "./constants.js";
import { sendBackToLogin } from "./utils.js";

async function getOrders() {
    const req = await fetch(`${BASE_URL}/admin/orders/all`, {
        credentials: "include",
    });

    if (!req.ok) {
        console.log({ req });
        if (req.status === 401) {
            sendBackToLogin();
        }
        throw new Error("Failed to fetch orders");
    }

    const json = await req.json();
    console.log({ json });
    return json;
}

(async function () {
    try {
        const orders = await getOrders();
        console.log({ orders });
        loadOrderTable(orders);
    } catch (e) {
        console.log(e);
    }
})();

// Populate the orders table
function loadOrderTable(orders) {
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
          <td class="py-2 px-4">$${parseFloat(order.total_amount).toFixed(2)}</td>
          <td class="py-2 px-4">${order.status}</td>
          <td class="py-2 px-4">
            <button id="viewButton" class="bg-blue-500 text-white py-1 px-2 rounded-md mr-2">View</button>
            <button id="editButton" onclick="editOrder(${order.order_id})" class="bg-yellow-500 text-white py-1 px-2 rounded-md mr-2">Edit</button>
            <button id="deleteButton" onclick="deleteOrder(${order.order_id})" class="bg-red-500 text-white py-1 px-2 rounded-md">Delete</button>
          </td>
        `;
        const viewButton = row.querySelector("#viewButton");
        const editButton = row.querySelector("#editButton");
        const deleteButton = row.querySelector("#deleteButton");
        viewButton.onclick = () => viewOrder(order.order_id, orders);
        editButton.onclick = () => editOrder(order.order_id, orders);
        deleteButton.onclick = () => deleteOrder(order.order_id, orders);

        tableBody.appendChild(row);
    });
}

// View Order Information
function viewOrder(orderId, orders) {
    const order = orders.find((o) => o.order_id === orderId);
    alert(
        `Order Info:\nCustomer ID: ${order.customer_id}\nStaff ID: ${
            order.staff_id ?? "N/A"
        }\nOrder Time: ${order.order_time}\nTotal Amount: $${order.total_amount}\nStatus: ${order.status}`,
    );
}

// Edit Order
function editOrder(orderId, orders) {
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
document.getElementById("editOrderForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const orderId = parseInt(document.getElementById("editOrderId").value);
    const order = orders.find((o) => o.order_id === orderId);

    order.customer_id = parseInt(document.getElementById("editCustomerId").value);
    order.staff_id = document.getElementById("editStaffId").value
        ? parseInt(document.getElementById("editStaffId").value)
        : null;
    order.order_time = document.getElementById("editOrderTime").value;
    order.total_amount = parseFloat(document.getElementById("editTotalAmount").value);
    order.status = document.getElementById("editStatus").value;

    loadOrderTable();
    closeEditModal();
});

// Delete Order
function deleteOrder(orderId, orders) {
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

document.getElementById("editCancel").onclick = closeEditModal;
