import { BASE_URL } from "./constants.js";
const loginForm = document.getElementById("loginForm");

async function loginUser(e) {
    e.preventDefault();
    if (!validateLogin()) return;

    const formData = new FormData(loginForm);

    const req = await fetch(`${BASE_URL}/user/login`, {
        method: "POST",
        body: JSON.stringify(Object.fromEntries(formData)),
    });

    if (!req.ok) {
        console.log({ req });
        alert("Login Error! Please try again");
        return;
    }

    const json = await req.json();
    console.log({ json });

    // Save login state (e.g., token or flag) to localStorage
    localStorage.setItem("isLoggedIn", "true");

    // Redirect to the menu page after successful login
    window.location.href = "menu.html";
}

function validateLogin() {
    let isValid = true;
    const email = document.getElementById("loginEmail").value.trim();
    const password = document.getElementById("loginPassword").value.trim();

    document.getElementById("emailError").classList.add("hidden");
    document.getElementById("passwordError").classList.add("hidden");

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        document.getElementById("emailError").classList.remove("hidden");
        isValid = false;
    }

    if (password === "") {
        document.getElementById("passwordError").classList.remove("hidden");
        isValid = false;
    }
    return isValid;
}

loginForm.onsubmit = loginUser;
