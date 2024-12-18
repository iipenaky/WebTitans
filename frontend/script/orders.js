import { handleAdminLoggedIn } from "./utils.js";
handleAdminLoggedIn();

import { handleLogout, handleError } from "./utils.js";
const logoutButton = document.getElementById("logout");
logoutButton.onclick = handleLogout;

import { BASE_URL } from "./constants.js";

const getAllOrders = async () => {
    const res = await fetch(`${BASE_URL}/admin/orders/all`, {
        credentials: "include",
    });

    if (!res.ok) {
        await handleError(res);
    }

    const data = await res.json();
    return data;
};

const deleteOrder = async (orderId) => {
    const res = await fetch(`${BASE_URL}/admin/orders/deleteById/${orderId}`, {
        method: "DELETE",
        credentials: "include",
    });

    if (!res.ok) {
        await handleError(res);
    }

    const data = await res.json();
    return data;
};

const populateOrderTable = (order) => {
    const orderTable = document.getElementById("order-table");
    for (let o of order) {
        let { order_id, customer_id, staff_id, order_time, total_amount, status } = o;
        const tdClass = "border px-4 py-2";

        const orderRow = document.createElement("tr");
        const orderId = document.createElement("td");
        const customerId = document.createElement("td");
        const staffId = document.createElement("td");
        const orderTime = document.createElement("td");
        const orderAmount = document.createElement("td");
        const orderStatus = document.createElement("td");

        orderId.textContent = order_id;
        customerId.textContent = customer_id;
        staffId.textContent = staff_id;
        orderTime.textContent = order_time;
        orderAmount.textContent = total_amount;
        orderStatus.textContent = status;

        const actions = document.createElement("td");
        const viewButton = document.createElement("button");
        const deleteButton = document.createElement("button");
        viewButton.textContent = "View";
        deleteButton.textContent = "Delete";

        let i = 0;
        const colors = ["blue", "red"];
        for (let element of [viewButton, deleteButton]) {
            element.className = `bg-${colors[i]}-500 text-white px-2 py-1`;
            if (i !== 0) {
                element.style.marginLeft = "0.25rem";
            }
            actions.appendChild(element);
            i++;
        }

        const view = () => {
            alert(
                `Order Id: ${order_id}\nCustomer Id: ${customer_id}\nStaff ID: ${staff_id}\nOrder Time: ${order_time}\nAmout: ${total_amount}\nStatus: ${status}`,
            );
        };

        const del = async () => {
            if (confirm("Are you sure you want to delete this order?")) {
                try {
                    const res = await deleteOrder(order_id);
                    orderRow.remove();
                } catch (e) {
                    console.error(e);
                }
            }
        };

        viewButton.onclick = view;
        deleteButton.onclick = del;

        for (let element of [orderId, customerId, staffId, orderTime, orderAmount, orderStatus, actions]) {
            element.className = tdClass;
            orderRow.appendChild(element);
        }

        orderTable.children[1].appendChild(orderRow);
    }
};

async function refreshOrderTable() {
    const order = await getAllOrders();
    const orderTable = document.getElementById("order-table");
    orderTable.children[1].innerHTML = "";
    populateOrderTable(order);
}

(async function () {
    await refreshOrderTable();
})();
