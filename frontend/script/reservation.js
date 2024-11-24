import { BASE_URL } from "./constants.js";
import {
    sendBackTo,
    readFromSessionStorage,
    handleUserLoggedIn,
    check401,
    handleError,
    htmlDateAndTimeTomysqlDatetime,
} from "./utils.js";

const date = document.getElementById("date");
const time = document.getElementById("time");
const guests = document.getElementById("guests");
const reservationform = document.getElementById("reservation-form");

import { handleLogout } from "./utils.js";
const logoutButton = document.getElementById("logout");
logoutButton.onclick = handleLogout;

handleUserLoggedIn();

async function getAvailableTables() {
    const res = await fetch(`${BASE_URL}/user/tables/available`, {
        method: "GET",
        credentials: "include",
    });

    if (!res.ok) {
        console.log({ res });
        check401(res);
        handleError(res);
    }

    const json = await res.json();
    console.log({ json });
    return json;
}

function populateTableSelect(tables) {
    const tableSelect = document.getElementById("table-select");
    for (const table of tables) {
        const option = document.createElement("option");
        option.value = table.table_id;
        option.textContent = `Table ${table.table_id} - Seats ${table.seating_capacity} - ${table.location}`;
        tableSelect.appendChild(option);
    }
}

async function reserveUser(e) {
    e.preventDefault();
    if (!validateReservationForm()) return;
    const userInfo = readFromSessionStorage("user");
    console.log({ userInfo });
    if (!userInfo) {
        alert("Please login to reserve a table");
        console.log("User not logged in");
        return;
    }
    let formData = Object.fromEntries(new FormData(reservationform));
    const payload = {
        customer_id: userInfo.customer_id,
        table_id: formData.table,
        reservation_datetime: htmlDateAndTimeTomysqlDatetime(formData.date, formData.time),
        number_of_guests: formData.guests,
        special_requests: formData.special_requests || "N\\A",
    };
    console.log({ payload });

    const req = await fetch(`${BASE_URL}/user/reserve/add`, {
        method: "POST",
        body: JSON.stringify(payload),
        credentials: "include",
    });

    if (!req.ok) {
        console.log({ req });
        check401(req);
        handleError(req);
        return;
    }

    const json = await req.json();
    const data = json.message;
    console.log({ data });
    alert(data);
    reservationform.reset();
    document.location.href = "./menu.html";
}

function validateReservationForm() {
    let isValid = true;

    const date = document.getElementById("date").value;
    const time = document.getElementById("time").value;
    const guests = document.getElementById("guests").value;
    const table = document.getElementById("table-select").value;

    // Clear previous error messages
    document.getElementById("dateError").classList.add("hidden");
    document.getElementById("timeError").classList.add("hidden");
    document.getElementById("guestsError").classList.add("hidden");
    //document.getElementById("tableError").classList.add("hidden");

    if (date === "") {
        document.getElementById("dateError").classList.remove("hidden");
        isValid = false;
    }

    if (time === "") {
        document.getElementById("timeError").classList.remove("hidden");
        isValid = false;
    }

    if (guests === "" || guests < 1 || guests > 20) {
        document.getElementById("guestsError").classList.remove("hidden");
        isValid = false;
    }

    if (!isValid) {
        alert("Please fill out all fields correctly.");
    }

    return isValid;
}

(async function () {
    const tables = await getAvailableTables();
    populateTableSelect(tables);
    if (tables.length === 0) {
        alert("No tables available for reservation");
        document.location.href = "./menu.html";
    }
})();

reservationform.onsubmit = reserveUser;
