import { BASE_URL } from "./constants.js";
import { check401 } from "./utils.js";
import { sendBackTo } from "./utils.js";

const itemNameElement = document.getElementById("item_name");
const quantElem = document.getElementById("quantity");
const reorderlevelElement = document.getElementById("reorder_level");
const submitElem = document.getElementById("submit");

import { handleLogout } from "./utils.js";
const logoutButton = document.getElementById("logout");
logoutButton.onclick = handleLogout;

import { handleAdminLoggedIn } from "./utils.js";
handleAdminLoggedIn();

function validateAddInventory() {
    messageDiv.innerText = "";
    itemNameError.classList.add("hidden");
    reorderlevelError.classList.add("hidden");
    quantError.classList.add("hidden");

    const itemName = itemNameElement.value.trim();
    const quant = quantElem.value.trim();
    const reorderLevel = reorderlevelElement.value.trim();

    console.log({ itemName, reorderLevel, quant });
    if (!itemName || !reorderLevel || !quant) {
        messageDiv.innerText = "All fields must be filled out.";
        return false;
    }
    return true;
}

async function addInventory(e) {
    e.preventDefault();
    if (!validateAddInventory()) return;
    const formData = new FormData(submitElem);

    const req = await fetch(`${BASE_URL}/admin/inventory/add`, {
        credentials: "include",
        body: JSON.stringify(Object.fromEntries(formData)),
    });

    if (!req.ok) {
        check401();
        console.log({ req });
        throw new Error("Failed to add inventory");
    }

    const json = await req.json();
    const data = json.message;
    console.log({ data });
    alert(data);
    signupForm.reset();
    document.location.href = "./stock.html";
}

async function getinventory() {
    const req = await fetch(`${BASE_URL}/admin/inventory/all`, {
        credentials: "include",
    });

    if (!req.ok) {
        console.log({ req });
        if (req.status === 401) {
            sendBackTo();
        }
        throw new Error("Failed to fetch inventory");
    }

    const json = await req.json();
    const data = json;
    console.log({ data });
    return data;
}

async function restockByQty(id, qty) {
    const req = await fetch(`${BASE_URL}/admin/inventory/restock`, {
        method: "PUT",
        credentials: "include",
        body: JSON.stringify({ id, quantity: qty }),
    });

    if (!req.ok) {
        console.log({ req });
        if (req.status === 401) {
            sendBackTo();
        }
        throw new Error("Failed to restock");
    }

    const json = await req.json();
    const data = json;
    console.log({ data });
    return data;
}

async function getinventorybyId() {
    const req = await fetch(`${BASE_URL}/admin/inventory/id`, {
        credentials: "include",
    });

    if (!req.ok) {
        console.log({ req });
        if (req.status === 401) {
            sendBackTo();
        }
        throw new Error("Failed to fetch inventory");
    }

    const json = await req.json();
    const data = json;
    console.log({ data });
    return data;
}

async function loadInventoryTable() {
    try {
        const data = await getinventory();
        const tbody = document.getElementById("inventoryTableBody");
        tbody.innerHTML = "";
        data.forEach((item, key) => {
            const row = `
        <tr>
          <td class="border px-4 py-2">${item.inventory_id}</td>
          <td class="border px-4 py-2">${item.item_name}</td>
          <td class="border px-4 py-2">${item.quantity}</td>
          <td class="border px-4 py-2">${item.reorder_level}</td>
          <td class="border px-4 py-2">
            <button id = "restock-inventory-${item.inventory_id}" class="bg-red-500 text-white px-2 py-1 restock-btn">Restock</button>
          </td>
        </tr>
      `;
            tbody.insertAdjacentHTML("beforeend", row);
            document.getElementById(`restock-inventory-${item.inventory_id}`).onclick = async () => {
                try {
                    const res = await restockByQty(item.inventory_id, 10);
                    alert(`Restocked ${item.item_name} by 10 units`);
                    loadInventoryTable();
                } catch (error) {
                    console.error(error);
                }
            };
        });
    } catch (error) {
        console.error(error);
    }
}

// function openAddInventoryModal() {
//     document.getElementById("addInventoryModal").style.display = "block";
// }

// function closeAddInventoryModal() {
//     document.getElementById("addInventoryModal").classList.display = "none";
// }

// submitElem.addEventListener("submit", addInventory);

async function restockInventory(id, quantity) {
    try {
        // Validate the inputs
        if (!id || quantity <= 0) {
            console.error("Invalid inputs: ID and quantity must be provided and valid.");
            alert("Please provide a valid ID and quantity greater than 0.");
            return;
        }

        // Send the PUT request with the required data
        const req = await fetch(`${BASE_URL}/admin/inventory/restock`, {
            method: "PUT",
            credentials: "include",
            headers: {
                "Content-Type": "application/json", // Specify JSON content
            },
            credentials: "include",
            body: JSON.stringify({ id, quantity }), // Pass id and quantity as JSON
        });

        // Handle request response
        if (!req.ok) {
            console.error("Request failed:", req.status, req.statusText);
            const error = await req.json();
            console.error("Error details:", error);
            alert(`Failed to restock: ${error.error || "Unknown error occurred."}`);
            return error;
        }

        // Parse and return the response
        const responseData = await req.json();
        console.log("Restock successful:", responseData);
        alert(`Success: ${responseData.message}`);
        return responseData;
    } catch (error) {
        console.error("An error occurred:", error);
        alert("An unexpected error occurred. Please try again.");
    }
}
// Attach a click event listener to all "Restock" buttons
document.getElementById("inventory-table").addEventListener("click", async function (event) {
    // Check if the clicked element is a restock button
    if (event.target.classList.contains("restock-btn")) {
        // Get the inventory ID from the button's data-id attribute
        const id = event.target.getAttribute("data-id");
        if (!id) {
            console.error("Button does not have an associated ID.");
            return;
        }

        // Prompt the user to enter the quantity
        const quantity = parseInt(prompt("Enter the quantity to restock:"), 10);
        if (isNaN(quantity) || quantity <= 0) {
            alert("Invalid quantity. Please enter a positive number.");
            return;
        }

        // Call the restockInventory function with the ID and quantity
        const response = await restockInventory(id, quantity);

        // Handle the response
        if (response?.message) {
            alert(`Success: ${response.message}`);
            // Optionally, refresh the page or update the row
        } else if (response?.error) {
            alert(`Error: ${response.error}`);
        } else {
            alert("An unexpected error occurred.");
        }
    }
});

// document.getElementById("add-button").onclick = openAddInventoryModal;
// document.getElementById("close-form").onclick = closeAddInventoryModal;
//document.getElementById("restock-inventory").onclick = restockInventory;

loadInventoryTable();
