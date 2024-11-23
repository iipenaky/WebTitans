// // Initialize storage for orders and reservations
// import { readFromSessionStorage, writeToSessionStorage, handleUserLoggedIn } from "./utils.js";
// let orders = JSON.parse(localStorage.getItem("orders")) || [];
// let reservations = JSON.parse(localStorage.getItem("reservations")) || [];

// import { handleLogout } from "./utils.js";
// const logoutButton = document.getElementById("logout");
// logoutButton.onclick = handleLogout;

// function openOrderForm(foodName, foodImage) {
//     document.getElementById("formTitle").innerText = foodName;
//     document.getElementById("formImage").src = foodImage;
//     document.getElementById("orderForm").style.display = "block";
//     document.getElementById("overlay").style.display = "block";
// }

// function closeOrderForm() {
//     document.getElementById("orderForm").style.display = "none";
//     document.getElementById("overlay").style.display = "none";
//     document.getElementById("orderDetails").reset();
// }

// // Add order
// function addOrder(foodName, quantity, paymentMethod, orderType) {
//     const order = {
//         id: Date.now(),
//         name: foodName,
//         quantity: quantity,
//         paymentMethod: paymentMethod,
//         orderType: orderType,
//         status: "Pending",
//     };

//     orders.push(order);
//     localStorage.setItem("orders", JSON.stringify(orders));
//     updateOrdersTable("orders.html");
//     updateOrdersTable("allOrders.html");
// }

// // Add reservation
// function addReservation(name, date, time, guests) {
//     const reservation = {
//         id: Date.now(),
//         name: name,
//         date: date,
//         time: time,
//         guests: guests,
//     };

//     reservations.push(reservation);
//     localStorage.setItem("reservations", JSON.stringify(reservations));
//     updateReservationsTable("allReservations.html");
// }

// // Update Orders Table
// function updateOrdersTable(page) {
//     const tableId = page === "orders.html" ? "userOrdersTable" : "allOrdersTable";
//     const table = document.getElementById(tableId);

//     if (!table) return;

//     table.innerHTML = ""; // Clear table
//     orders.forEach((order) => {
//         const row = document.createElement("tr");
//         row.innerHTML = `
//       <td>${order.name}</td>
//       <td>${order.quantity}</td>
//       <td>${order.paymentMethod}</td>
//       <td>${order.orderType}</td>
//       <td>${order.status}</td>
//       <td>
//         <button onclick="deleteOrder(${order.id}, '${page}')">Delete</button>
//       </td>
//     `;
//         table.appendChild(row);
//     });
// }

// // Update Reservations Table
// function updateReservationsTable(page) {
//     const tableId = page === "allReservations.html" ? "reservationTableBody" : null;
//     const table = document.getElementById(tableId);

//     if (!table) return;

//     table.innerHTML = ""; // Clear table
//     reservations.forEach((reservation) => {
//         const row = document.createElement("tr");
//         row.innerHTML = `
//       <td>${reservation.name}</td>
//       <td>${reservation.date}</td>
//       <td>${reservation.time}</td>
//       <td>${reservation.guests}</td>
//       <td>
//         <button onclick="deleteReservation(${reservation.id}, '${page}')">Delete</button>
//       </td>
//     `;
//         table.appendChild(row);
//     });
// }

// // Delete order
// function deleteOrder(orderId, page) {
//     orders = orders.filter((order) => order.id !== orderId);
//     localStorage.setItem("orders", JSON.stringify(orders));
//     updateOrdersTable(page);
// }

// // Delete reservation
// function deleteReservation(reservationId, page) {
//     reservations = reservations.filter((reservation) => reservation.id !== reservationId);
//     localStorage.setItem("reservations", JSON.stringify(reservations));
//     updateReservationsTable(page);
// }

// // Submit orders (e.g., send to server or process further)
// function submitOrders() {
//     alert(`Submitting ${orders.length} orders.`);
//     orders = []; // Clear orders
//     localStorage.setItem("orders", JSON.stringify(orders));
//     updateOrdersTable("orders.html");
// }

// // On form submit for orders
// document.getElementById("orderDetails").addEventListener("submit", (e) => {
//     e.preventDefault();
//     const foodName = document.getElementById("formTitle").innerText;
//     const quantity = document.getElementById("quantity").value;
//     const paymentMethod = document.getElementById("paymentMethod").value;
//     const orderType = document.getElementById("orderType").value;

//     addOrder(foodName, quantity, paymentMethod, orderType);
//     closeOrderForm();
// });

// // On form submit for reservations
// document.getElementById("reservation-form")?.addEventListener("submit", (e) => {
//     e.preventDefault();
//     const name = document.getElementById("name").value;
//     const date = document.getElementById("date").value;
//     const time = document.getElementById("time").value;
//     const guests = document.getElementById("guests").value;

//     addReservation(name, date, time, guests);
// });

// document.addEventListener("DOMContentLoaded", () => {
//     // Redirect to login if user is not logged in
//     console.log("Here");
//     handleUserLoggedIn("login.html");
// });

