import { BASE_URL } from "./constants.js";
import { handleLogout, handleError, check401 } from "./utils.js";

import { handleAdminLoggedIn } from "./utils.js";
handleAdminLoggedIn();

const logoutButton = document.getElementById("logout");
logoutButton.onclick = handleLogout;

const getNum = async (path) => {
    const res = await fetch(`${BASE_URL}/admin/${path}/num`, {
        credentials: "include",
    });

    if (!res.ok) {
        check401();
        await handleError(res);
    }

    const data = await res.json();
    console.log({ data });
    return data.num;
};

(async function () {
    const numCustomers = await getNum("customers");
    const numStaff = await getNum("staff");
    const numOrders = await getNum("orders");
    document.getElementById("cust-num").innerText = numCustomers;
    document.getElementById("staff-num").innerText = numStaff;
    document.getElementById("pendingOrders").innerText = numOrders;
})();
