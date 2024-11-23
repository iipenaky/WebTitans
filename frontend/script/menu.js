import { readFromSessionStorage, writeToSessionStorage, handleUserLoggedIn, handleError } from "./utils.js";
import { handleLogout } from "./utils.js";
import { BASE_URL } from "./constants.js";

// Handle user logout
const logoutButton = document.getElementById("logout");
logoutButton.onclick = handleLogout;

async function getMenuItems() {
    const res = await fetch(`${BASE_URL}/user/order/menu`, {
        credentials: "include",
    });

    if (!res.ok) {
        console.log({ res });
        handleError(res);
    }

    const json = await res.json();
    console.log({ json });
    return json;
}

let items = [];

function renderItems(items) {
    const itemsDiv = document.getElementById("items");
    for (const item of items) {
        const { menu_item_id, name, image, quantity } = item;
        const itemDiv = document.createElement("div");
        const itemImage = document.createElement("img");
        itemImage.src = image;
        itemImage.alt = name;
        itemImage.style = "max-width: 100%; boreder-radius: 10px";
        itemDiv.appendChild(itemImage);
        itemDiv.style = "border: 1px solid #ccc; padding: 10px; margin: 10px; border-radius: 10px";
        itemsDiv.appendChild(itemDiv);
    }
    items = items.sort((a, b) => a.menu_item_id - b.menu_item_id);
}

function addMenuItem(item) {
    items.push({
        menu_item_id: item.menu_item_id,
        name: item.name,
        image: item.image,
        quantity: item.quantity,
    });
}

function removeMenuItem(item) {
    const index = item.menu_item_id;
    items.splice(index, 1);
}

async function submitOrder() {
    const user = readFromSessionStorage("user");
    const { customer_id, first_name, last_name, email } = user;
    const order = {
        order: {
            customer_id,
        },
        order_details: items.map((i) => ({ menu_item_id: i.menu_item_id, quantity: i.quantity })),
    };
    const res = await fetch(`${BASE_URL}/user/order/add`, {
        method: "POST",
        credentials: "include",
        body: JSON.stringify(order),
    });

    if (!res.ok) {
        console.log({ res });
        handleError(res);
    }

    const json = await res.json();
    console.log({ json });
    return json;
}

(async function () {
    const menuItems = await getMenuItems();
})();

// Open the order form
function openOrderForm(foodName, foodImage) {
    document.getElementById("formTitle").innerText = foodName;
    document.getElementById("formImage").src = foodImage;
    document.getElementById("orderForm").style.display = "block";
}

// Attach to the global window object for inline attributes (optional if needed)
window.openOrderForm = openOrderForm;

// Close the order form
function closeOrderForm() {
    document.getElementById("orderForm").style.display = "none";
    document.getElementById("orderDetails").reset();
}

// Attach to the global window object for inline attributes (optional if needed)
window.closeOrderForm = closeOrderForm;

// Map food names to menu item IDs (replace with backend call if needed)
async function getMenuItemIdByName(foodName) {
    // Example mapping (use backend endpoint for dynamic mapping)
    const menuItems = {
        Lasagna: 1,
        Croissant: 2,
        "Danish Pastry": 3,
        "Blueberry Muffin": 4, // Ensure exact match
        "Classic Scone": 5,
        Bagel: 6,
        Pretzel: 7,
        "Spaghetti Bolognese": 8,
        "Grilled Chicken": 9,
        "Beef Tacos": 10,
        "Grilled Salmon": 11,
        "Classic Cheeseburger": 12,
        "Margherita Pizza": 13,
        Cheesecake: 14,
        "Chocolate Brownie": 15,
        "Vanilla Ice Cream": 16,
        Pavlova: 17,
        Tiramisu: 18,
        "Chocolate Cupcake": 19,
        "Chocolate Chip Cookies": 20,
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

// Add event listeners to "Order Now" buttons
document.addEventListener("DOMContentLoaded", () => {
    const orderButtons = document.querySelectorAll("[data-order-button]");
    orderButtons.forEach((button) => {
        button.addEventListener("click", () => {
            const foodName = button.getAttribute("data-food-name");
            const foodImage = button.getAttribute("data-food-image");
            openOrderForm(foodName, foodImage);
        });
    });

    // Ensure user is logged in
    handleUserLoggedIn("login.html");
});

// Attach event listener to the form submit
document.getElementById("orderDetails").addEventListener("submit", (e) => {
    e.preventDefault();
    submitOrder();
});
