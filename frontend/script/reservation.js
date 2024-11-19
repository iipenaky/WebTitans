import { BASE_URL } from "./constants.js";
import { sendBackTo, readFromSessionStorage } from "./utils.js";

const fullnameElem = document.getElementById("name");
const email = document.getElementById("email");
const date = document.getElementById("date");
const time = document.getElementById("time");
const guests = document.getElementById("guests");
const reservationform = document.getElementById("reservation-form");

(async function () {
    //if (readFromSessionStorage("isLoggedIn") !== "true") {
    //    alert("Please log in to make a reservation");
    //    sendBackTo();
    //}
})();

async function reserveUser(e) {
    e.preventDefault();
    if (!validateReservationForm()) return;
    const formData = new FormData(reservationform);

    const req = await fetch(`${BASE_URL}/user/reserve`, {
        method: "POST",
        body: JSON.stringify(Object.fromEntries(formData)),
        credentials: "include",
    });

    if (!req.ok) {
        console.log({ req });
        if (req.status === 401) {
            sendBackTo();
        }
        throw new Error("Failed to reserve table");
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

    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const date = document.getElementById("date").value;
    const time = document.getElementById("time").value;
    const guests = document.getElementById("guests").value;

    // Clear previous error messages
    document.getElementById("nameError").classList.add("hidden");
    document.getElementById("emailError").classList.add("hidden");
    document.getElementById("dateError").classList.add("hidden");
    document.getElementById("timeError").classList.add("hidden");
    document.getElementById("guestsError").classList.add("hidden");

    // Validate Name
    if (name === "") {
        document.getElementById("nameError").classList.remove("hidden");
        isValid = false;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        document.getElementById("emailError").classList.remove("hidden");
        isValid = false;
    }

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

    if (isValid) {
        alert("Your reservation has been submitted successfully!");
    } else {
        alert("Please fill out all  fields correctly.");
    }

    return isValid;
}

reservationform.onsubmit = reserveUser;
