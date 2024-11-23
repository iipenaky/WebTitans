import { readFromSessionStorage, writeToSessionStorage, handleUserLoggedIn } from "./utils.js";
import { handleLogout } from "./utils.js";

// Handle user logout
const logoutButton = document.getElementById("logout");
logoutButton.onclick = handleLogout;

// Open the order form
function openOrderForm(foodName, foodImage) {
    document.getElementById("formTitle").innerText = foodName;
    document.getElementById("formImage").src = foodImage;
    document.getElementById("orderForm").style.display = "block";
}

// Close the order form
function closeOrderForm() {
    document.getElementById("orderForm").style.display = "none";
    document.getElementById("orderDetails").reset();
}

// Submit an order to the backend
async function submitOrder() {
    const foodName = document.getElementById("formTitle").innerText;
    const quantity = document.getElementById("quantity").value;
    const paymentMethod = document.getElementById("paymentMethod").value;
    const orderType = document.getElementById("orderType").value;

    // Example: Replace with the actual user and menu data
    const customerId = sessionStorage.getItem("user_id"); // Retrieve from session storage
    const menuItemId = await getMenuItemIdByName(foodName);

    if (!menuItemId || !customerId) {
        alert("Error: Invalid food selection or user not logged in.");
        return;
    }

    try {
        const response = await fetch("orderSubmit.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                customer_id: customerId,
                menu_item_id: menuItemId,
                quantity: quantity,
                payment_method: paymentMethod,
                order_type: orderType,
            }),
        });

        const result = await response.json();

        if (result.success) {
            alert("Order placed successfully!");
            closeOrderForm();
            await refreshOrdersTable(); // Refresh the orders table
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        alert("Error placing order: " + error.message);
    }
}

// Map food names to menu item IDs (replace with backend call if needed)
async function getMenuItemIdByName(foodName) {
    // Example mapping (use backend endpoint for dynamic mapping)
    const menuItems = {
        "Lasagna": 1,
        "Croissant": 2,
        "Danish Pastry": 3,
        // Add all menu items here
    };
    return menuItems[foodName] || null;
}

// Refresh orders table (fetch data from backend)
async function refreshOrdersTable() {
    try {
        const response = await fetch("getOrders.php");
        const orders = await response.json();

        // Populate the orders table
        const ordersContainer = document.getElementById("ordersContainer");
        ordersContainer.innerHTML = "";

        orders.forEach((order) => {
            const orderDiv = document.createElement("div");
            orderDiv.innerHTML = `
                <h2>Order #${order.order_id}</h2>
                <p>Food: ${order.menu_item_name}</p>
                <p>Quantity: ${order.quantity}</p>
                <p>Status: ${order.status}</p>
            `;
            ordersContainer.appendChild(orderDiv);
        });
    } catch (error) {
        console.error("Error fetching orders:", error);
    }
}

// Attach event listener to the form submit
document.getElementById("orderDetails").addEventListener("submit", (e) => {
    e.preventDefault();
    submitOrder();
});

// Ensure user is logged in
document.addEventListener("DOMContentLoaded", () => {
    handleUserLoggedIn("login.html");
});
