import { BASE_URL } from "./constants.js";
const loginForm = document.getElementById("loginForm");

async function loginUser(e) {
  e.preventDefault();
  if (!validateLogin()) return;

  const formData = new FormData(loginForm);

  const req = await fetch(`${BASE_URL}/admin/login`, {
    method: "POST",
    body: JSON.stringify(Object.fromEntries(formData)),
  });

  if (!req.ok) {
    console.log({ req });
    alert("Login Error! Please try again");
  }

  const json = await req.json();
  console.log({ json });
  const msg = json.message;
  alert(msg);
  loginForm.reset();
  setTimeout(() => {
    document.location.href = "./dashboard.html";
  }, 1500);
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
