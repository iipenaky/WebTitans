import { handleAdminLoggedIn } from "./utils.js";
import { handleEmail, validateFieldsFilled } from "./validation.js";
handleAdminLoggedIn();

import { handleLogout, handleError } from "./utils.js";
const logoutButton = document.getElementById("logout");
logoutButton.onclick = handleLogout;

import { BASE_URL } from "./constants.js";

const getAllReservations = async () => {
    const res = await fetch(`${BASE_URL}/admin/reservations/all`, {
        credentials: "include",
    });

    if (!res.ok) {
        await handleError(res);
    }

    const data = await res.json();
    console.log({ data });
    return data;
};

// const updateReservation = async (reservation) => {
//     const res = await fetch(`${BASE_URL}/admin/reservations/update`, {
//         method: "PUT",
//         body: JSON.stringify(reservation),
//         credentials: "include",
//     });

//     if (!res.ok) {
//         await handleError(res);
//     }

//     const data = await res.json();
//     console.log({ data });
//     return data;
// };

const deleteReservation = async (reservationId) => {
    const res = await fetch(`${BASE_URL}/admin/reservations/delete/${reservationId}`, {
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


const populateReservationTable = (reservation) => {
    const reservationTable = document.getElementById("reservation-table");
    for (let r of reservation) {
        let { reservation_id, customer_id, table_id, reservation_datetime, number_of_guests, special_requests} = r;
        const tdClass = "border px-4 py-2";

        const reservationRow = document.createElement("tr");
        const reservationID = document.createElement("td");
        const customerID = document.createElement("td");
        const tableID = document.createElement("td");
        const reservationDate = document.createElement("td");
        const numGuests = document.createElement("td");
        const requests = document.createElement("td");

        reservationID.textContent = reservation_id;
        customerID.textContent = customer_id;
        tableID.textContent = table_id;
        reservationDate.textContent = reservation_datetime;
        numGuests.textContent = number_of_guests;
        requests.textContent = special_requests;

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
                `Reservation ID: ${reservation_id}\nCustomer ID ${customer_id}\nTable ID: ${table_id}\nReservation Date: ${reservation_datetime}\nNumber of Guests: ${number_of_guests}\nSpecial Requests: $${special_requests}`,
            );
        };

        const del = async () => {
            if (confirm("Are you sure you want to delete this reservation?")) {
                try {
                    const res = await deleteReservation(reservation_id);
                    reservationRow.remove();
                    console.log({ res });
                } catch (e) {
                    console.error(e);
                }
            }
        };

        // const update = async () => {
        //     const modal = document.getElementById("updatereservationForm");

        //     const modalData = Object.fromEntries(new FormData(modal));
        //     modalData.reservation_id = reservation_id;
        //     modalData.firstName = first_name;
        //     modalData.lastName = last_name;
        //     modalData.email = email;
        //     modalData.salary = salary;
        //     modalData.position = position;
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
        //             reservation_id: reservation_id,
        //             first_name: fData.firstName,
        //             last_name: fData.lastName,
        //             email: fData.email,
        //             salary: fData.salary,
        //             position: fData.position,
        //         };
        //         console.log({ data });
        //         if (
        //             !validateFieldsFilled(Object.values(data)) ||
        //             !handleEmail(data.email) ||
        //             !handleSalary(data.salary)
        //         ) {
        //             return;
        //         }

        //         try {
        //             const res = await updatereservation(data);
        //             console.log({ res });
        //             await refreshreservationTable();
        //             alert("reservation updated successfully");
        //             modal.onsubmit = null;
        //             modal.reset();
        //         } catch (e) {
        //             console.error(e);
        //         }
        //         closeUpdatereservationModal();
        //     };

        //     openUpdatereservationModal();
        // };

        viewButton.onclick = view;
        deleteButton.onclick = del;
        editButton.onclick = update;

        for (let element of [
            reservationID,
            customerID,
            tableID,
            reservationDate,
            numGuests,
            requests
        ]) {
            element.className = tdClass;
            reservationRow.appendChild(element);
        }

        reservationTable.children[1].appendChild(reservationRow);
    }
};

// function openUpdatereservationModal() {
//     document.getElementById("updatereservationModal").style.display = "block";
// }

// function closeUpdatereservationModal() {
//     document.getElementById("updatereservationModal").style.display = "none";
// }



// document.getElementById("update-modal-close").onclick = closeUpdatereservationModal;

async function refreshReservationTable() {
    const reservation = await getAllReservations();
    const reservationTable = document.getElementById("reservation-table");
    reservationTable.children[1].innerHTML = "";
    populateReservationTable(reservation);
}

(async function () {
    await refreshReservationTable();
})();
