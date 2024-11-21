import { handleAdminLoggedIn } from "./utils.js";
// import { handleEmail, validateFieldsFilled } from "./validation.js";
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
    console.log({ data });
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
    console.log({ data });
    return data;
};


const populateOrderTable = (order) => {
    const orderTable = document.getElementById("order-table");
    for (let o of order) {
        let { order_id, customer_id, staff_id, order_date, amount,status} = o;
        const tdClass = "border px-4 py-2";

        const orderRow = document.createElement("tr");
        const orderId = document.createElement("td");
        const customerId = document.createElement("td");
        const staffId = document.createElement("td");
        const orderTime = document.createElement("td");
        const orderAmount = document.createElement("td");
        const orderStaus = document.createElement("td");

        orderId.textContent = order_id;
        customerId.textContent = customer_id;
        staffId.textContent = staff_id;
        orderTime.textContent = order_date;
        orderAmount.textContent = amount;
        orderStaus.textContent = status;


        const actions = document.createElement("td");
        const viewButton = document.createElement("button");
        // const editButton = document.createElement("button");
        const deleteButton = document.createElement("button");
        viewButton.textContent = "View";
        // editButton.textContent = "Edit";
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
                `Order Id: ${order_id}\nCustomer Id: ${customer_id}\nStaff ID: ${staff_id}\nOrder Time: ${order_date}\nAmout: ${amount}\nStatus: ${status}`,
            );
        };

        const del = async () => {
            if (confirm("Are you sure you want to delete this order?")) {
                try {
                    const res = await deleteOrder(order_id);
                    orderRow.remove();
                    console.log({ res });
                } catch (e) {
                    console.error(e);
                }
            }
        };

        // const update = async () => {
        //     const modal = document.getElementById("updateorderForm");

        //     const modalData = Object.fromEntries(new FormData(modal));
        //     modalData.order_id = order_id;
        //     modalData.firstName = first_name;
        //     modalData.lastName = last_name;
        //     modalData.email = email;
        //     console.log({ modalData });

        //     // Populate form with existing modalData
        //     for (let key in modalData) {
        //         const input = document.getElementById(key);
        //         if (input) {
        //             input.value = modalData[key];
        //         }
        //     }

        //     modal.onsubmit = async (e) => {
        //         e.preventDefault();
        //         const fData = Object.fromEntries(new FormData(modal));
        //         const data = {
        //             order_id: order_id,
        //             first_name: fData.firstName,
        //             last_name: fData.lastName,
        //             email: fData.email,
        //         };
        //         console.log({ data });
        //         if (
        //             !validateFieldsFilled(Object.values(data)) ||
        //             !handleEmail(data.email)
        //         ) {
        //             return;
        //         }

        //         try {
        //             const res = await updateorder(data);
        //             console.log({ res });
        //             await refreshorderTable();
        //             alert("Cutomer updated successfully");
        //             modal.onsubmit = null;
        //             modal.reset();
        //         } catch (e) {
        //             console.error(e);
        //         }
        //         closeUpdateorderModal();
        //     };

        //     openUpdateorderModal();
        // };

        viewButton.onclick = view;
        deleteButton.onclick = del;
        // editButton.onclick = update;

        for (let element of [
            orderId,
            customerId,
            staffId,
            orderTime,
            amount,
            status
        ]) {
            element.className = tdClass;
            orderRow.appendChild(element);
        }

        orderTable.children[1].appendChild(orderRow);
    }
};



// function openUpdateorderModal() {
//     document.getElementById("updateorderModal").style.display = "block";
// }

// function closeUpdateorderModal() {
//     document.getElementById("updateorderModal").style.display = "none";
// }


// document.getElementById("update-modal-close").onclick = closeUpdateorderModal;

async function refreshOrderTable() {
    const order = await getAllOrders();
    const orderTable = document.getElementById("order-table");
    orderTable.children[1].innerHTML = "";
    populateOrderTable(order);
}

(async function () {
    await refreshOrderTable();
})();
