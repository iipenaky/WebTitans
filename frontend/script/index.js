import { readFromSessionStorage, writeToSessionStorage, addElementToElementOnCondition } from "./utils.js";

(async function () {
    const req = await fetch("http://169.239.251.102:3341/~madiba.quansah/backend/src/index.php/health");
    if (!req.ok) {
        console.log({ req });
        return;
    }
    const json = await req.json();
    const data = json.status;
    console.log(`${data}`);
})();

document.addEventListener("DOMContentLoaded", () => {
    const orderNowButton = document.querySelector("a[href='menu.html']");

    orderNowButton.addEventListener("click", (e) => {
        e.preventDefault(); // Prevent default link behavior

        if (checkLoginStatus()) {
            // Redirect to menu page if logged in
            window.location.href = "menu.html";
        } else {
            // Redirect to login page if not logged in
            window.location.href = "login.html";
        }
    });
});

/**
 * Checks if the user is logged in.
 * @returns {boolean} True if logged in, false otherwise.
 */
function checkLoginStatus() {
    const isLoggedIn = readFromSessionStorage("isLoggedIn");
    return isLoggedIn === "true"; // Returns true if the user is logged in
}
