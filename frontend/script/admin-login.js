import { BASE_URL } from "./constants.js";
import { readFromSessionStorage, writeToSessionStorage } from "./utils.js";
const loginForm = document.getElementById("loginForm");

async function loginUser(e) {
    e.preventDefault();
    if (!validateLogin()) return;

    const formData = new FormData(loginForm);

    const res = await fetch(`${BASE_URL}/admin/login`, {
        method: "POST",
        body: JSON.stringify(Object.fromEntries(formData)),
        credentials: "include",
    });

    if (!res.ok) {
        const err = await res.json();
        console.log({ err });
        alert("Login Error! Please try again");
        return;
    }

    const json = await res.json();
    const msg = json.message;
    writeToSessionStorage("isAdminLoggedIn", "true");
    loginForm.reset();
    document.location.href = "./dashboard.html";
}

function validateLogin() {
    let isValid = true;
    const password = document.getElementById("loginPassword").value.trim();

    document.getElementById("passwordError").classList.add("hidden");

    if (password === "") {
        document.getElementById("passwordError").classList.remove("hidden");
        isValid = false;
    }
    return isValid;
}

loginForm.onsubmit = loginUser;
