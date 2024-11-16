import { BASE_URL } from "./constants.js";

// Example form handling code (if needed for other forms)
const fnameElem = document.getElementById("first_name");
const lnameElem = document.getElementById("last_name");
const emailElem = document.getElementById("email");
const passwordElem = document.getElementById("password");
const confirmPasswordElem = document.getElementById("confirmPassword");
const signupForm = document.getElementById("signupForm");

// Check login status and redirect if necessary
document.addEventListener("DOMContentLoaded", () => {
  const orderNowButton = document.getElementById("orderNowBtn");

  if (orderNowButton) {
    orderNowButton.addEventListener("click", (event) => {
      if (!checkLoginStatus()) {
        event.preventDefault();
        alert("You must be logged in to place an order!");
        document.location.href = "./login.html";
      }
    });
  }
});

function checkLoginStatus() {
  // Check for a session token (localStorage example)
  const userToken = localStorage.getItem("userToken");
  return !!userToken; // Returns true if token exists
}

async function signupUser(e) {
  e.preventDefault();
  if (!validateSignupForm()) return;

  const formData = new FormData(signupForm);
  const req = await fetch(`${BASE_URL}/user/signup`, {
    method: "POST",
    body: JSON.stringify(Object.fromEntries(formData)),
  });

  if (!req.ok) {
    console.log({ req });
    return;
  }

  const json = await req.json();
  alert(json.message);
  signupForm.reset();
  setTimeout(() => {
    document.location.href = "./login.html";
  }, 1500);
}

function validateSignupForm() {
  const fname = fnameElem.value.trim();
  const lname = lnameElem.value.trim();
  const email = emailElem.value.trim();
  const password = passwordElem.value.trim();
  const confirmPassword = confirmPasswordElem.value.trim();

  if (!fname || !lname || !email || !password || !confirmPassword) {
    alert("All fields must be filled out.");
    return false;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    alert("Invalid email format.");
    return false;
  }

  const passwordPattern = /^(?=.*[A-Z])(?=.*[0-9])(?=.{6,})/;
  if (!passwordPattern.test(password)) {
    alert("Password must include an uppercase letter, a number, and be at least 6 characters.");
    return false;
  }

  if (password !== confirmPassword) {
    alert("Passwords do not match.");
    return false;
  }

  return true;
}

if (signupForm) {
  signupForm.onsubmit = signupUser;
}
