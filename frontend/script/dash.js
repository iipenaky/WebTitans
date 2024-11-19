import { BASE_URL } from "./constants.js";
import { logout, sendBackTo, readFromSessionStorage } from "./utils.js";

(async function () {
    if (readFromSessionStorage("isLoggedIn") !== "true") {
        sendBackTo();
    }
})();

const logoutButton = document.getElementById("logout");
const handleLogout = async (e) => {
    e.preventDefault();
    try {
        const res = await logout();
    } catch (error) {
        console.log(error);
        alert("Failed to log out");
    }
};
logoutButton.onclick = handleLogout;
