import { handleLogout, handleUserLoggedIn } from "./utils.js";
const logoutButton = document.getElementById("logout");
logoutButton.onclick = handleLogout;

handleUserLoggedIn();

// Fetch and display user-specific orders
async function fetchOrders() {
    try {
        const response = await fetch('getOrders.php?user_id=1'); // Replace with dynamic user ID
        const orders = await response.json();

        // Clear the existing orders
        const ordersContainer = document.getElementById("ordersContainer");
        ordersContainer.innerHTML = '';

        orders.forEach(order => {
            const orderDiv = document.createElement('div');
            orderDiv.classList.add('order');
            orderDiv.innerHTML = `
                <h2>Order #${order.order_id}</h2>
                <p>Food: ${order.menu_item_name}</p>
                <p>Quantity: ${order.quantity}</p>
                <p>Status: ${order.status}</p>
            `;
            ordersContainer.appendChild(orderDiv);
        });
    } catch (error) {
        console.error('Failed to fetch orders:', error);
    }
}

// Call fetchOrders() on page load
document.addEventListener('DOMContentLoaded', fetchOrders);
