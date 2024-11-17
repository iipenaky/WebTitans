import { BASE_URL } from "./constants.js";

const itemNameElement = document.getElementById("item_name");
const quantElem = document.getElementById("quantity");
const reorderlevelElement = document.getElementById("reorder_level");
const submitElem = document.getElementById("submit");
let inventoryItems = [];
function validateAddInventory() {
    messageDiv.innerText = "";
    itemNameError.classList.add("hidden");
    reorderlevelError.classList.add("hidden");
    quantError.classList.add("hidden");
  
    const itemName = itemNameElement.value.trim();
    const quant = quantElem.value.trim();
    const reorderLevel = reorderlevelElement.value.trim();
  
    console.log({ itemName, reorderLevel, quant});
    if (!itemName || !reorderLevel || !quant) {
      messageDiv.innerText = "All fields must be filled out.";
      return false;
    }
    return true;
  }

async function addInventory(e) {
    e.preventDefault();
    if (!validateAddInventory()) return;
    const formData = new FormData(submitElem);
  
    const req = await fetch(`${BASE_URL}/admin/inventory/add`, {
      method: "POST",
      body: JSON.stringify(Object.fromEntries(formData)),
    });
  
    if (!req.ok) {
      console.log({ req });
    }
  
    const json = await req.json();
    const data = json.message;
    console.log({ data });
    alert(data);
    signupForm.reset();
    setTimeout(() => {
      document.location.href = "./stock.html";
    }, 1500);
  }

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

  async function getinventorybyId() {
  
    const req = await fetch(`${BASE_URL}/admin/inventory/id`,);
  
    if (!req.ok) {
      console.log({ req });
    }
  
    const json = await req.json();
    const data = json;
    console.log({ data });
    return data;
  }

  async function loadInventoryTable() {
    const data = await getinventory();
    const tbody = document.getElementById('inventoryTableBody');
    tbody.innerHTML = '';
    if (data.length === 0) {
      tbody.innerHTML = "<tr><td colspan='6'>No inventory data available.</td></tr>";
      return;
    }  
    data.forEach((item) => {
      const row = `
        <tr>
          <td class="border px-4 py-2">${item.inventory_id}</td>
          <td class="border px-4 py-2">${item.item_name}</td>
          <td class="border px-4 py-2">${item.quantity}</td>
          <td class="border px-4 py-2">${item.unit}</td>
          <td class="border px-4 py-2">${item.reorder_level}</td>
          <td class="border px-4 py-2">
            <button onclick="editInventoryItem(${item.inventory_id})" class="bg-yellow-500 text-white px-2 py-1">Edit</button>
            <button onclick="restock(${item.inventory_id})" class="bg-red-500 text-white px-2 py-1">Restock</button>
          </td>
        </tr>
      `;
      tbody.insertAdjacentHTML('beforeend', row);
    });
  }

  function openAddInventoryModal() {
    document.getElementById('addInventoryModal').style.display = 'block';
  }

  function closeAddInventoryModal() {
    document.getElementById('addInventoryModal').classList.display = 'none';
  }

  document.getElementById('addInventoryForm').addEventListener('submit', function(event) {
    event.preventDefault();
    
    const newItem = {
      inventory_id: inventoryItems.length + 1,
      item_name: document.getElementById('item_name').value,
      quantity: parseInt(document.getElementById('quantity').value, 10),
      unit: parseInt(document.getElementById('unit').value, 10),
      reorder_level: parseInt(document.getElementById('reorder_level').value, 10)
    };
    inventoryItems.push(newItem);
    loadInventoryTable();
    closeAddInventoryModal();
  });

  async function editInventoryItem(itemId) {
    const item = await getinventorybyId();
    item.item_name = prompt("Update Item Name", item.item_name);
    item.quantity = parseInt(prompt("Update Quantity", item.quantity), 10);
    item.unit = parseInt(prompt("Update Unit", item.unit), 10);
    item.reorder_level = parseInt(prompt("Update Reorder Level", item.reorder_level), 10);
    loadInventoryTable();
  }

  function restock(itemId) {
    const confirmed = confirm("Are you sure you want to delete this item?");
    if (confirmed) {
      // an update for restocking update for editing the name snd other attributes to inventory php
    }
  }

  loadInventoryTable();