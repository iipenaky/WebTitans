// Initialize storage for orders and reservations
let orders = JSON.parse(localStorage.getItem('orders')) || [];
let reservations = JSON.parse(localStorage.getItem('reservations')) || [];

// Open the order form
function openOrderForm(foodName, foodImage) {
  document.getElementById('formTitle').innerText = foodName;
  document.getElementById('formImage').src = foodImage;
  document.getElementById('orderForm').style.display = 'block';
}

// Close the order form
function closeOrderForm() {
  document.getElementById('orderForm').style.display = 'none';
  document.getElementById('orderDetails').reset();
}

// Add order
function addOrder(foodName, quantity, paymentMethod, orderType) {
  const order = {
    id: Date.now(),
    name: foodName,
    quantity: quantity,
    paymentMethod: paymentMethod,
    orderType: orderType,
    status: 'Pending',
  };

  orders.push(order);
  localStorage.setItem('orders', JSON.stringify(orders));
  updateOrdersTable('orders.html');
  updateOrdersTable('allOrders.html');
}

// Add reservation
function addReservation(name, date, time, guests) {
  const reservation = {
    id: Date.now(),
    name: name,
    date: date,
    time: time,
    guests: guests,
  };

  reservations.push(reservation);
  localStorage.setItem('reservations', JSON.stringify(reservations));
  updateReservationsTable('allReservations.html');
}

// Update Orders Table
function updateOrdersTable(page) {
  const tableId = page === 'orders.html' ? 'userOrdersTable' : 'allOrdersTable';
  const table = document.getElementById(tableId);

  if (!table) return;

  table.innerHTML = ''; // Clear table
  orders.forEach((order) => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${order.name}</td>
      <td>${order.quantity}</td>
      <td>${order.paymentMethod}</td>
      <td>${order.orderType}</td>
      <td>${order.status}</td>
      <td>
        <button onclick="deleteOrder(${order.id}, '${page}')">Delete</button>
      </td>
    `;
    table.appendChild(row);
  });
}

// Update Reservations Table
function updateReservationsTable(page) {
  const tableId = page === 'allReservations.html' ? 'reservationTableBody' : null;
  const table = document.getElementById(tableId);

  if (!table) return;

  table.innerHTML = ''; // Clear table
  reservations.forEach((reservation) => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${reservation.name}</td>
      <td>${reservation.date}</td>
      <td>${reservation.time}</td>
      <td>${reservation.guests}</td>
      <td>
        <button onclick="deleteReservation(${reservation.id}, '${page}')">Delete</button>
      </td>
    `;
    table.appendChild(row);
  });
}

// Delete order
function deleteOrder(orderId, page) {
  orders = orders.filter((order) => order.id !== orderId);
  localStorage.setItem('orders', JSON.stringify(orders));
  updateOrdersTable(page);
}

// Delete reservation
function deleteReservation(reservationId, page) {
  reservations = reservations.filter((reservation) => reservation.id !== reservationId);
  localStorage.setItem('reservations', JSON.stringify(reservations));
  updateReservationsTable(page);
}

// Submit orders (e.g., send to server or process further)
function submitOrders() {
  alert(`Submitting ${orders.length} orders.`);
  orders = []; // Clear orders
  localStorage.setItem('orders', JSON.stringify(orders));
  updateOrdersTable('orders.html');
}

// On form submit for orders
document.getElementById('orderDetails').addEventListener('submit', (e) => {
  e.preventDefault();
  const foodName = document.getElementById('formTitle').innerText;
  const quantity = document.getElementById('quantity').value;
  const paymentMethod = document.getElementById('paymentMethod').value;
  const orderType = document.getElementById('orderType').value;

  addOrder(foodName, quantity, paymentMethod, orderType);
  closeOrderForm();
});

// On form submit for reservations
document.getElementById('reservation-form')?.addEventListener('submit', (e) => {
  e.preventDefault();
  const name = document.getElementById('name').value;
  const date = document.getElementById('date').value;
  const time = document.getElementById('time').value;
  const guests = document.getElementById('guests').value;

  addReservation(name, date, time, guests);
});
