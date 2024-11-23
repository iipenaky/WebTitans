import { handleLogout, handleUserLoggedIn, handleError, readFromSessionStorage } from "./utils.js";
import { BASE_URL } from "./constants.js";
const logoutButton = document.getElementById("logout");
logoutButton.onclick = handleLogout;

handleUserLoggedIn();

async function getCustomerOrders() {
    const user = readFromSessionStorage("user");
    const { customer_id, first_name, last_name, email } = user;
    const req = await fetch(`${BASE_URL}/user/order/id/${customer_id}`, {
        credentials: "include",
    });

    if (!req.ok) {
        handleError(req);
        return;
    }

    const json = await req.json();
    console.log({ json });
    return json;
}

async function getCustomerReservations() {
    const user = readFromSessionStorage("user");
    const { customer_id, first_name, last_name, email } = user;
    const req = await fetch(`${BASE_URL}/user/reserve/id/${customer_id}`, {
        credentials: "include",
    });

    if (!req.ok) {
        handleError(req);
        return;
    }

    const json = await req.json();
    console.log({ json });
    return json;
}

async function cancelOrder(order) {
    const req = await fetch(`${BASE_URL}/user/order/cancel`, {
        method: "POST",
        body: JSON.stringify(order),
        credentials: "include",
    });

    if (!req.ok) {
        handleError(req);
        return;
    }

    const json = await req.json();
    console.log({ json });
    return json;
}

async function cancelReservation(reservation_id) {
    const req = await fetch(`${BASE_URL}/user/reserve/cancel/${reservation_id}`, {
        method: "DELETE",
        credentials: "include",
    });

    if (!req.ok) {
        handleError(req);
        return;
    }

    const json = await req.json();
    console.log({ json });
    return json;
}

function renderOrders(orders, container) {
    const user = readFromSessionStorage("user");
    const { customer_id, first_name, last_name, email } = user;
    const orderGroups = {};
    for (let order of orders) {
        const orderDiv = document.createElement("div");
        let { order_id, name, total_amount, price, quantity } = order;
        orderGroups[order_id] = orderGroups[order_id] || [];
        total_amount = parseFloat(total_amount).toFixed(2);
        orderDiv.innerHTML = `
                    <h2 class="text-xl font-bold">${name}</h2>
                        <p>Price: $${price}</p>
                        <p>Quantity: ${quantity}</p>
`;
        orderDiv.className = "order";
        orderGroups[order_id].push({
            orderObj: order,
            orderDiv,
        });
    }

    for (let order_id in orderGroups) {
        const orderGroup = orderGroups[order_id];
        const { status, total_amount, staff_id, order_time } = orderGroup[0].orderObj;
        const orderDiv = document.createElement("div");
        orderDiv.className = "order";
        orderDiv.innerHTML = `
<h2 class="text-xl font-bold">Order #${order_id}</h2>
<p>Status: ${status}</p>
<p>Total amount: $${total_amount}</p>
`;
        const orderGroupContainer = document.createElement("div");
        orderGroupContainer.className = "mt-4 grid grid-cols-1 gap-2";
        for (let { orderObj, orderDiv } of orderGroup) {
            orderGroupContainer.appendChild(orderDiv);
        }
        const cancelButton = document.createElement("button");
        const order = {
            order_id,
            staff_id,
            customer_id,
            order_time,
            total_amount,
        };
        cancelButton.textContent = "Cancel";
        cancelButton.className = "bg-red-600 text-white px-4 py-1 rounded mt-2";
        cancelButton.onclick = async () => {
            if (confirm("Are you sure you want to cancel this order?")) {
                try {
                    const req = await cancelOrder(order);
                    console.log({ req });
                    await refreshTable();
                } catch (error) {
                    console.error(error);
                }
            }
        };

        orderDiv.appendChild(orderGroupContainer);
        orderDiv.appendChild(cancelButton);
        container.appendChild(orderDiv);
    }
    console.log({ orderGroups });
}

function renderReservations(reservations, container) {
    for (let reservation of reservations) {
        const reservationDiv = document.createElement("div");
        const { reservation_id, number_of_guests, reservation_datetime, special_requests, table_id } = reservation;
        reservationDiv.innerHTML = `
<h2 class="text-xl font-bold">Reservation #${reservation_id}</h2>
<p>Number of guests: ${number_of_guests}</p>
<p>Date: ${reservation_datetime}</p>
<p>Special requests: ${special_requests}</p>
<p>Table ID: ${table_id}</p>
`;
        const cancelButton = document.createElement("button");
        cancelButton.className = "bg-red-600 text-white px-4 py-1 rounded mt-2";
        cancelButton.textContent = "Cancel";
        cancelButton.onclick = async () => {
            if (confirm("Are you sure you want to cancel this reservation?")) {
                try {
                    const req = await cancelReservation(reservation_id);
                    console.log({ req });
                    await refreshTable();
                } catch (error) {
                    console.error(error);
                }
            }
        };

        reservationDiv.className = "order";
        reservationDiv.appendChild(cancelButton);
        container.appendChild(reservationDiv);
    }
}

async function refreshTable() {
    const ordersContainer = document.getElementById("ordersContainer");
    ordersContainer.innerHTML = "";
    const messageDiv = document.getElementById("message");

    const orders = await getCustomerOrders();
    const reservations = await getCustomerReservations();

    if (orders.length === 0 && reservations.length === 0) {
        ordersContainer.innerHTML = '<p class="text-center text-gray-400">No reservations or orders found.</p>';
        return;
    }
    renderOrders(orders, ordersContainer);
    renderReservations(reservations, ordersContainer);
}

(async function () {
    await refreshTable();
})();