// /**
//  * Checks if the user is logged in.
//  * @returns {boolean} True if logged in, false otherwise.
//  */
// function checkLoginStatus() {
//     const isLoggedIn = readFromSessionStorage("isLoggedIn");
//     return isLoggedIn === true;
// }

// function validateForm() {
//     const name = document.getElementById("name").value;
//     const phoneNumber = document.getElementById("phoneNumber").value;
//     const quantity = document.getElementById("quantity").value;
//     const paymentMethod = document.getElementById("paymentMethod").value;
//     const orderType = document.getElementById("orderType").value;

//     let isValid = true;

//     if (name === "") {
//         document.getElementById("nameError").classList.remove("hidden");
//         isValid = false;
//     } else {
//         document.getElementById("nameError").classList.add("hidden");
//     }

//     const phoneRegex = /^[0-9]{10}$/;
//     if (!phoneRegex.test(phoneNumber)) {
//         document.getElementById("phoneError").classList.remove("hidden");
//         isValid = false;
//     } else {
//         document.getElementById("phoneError").classList.add("hidden");
//     }

//     if (quantity === "" || quantity < 1) {
//         document.getElementById("quantityError").classList.remove("hidden");
//         isValid = false;
//     } else {
//         document.getElementById("quantityError").classList.add("hidden");
//     }

//     if (paymentMethod === "") {
//         document.getElementById("paymentError").classList.remove("hidden");
//         isValid = false;
//     } else {
//         document.getElementById("paymentError").classList.add("hidden");
//     }

//     if (orderType === "") {
//         document.getElementById("orderTypeError").classList.remove("hidden");
//         isValid = false;
//     } else {
//         document.getElementById("orderTypeError").classList.add("hidden");
//     }

//     if (isValid) {
//         alert("Thank you " + name + "! Your order has been placed successfully.");
//         closeOrderForm();
//     }

//     return isValid;
// }

// ///////////////////////// Order Form /////////////////////////
// async function submitOrder() {
//     const foodName = document.getElementById("formTitle").innerText;
//     const quantity = document.getElementById("quantity").value;
//     const paymentMethod = document.getElementById("paymentMethod").value;
//     const orderType = document.getElementById("orderType").value;

//     // Example data - Replace with actual customer and food IDs
//     const customerId = 1; // Replace with logged-in user ID
//     const menuItemId = 1; // Map `foodName` to actual ID from the menu

//     try {
//         const response = await fetch('orderSubmit.php', {
//             method: 'POST',
//             headers: { 'Content-Type': 'application/json' },
//             body: JSON.stringify({
//                 customer_id: customerId,
//                 menu_item_id: menuItemId,
//                 quantity: quantity,
//                 payment_method: paymentMethod,
//                 order_type: orderType,
//             }),
//         });

//         const result = await response.json();

//         if (result.success) {
//             alert('Order placed successfully!');
//             closeOrderForm();
//             updateOrdersTable(); // Refresh order pages
//         } else {
//             throw new Error(result.message);
//         }
//     } catch (error) {
//         alert('Error placing order: ' + error.message);
//     }
// }

// // Attach event listener to the form submit
// document.getElementById("orderDetails").addEventListener("submit", (e) => {
//     e.preventDefault();
//     submitOrder();
// });


// Open the order form with food details
function openOrderForm(foodName, foodImage) {
    document.getElementById("formTitle").innerText = foodName;
    document.getElementById("formImage").src = foodImage;
    document.getElementById("orderForm").style.display = "block";
}

// Close the order form
function closeOrderForm() {
    document.getElementById("orderForm").style.display = "none";
    document.getElementById("orderDetails").reset();
}

// Submit order to backend
async function submitOrder() {
    const foodName = document.getElementById("formTitle").innerText;
    const quantity = document.getElementById("quantity").value;
    const paymentMethod = document.getElementById("paymentMethod").value;
    const orderType = document.getElementById("orderType").value;

    // Example data - Replace with actual customer and menu item IDs
    const customerId = sessionStorage.getItem("user_id"); // Logged-in user ID
    const menuItemId = getMenuItemIdByName(foodName); // Map food name to menu item ID

    if (!customerId || !menuItemId) {
        alert("Invalid user or food selection.");
        return;
    }

    try {
        const response = await fetch("orderSubmit.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                customer_id: customerId,
                menu_item_id: menuItemId,
                quantity: quantity,
                payment_method: paymentMethod,
                order_type: orderType,
            }),
        });

        const result = await response.json();

        if (result.success) {
            alert("Order placed successfully!");
            closeOrderForm();
            updateOrdersTable(); // Refresh the user and admin orders
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        alert("Failed to place order: " + error.message);
    }
}

// Map food name to menu item ID (adjust based on your menu data)
function getMenuItemIdByName(foodName) {
    const menuItems = {
        "Margherita Pizza": 1,
        "Caesar Salad": 2,
        "Chocolate Cake": 3,
        // Add all menu items with their IDs
    };
    return menuItems[foodName] || null;
}

// Attach event listener to the order form
document.getElementById("orderDetails").addEventListener("submit", (e) => {
    e.preventDefault();
    submitOrder();
});
