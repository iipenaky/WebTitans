import { handleLogout, handleUserLoggedIn } from "./utils.js";
const logoutButton = document.getElementById("logout");
logoutButton.onclick = handleLogout;

handleUserLoggedIn();
