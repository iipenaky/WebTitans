import { BASE_URL } from "./constants.js";
import { sendBackToLogin } from "./utils.js";

async function getCustomer() {
    const req = await fetch(`${BASE_URL}/admin/customers/all`, {
        method: "GET",
        credentials: "include",
    });

    if (!req.ok) {
        console.log({ req });
        if (req.status === 401) {
            sendBackToLogin();
        }
        throw new Error("Failed to fetch customers");
    }

    const json = await req.json();
    const data = json;
    console.log({ data });
    return data;
}

async function getCustomerId() {
    const req = await fetch(`${BASE_URL}/admin/customers/id`, {
        method: "GET",
        credentials: "include",
    });

    if (!req.ok) {
        console.log({ req });
        if (req.status === 401) {
            sendBackToLogin();
        }
        throw new Error("Failed to fetch customer");
    }

    const json = await req.json();
    const data = json;
    console.log({ data });
    return data;
}

// Populate the customer table
async function loadCustomerTable() {
    try {
        const customers = await getCustomer();
        const tableBody = document.getElementById("customerTableBody");
        tableBody.innerHTML = ""; // Clear existing rows

        if (customers.length === 0) {
            tbody.innerHTML = "<tr><td colspan='6'>No customer data available.</td></tr>";
            return;
        }

        customers.forEach((customer) => {
            const row = document.createElement("tr");
            row.classList.add("border-t");
            row.innerHTML = `
          <td class="py-2 px-4">${customer.customer_id}</td>
          <td class="py-2 px-4">${customer.first_name}</td>
          <td class="py-2 px-4">${customer.last_name}</td>
          <td class="py-2 px-4">${customer.email}</td>
          <td class="py-2 px-4">
            <button id = "view-customer" class="bg-blue-500 text-white py-1 px-2 rounded-md mr-2">View</button>
            <button onclick="editCustomer(${customer.id})" class="bg-yellow-500 text-white py-1 px-2 rounded-md mr-2">Edit</button>
            <button onclick="deleteCustomer(${customer.id})" class="bg-red-500 text-white py-1 px-2 rounded-md">Delete</button>
          </td>
        `;
            tableBody.appendChild(row);
        });
    } catch (e) {
        console.log(e);
    }
}

// View Customer
async function viewCustomer() {
	const customer = await getCustomerId();
	alert(
		`Viewing Customer: \n\nFirst Name: ${customer.firstName}\nLast Name: ${customer.lastName}\nEmail: ${customer.email}\nAddress: ${customer.address}\nPhone: ${customer.phone}`,
	);
}

// Edit Customer continue
async function editCustomer() {
	const customer = await getCustomerId();
	document.getElementById("editCustomerId").value = customer.id;
	document.getElementById("editCustomerFirstName").value = customer.firstName;
	document.getElementById("editCustomerLastName").value = customer.lastName;
	document.getElementById("editCustomerEmail").value = customer.email;
	document.getElementById("editCustomerAddress").value = customer.address;
	document.getElementById("editCustomerPhone").value = customer.phone;
	document.getElementById("editModal").classList.remove("hidden");
}

// Save Edited Customer
document.getElementById("editCustomerForm").addEventListener("submit", function (event) {
    event.preventDefault();

    // Get the customer ID and the updated data from the form
    const customerId = parseInt(document.getElementById("editCustomerId").value);
    const customerData = {
        id: customerId,
        firstName: document.getElementById("editCustomerFirstName").value,
        lastName: document.getElementById("editCustomerLastName").value,
        email: document.getElementById("editCustomerEmail").value,
        address: document.getElementById("editCustomerAddress").value,
        phone: document.getElementById("editCustomerPhone").value,
    };

    // Make an AJAX request to the PHP endpoint that handles the customer update
    fetch(`${BASE_URL}/admin/customer/update`, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
        },
        credentials: "include",
        body: JSON.stringify(customerData), // Send the customer data as JSON
    })
        .then((response) => response.json())
        .then((data) => {
            // Handle the response from the backend
            if (data.success) {
                // If update was successful, reload the customer table
                loadCustomerTable();
                closeEditModal(); // Close the modal after the update
            } else {
                // Handle error case if the backend responds with failure
                alert("Failed to update customer data.");
            }
        })
        .catch((error) => {
            console.error("Error updating customer:", error);
            alert("An error occurred while updating the customer.");
        });
});

// Delete Customer work on this well
function deleteCustomer(customerId) {
    const confirmed = confirm("Are you sure you want to delete this customer?");
    if (confirmed) {
        const customerIndex = customers.findIndex((c) => c.id === customerId);
        customers.splice(customerIndex, 1);
        loadCustomerTable(); // Reload the table after deletion
    }
}

// Close Edit Modal
function closeEditModal() {
    document.getElementById("editModal").classList.add("hidden");
}
document.getElementById("view-customer").onclick = viewCustomer;
document.getElementById("edit-customer").onclick = editCustomer;
document.getElementById("delete-customer").onclick = deleteCustomer;
// Load the customer table initially
loadCustomerTable();
