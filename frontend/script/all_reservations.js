import { handleAdminLoggedIn } from "./utils.js";
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
        let { reservation_id, customer_id, table_id, reservation_datetime, number_of_guests, special_requests } = r;
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
        const deleteButton = document.createElement("button");
        viewButton.textContent = "View";
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
                `Reservation ID: ${reservation_id}\nCustomer ID ${customer_id}\nTable ID: ${table_id}\nReservation Date: ${reservation_datetime}\nNumber of Guests: ${number_of_guests}\nSpecial Requests: ${special_requests}`,
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

        viewButton.onclick = view;
        deleteButton.onclick = del;

        for (let element of [reservationID, customerID, tableID, reservationDate, numGuests, actions]) {
            element.className = tdClass;
            reservationRow.appendChild(element);
        }

        reservationTable.children[1].appendChild(reservationRow);
    }
};

async function refreshReservationTable() {
    const reservation = await getAllReservations();
    const reservationTable = document.getElementById("reservation-table");
    reservationTable.children[1].innerHTML = "";
    populateReservationTable(reservation);
}

(async function () {
    await refreshReservationTable();
})();
