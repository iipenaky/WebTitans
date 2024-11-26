import { readFromSessionStorage, writeToSessionStorage, handleUserLoggedIn, handleError } from "./utils.js";
import { handleLogout } from "./utils.js";
import { BASE_URL } from "./constants.js";

// Handle user logout
const logoutButton = document.getElementById("logout");
logoutButton.onclick = handleLogout;

handleUserLoggedIn("login.html");

async function getMenuItems() {
    const res = await fetch(`${BASE_URL}/user/order/menu`, {
        credentials: "include",
    });

    if (!res.ok) {
        handleError(res);
    }

    const json = await res.json();
    return json;
}

let items = [];

function renderItems(items) {
    const itemsDiv = document.getElementById("items");
    itemsDiv.innerHTML = "";
    for (const item of items) {
        const { menu_item_id, name, image, quantity } = item;
        const itemDiv = document.createElement("div");
        const itemImage = document.createElement("img");
        itemImage.src = image;
        itemImage.alt = name;
        itemImage.style = "max-width: 10rem; border-radius: 10px";
        itemDiv.appendChild(itemImage);
        itemDiv.classList.add("menu-item");
        itemDiv.style = "border: 1px solid #ccc; padding: 10px; margin: 10px; border-radius: 10px";
        const itemQuant = document.createElement("h3");
        itemQuant.textContent = `x${quantity}`;

        itemDiv.appendChild(itemQuant);
        itemsDiv.appendChild(itemDiv);
        itemsDiv.onclick = () => {
            removeMenuItem(item);
        };
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
    renderItems(items);
}

function removeMenuItem(item) {
    const index = item.menu_item_id;
    items = items.filter((i) => i.menu_item_id !== index);
    renderItems(items);
}

async function submitOrder() {
    if (items.length == 0) {
        alert("No items in cart");
        return;
    }

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
        handleError(res);
    }

    items = [];
    renderItems(items);
    const json = await res.json();
    return json;
}

(async function () {
    const menuItems = await getMenuItems();
    displayMenuItems(menuItems);
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

// Attach event listener to the form submit
document.getElementById("orderDetails").addEventListener("submit", (e) => {
    e.preventDefault();
    // submitOrder();
});

//
function groupMenuItemsByCategory(menuItems) {
    return menuItems.reduce((categories, item) => {
        const { category } = item; // Ensure `category` is a property in your menu items
        if (!categories[category]) {
            categories[category] = [];
        }
        categories[category].push(item);
        return categories;
    }, {});
}

document.getElementById("complete").onclick = async () => {
    try {
        const req = await submitOrder();
    } catch (e) {
        console.error(e);
    }
};

function displayMenuItems(menuItems) {
    // Group menu items by category
    const groupedItems = groupMenuItemsByCategory(menuItems);

    // Get the menu_items container
    const menuItemsDiv = document.getElementById("menu_items");

    // Clear existing content
    menuItemsDiv.innerHTML = "";

    // Iterate over categories and their items
    Object.keys(groupedItems).forEach((category) => {
        // Create a category section
        const categorySection = document.createElement("div");
        categorySection.className = "category-section mb-8";

        // Add category title
        const categoryTitle = document.createElement("h2");
        categoryTitle.className = "text-2xl font-bold mb-4 text-white";
        categoryTitle.innerText = category;
        categorySection.appendChild(categoryTitle);

        // Add items under the category
        const itemsGrid = document.createElement("div");
        itemsGrid.className = "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6";

        groupedItems[category].forEach((item) => {
            const { menu_item_id, name, image, price, description } = item;

            // Create a div for the menu item
            const itemDiv = document.createElement("div");
            itemDiv.className = "menu-item bg-white p-4 rounded shadow-md";

            // Add menu item content
            itemDiv.innerHTML = `
                <img 
                    src="${image}" 
                    alt="${name}" 
                    class="w-full h-40 object-cover rounded-md mb-2"
                />
                <h3 class="text-lg font-bold mb-1">${name}</h3>
                <p class="text-sm text-gray-600 mb-1">${description}</p>
                <p class="text-lg font-bold text-blue-600 mb-2">$${price.toFixed(2)}</p>
            `;

            const orderButton = document.createElement("button");
            orderButton.className = `order-now-btn bg-blue-600 text-white px-4 py-2 rounded`;
            orderButton.textContent = "Order Now";
            orderButton.onclick = (e) => {
                e.preventDefault();
                const modal = document.getElementById("orderDetails");

                modal.onsubmit = () => {
                    const quantityElem = document.getElementById("quantity");
                    const quantity = parseInt(quantityElem.value);
                    if (quantityElem.value === "") {
                        alert("Empty fields");
                        return false;
                    }
                    let i = { ...item, quantity };
                    addMenuItem(i);
                    modal.onsubmit = null;
                    modal.reset();
                    closeOrderForm();
                };
                openOrderForm(name, image);
            };

            itemDiv.appendChild(orderButton);
            itemsGrid.appendChild(itemDiv);
        });

        categorySection.appendChild(itemsGrid);
        menuItemsDiv.appendChild(categorySection);
    });
}
