import { BASE_URL } from "./constants.js";

const fnameElem = document.getElementById("first_name");
const lnameElem = document.getElementById("last_name");
const emailElem = document.getElementById("email");
const passwordElem = document.getElementById("password");
const confirmPasswordElem = document.getElementById("confirmPassword");
const messageDiv = document.getElementById("message");
const emailError = document.getElementById("emailError");
const passwordError = document.getElementById("passwordError");
const confirmPasswordError = document.getElementById("confirmPasswordError");
const signupForm = document.getElementById("signupForm");

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
  }

  const json = await req.json();
  console.log({ json });
}

function validateSignupForm() {
  messageDiv.innerText = "";
  emailError.classList.add("hidden");
  passwordError.classList.add("hidden");
  confirmPasswordError.classList.add("hidden");

  const fname = fnameElem.value.trim();
  const lname = lnameElem.value.trim();
  const email = emailElem.value.trim();
  const password = passwordElem.value.trim();
  const confirmPassword = confirmPasswordElem.value.trim();

  console.log({ fname, lname, email, password, confirmPassword });
  if (!fname || !lname || !email || !password || !confirmPassword) {
    messageDiv.innerText = "All fields must be filled out.";
    return false;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    emailError.classList.remove("hidden");
    return false;
  }

  const passwordPattern = /^(?=.*[A-Z])(?=.*[0-9])(?=.{6,})/; // At least one uppercase and one number, minimum of 6 characters
  if (!passwordPattern.test(password)) {
    passwordError.classList.remove("hidden");
    return false;
  }

  if (password !== confirmPassword) {
    confirmPasswordError.classList.remove("hidden");
    return false;
  }

  //alert("Sign Up successful! You can now log in.");
  //document.getElementById("signupForm").reset();
  return true;
}

signupForm.onsubmit = signupUser;
