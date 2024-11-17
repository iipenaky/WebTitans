import { BASE_URL } from "./constants.js";

  async function getinventory() {
  
    const req = await fetch(`${BASE_URL}/admin/inventory/all`,);
  
    if (!req.ok) {
      console.log({ req });
    }
  
    const json = await req.json();
    const data = json;
    console.log({ data });
    return data;
  }
  (async function () {
    const data = await getinventory();
  })();

  async function loadReservationTable() {
    const tableBody = document.getElementById('reservationTableBody');
    tableBody.innerHTML = ''; // Clear existing rows

    reservations.forEach(reservation => {
      const row = document.createElement('tr');
      row.classList.add('border-t');
      row.innerHTML = `
        <td class="py-2 px-4">${reservation.reservation_id}</td>
        <td class="py-2 px-4">${reservation.position_id}</td>
        <td class="py-2 px-4">${reservation.customer_id}</td>
        <td class="py-2 px-4">${reservation.table_id}</td>
        <td class="py-2 px-4">${reservation.reservation_datetime}</td>
        <td class="py-2 px-4">${reservation.number_of_guests}</td>
        <td class="py-2 px-4">
          <button onclick="viewReservation(${reservation.reservation_id})" class="bg-blue-500 text-white py-1 px-2 rounded-md mr-2">View</button>
          <button onclick="editReservation(${reservation.reservation_id})" class="bg-yellow-500 text-white py-1 px-2 rounded-md mr-2">Edit</button>
          <button onclick="deleteReservation(${reservation.reservation_id})" class="bg-red-500 text-white py-1 px-2 rounded-md">Delete</button>
        </td>
      `;
      tableBody.appendChild(row);
    });
  }

  // View Reservation Information
  function viewReservation(reservationId) {
    const reservation = reservations.find(r => r.reservation_id === reservationId);
    alert(`Reservation Info:\nCustomer ID: ${reservation.customer_id}\nTable ID: ${reservation.table_id}\nDate & Time: ${reservation.reservation_datetime}\nGuests: ${reservation.number_of_guests}`);
  }

  // Edit Reservation
  function editReservation(reservationId) {
    const reservation = reservations.find(r => r.reservation_id === reservationId);
    document.getElementById('editReservationId').value = reservation.reservation_id;
    document.getElementById('editPositionId').value = reservation.position_id;
    document.getElementById('editCustomerId').value = reservation.customer_id;
    document.getElementById('editTableId').value = reservation.table_id;
    document.getElementById('editReservationDateTime').value = reservation.reservation_datetime;
    document.getElementById('editGuests').value = reservation.number_of_guests;
    
    document.getElementById('editModal').classList.remove('hidden');
  }

  // Save Edited Reservation
  document.getElementById('editReservationForm').addEventListener('submit', function(event) {
    event.preventDefault();
    
    const reservationId = parseInt(document.getElementById('editReservationId').value);
    const reservation = reservations.find(r => r.reservation_id === reservationId);
    
    reservation.position_id = parseInt(document.getElementById('editPositionId').value);
    reservation.customer_id = parseInt(document.getElementById('editCustomerId').value);
    reservation.table_id = parseInt(document.getElementById('editTableId').value);
    reservation.reservation_datetime = document.getElementById('editReservationDateTime').value;
    reservation.number_of_guests = parseInt(document.getElementById('editGuests').value);
    
    loadReservationTable();  // Reload the table with updated data
    closeEditModal();
  });

  // Delete Reservation
  function deleteReservation(reservationId) {
    const confirmed = confirm("Are you sure you want to delete this reservation?");
    if (confirmed) {
      const reservationIndex = reservations.findIndex(r => r.reservation_id === reservationId);
      reservations.splice(reservationIndex, 1);
      loadReservationTable();  // Reload the table after deletion
    }
  }

  // Close the Edit Modal
  function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
  }

  // Initial loading of reservation data
  loadReservationTable();