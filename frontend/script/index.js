(async function () {
  const req = await fetch(
    "http://169.239.251.102:3341/~madiba.quansah/backend/src/index.php/health",
  );
  const json = await req.json();
  const data = json.status;
  console.log(`${data}`);
})();

document.addEventListener("DOMContentLoaded", () => {
  const orderNowButton = document.querySelector("a[href='menu.html']");

  orderNowButton.addEventListener("click", (e) => {
    e.preventDefault(); // Prevent the default navigation behavior
    
    // Simulated check for login status
    const isLoggedIn = checkLoginStatus();

    if (isLoggedIn) {
      // Redirect to menu page if logged in
      window.location.href = "menu.html";
    } else {
      // Redirect to login page if not logged in
      window.location.href = "login.html";
    }
  });
});

/**
 * Simulated function to check login status.
 * Replace with actual login check logic, e.g., checking cookies, localStorage, or an API call.
 * @returns {boolean} - true if logged in, false otherwise
 */
function checkLoginStatus() {
  // Example: Check for a specific item in localStorage
  const userToken = localStorage.getItem("userToken");
  return !!userToken; // Return true if token exists, false otherwise
}
