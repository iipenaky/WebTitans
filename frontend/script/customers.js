import { handleAdminLoggedIn } from "./utils.js";
import { handleEmail, validateFieldsFilled } from "./validation.js";
handleAdminLoggedIn();

import { handleLogout, handleError } from "./utils.js";
const logoutButton = document.getElementById("logout");
logoutButton.onclick = handleLogout;

import { BASE_URL } from "./constants.js";

const getAllCustomer = async () => {
    const res = await fetch(`${BASE_URL}/admin/customers/all`, {
        credentials: "include",
    });

    if (!res.ok) {
        await handleError(res);
    }

    const data = await res.json();
    console.log({ data });
    return data;
};

const updateCustomer = async (customer) => {
    const res = await fetch(`${BASE_URL}/admin/customers/update`, {
        method: "PUT",
        body: JSON.stringify(customer),
        credentials: "include",
    });

    if (!res.ok) {
        await handleError(res);
    }

    const data = await res.json();
    console.log({ data });
    return data;
};

const deleteCustomer = async (customerId) => {
    const res = await fetch(`${BASE_URL}/admin/customers/delete/${customerId}`, {
        method: "DELETE",
        credentials: "include",
    });

    if (!res.ok) {
        await handleError(res);
    }

    const data = await res.json();
    console.log({ data });
    return data;
};


const populateCustomerTable = (customer) => {
    const customerTable = document.getElementById("customer-table");
    for (let c of customer) {
        let { customer_id, first_name, last_name, email} = c;
        const tdClass = "border px-4 py-2";

        const customerRow = document.createElement("tr");
        const customerId = document.createElement("td");
        const customerFirstName = document.createElement("td");
        const customerLastName = document.createElement("td");
        const customerEmail = document.createElement("td");

        customerId.textContent = customer_id;
        customerFirstName.textContent = first_name;
        customerLastName.textContent = last_name;
        customerEmail.textContent = email;


        const actions = document.createElement("td");
        const viewButton = document.createElement("button");
        const editButton = document.createElement("button");
        const deleteButton = document.createElement("button");
        viewButton.textContent = "View";
        editButton.textContent = "Edit";
        deleteButton.textContent = "Delete";

        let i = 0;
        const colors = ["blue", "yellow", "red"];
        for (let element of [viewButton, editButton, deleteButton]) {
            element.className = `bg-${colors[i]}-500 text-white px-2 py-1`;
            if (i !== 0) {
                element.style.marginLeft = "0.25rem";
            }
            actions.appendChild(element);
            i++;
        }

        const view = () => {
            alert(
                `customer Name: ${first_name} ${last_name}\nEmail: ${email}`,
            );
        };

        const del = async () => {
            if (confirm("Are you sure you want to delete this customer?")) {
                try {
                    const res = await deleteCustomer(customer_id);
                    customerRow.remove();
                    console.log({ res });
                } catch (e) {
                    console.error(e);
                }
            }
        };

        const update = async () => {
            const modal = document.getElementById("updateCustomerForm");

            const modalData = Object.fromEntries(new FormData(modal));
            modalData.customer_id = customer_id;
            modalData.firstName = first_name;
            modalData.lastName = last_name;
            modalData.email = email;
            console.log({ modalData });

            // Populate form with existing modalData
            for (let key in modalData) {
                const input = document.getElementById(key);
                if (input) {
                    input.value = modalData[key];
                }
            }

            modal.onsubmit = async (e) => {
                e.preventDefault();
                const fData = Object.fromEntries(new FormData(modal));
                const data = {
                    customer_id: customer_id,
                    first_name: fData.firstName,
                    last_name: fData.lastName,
                    email: fData.email,
                };
                console.log({ data });
                if (
                    !validateFieldsFilled(Object.values(data)) ||
                    !handleEmail(data.email)
                ) {
                    return;
                }

                try {
                    const res = await updateCustomer(data);
                    console.log({ res });
                    await refreshCustomerTable();
                    alert("Cutomer updated successfully");
                    modal.onsubmit = null;
                    modal.reset();
                } catch (e) {
                    console.error(e);
                }
                closeUpdateCustomerModal();
            };

            openUpdateCustomerModal();
        };

        viewButton.onclick = view;
        deleteButton.onclick = del;
        editButton.onclick = update;

        for (let element of [
            customerId,
            customerFirstName,
            customerLastName,
            customerEmail,
            actions,
        ]) {
            element.className = tdClass;
            customerRow.appendChild(element);
        }

        customerTable.children[1].appendChild(customerRow);
    }
};



function openUpdateCustomerModal() {
    document.getElementById("updateCustomerModal").style.display = "block";
}

function closeUpdateCustomerModal() {
    document.getElementById("updateCustomerModal").style.display = "none";
}


document.getElementById("update-modal-close").onclick = closeUpdateCustomerModal;

async function refreshCustomerTable() {
    const customer = await getAllCustomer();
    const customerTable = document.getElementById("customer-table");
    customerTable.children[1].innerHTML = "";
    populateCustomerTable(customer);
}

(async function () {
    await refreshCustomerTable();
})();
