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
    document.getElementById("cust-num").innerText = numCustomers;
    document.getElementById("staff-num").innerText = numStaff;
})();

// // Orders rate data for the chart
// const ordersData = {
//     labels: ["Week 1", "Week 2", "Week 3", "Week 4", "Week 5"],
//     datasets: [
//       {
//         label: "Orders Per Week",
//         data: [15, 22, 18, 24, 30], // Example order counts for each week
//         borderColor: "#4CAF50",
//         backgroundColor: "rgba(76, 175, 80, 0.2)",
//         borderWidth: 2,
//         fill: true,
//       },
//     ],
//   };

//   // Chart.js setup
//   const ctx = document.getElementById("ordersChart").getContext("2d");
//   const ordersChart = new Chart(ctx, {
//     type: "line",
//     data: ordersData,
//     options: {
//       responsive: true,
//       scales: {
//         y: {
//           beginAtZero: true,
//         },
//       },
//     },
//   });