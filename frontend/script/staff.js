import { handleAdminLoggedIn } from "./utils.js";
import { handleEmail, validateFieldsFilled, handleSalary, handlePasswordProblems } from "./validation.js";
handleAdminLoggedIn();

import { handleLogout, handleError } from "./utils.js";
const logoutButton = document.getElementById("logout");
logoutButton.onclick = handleLogout;

import { BASE_URL } from "./constants.js";

const getAllStaff = async () => {
    const res = await fetch(`${BASE_URL}/admin/staff/all`, {
        credentials: "include",
    });

    if (!res.ok) {
        await handleError(res);
    }

    const data = await res.json();
    return data;
};

const updateStaff = async (staff) => {
    const res = await fetch(`${BASE_URL}/admin/staff/update`, {
        method: "PUT",
        body: JSON.stringify(staff),
        credentials: "include",
    });

    if (!res.ok) {
        await handleError(res);
    }

    const data = await res.json();
    return data;
};

const deleteStaff = async (staffId) => {
    const res = await fetch(`${BASE_URL}/admin/staff/delete/${staffId}`, {
        method: "DELETE",
        credentials: "include",
    });

    if (!res.ok) {
        await handleError(res);
    }

    const data = await res.json();
    return data;
};

const addStaff = async (staff) => {
    const res = await fetch(`${BASE_URL}/admin/staff/signup`, {
        method: "POST",
        credentials: "include",
        body: JSON.stringify(staff),
    });
    if (!res.ok) {
        await handleError(res);
    }
    const data = await res.json();
    return data;
};

const populateStaffTable = (staff) => {
    const staffTable = document.getElementById("staff-table");
    for (let s of staff) {
        let { staff_id, first_name, last_name, email, salary, position, hire_date, passhash } = s;
        salary = parseFloat(salary).toFixed(2);
        const tdClass = "border px-4 py-2";

        const staffRow = document.createElement("tr");
        const staffId = document.createElement("td");
        const staffFirstName = document.createElement("td");
        const staffLastName = document.createElement("td");
        const staffEmail = document.createElement("td");
        const staffSalary = document.createElement("td");
        const staffPosition = document.createElement("td");
        const staffHireDate = document.createElement("td");

        staffId.textContent = staff_id;
        staffFirstName.textContent = first_name;
        staffLastName.textContent = last_name;
        staffEmail.textContent = email;
        staffSalary.textContent = salary;
        staffPosition.textContent = position;
        staffHireDate.textContent = hire_date;

        const actions = document.createElement("td");
        const viewButton = document.createElement("button");
        const editButton = document.createElement("button");
        const deleteButton = document.createElement("button");
        viewButton.textContent = "View";
        editButton.textContent = "Edit";
        deleteButton.textContent = "Delete";

        let i = 0;
        const colors = ["blue", "yellow", "red"];
        for (let element of [viewButton, editButton, deleteButton]) {
            element.className = `bg-${colors[i]}-500 text-white px-2 py-1`;
            if (i !== 0) {
                element.style.marginLeft = "0.25rem";
            }
            actions.appendChild(element);
            i++;
        }

        const view = () => {
            alert(
                `Staff Name: ${first_name} ${last_name}\nPosition: ${position}\nEmail: ${email}\nHire Date: ${hire_date}\nSalary: $${salary}`,
            );
        };

        const del = async () => {
            if (confirm("Are you sure you want to delete this staff?")) {
                try {
                    const res = await deleteStaff(staff_id);
                    staffRow.remove();
                } catch (e) {
                    console.error(e);
                }
            }
        };

        const update = async () => {
            const modal = document.getElementById("updateStaffForm");

            const modalData = Object.fromEntries(new FormData(modal));
            modalData.staff_id = staff_id;
            modalData.firstName = first_name;
            modalData.lastName = last_name;
            modalData.email = email;
            modalData.salary = salary;
            modalData.position = position;

            // Populate form with existing modalData
            for (let key in modalData) {
                const input = document.getElementById(key);
                if (input) {
                    input.value = modalData[key];
                }
            }

            modal.onsubmit = async (e) => {
                e.preventDefault();
                const fData = Object.fromEntries(new FormData(modal));
                const data = {
                    staff_id: staff_id,
                    first_name: fData.firstName,
                    last_name: fData.lastName,
                    email: fData.email,
                    salary: fData.salary,
                    position: fData.position,
                    passhash,
                };
                if (
                    !validateFieldsFilled(Object.values(data)) ||
                    !handleEmail(data.email) ||
                    !handleSalary(data.salary)
                ) {
                    return;
                }

                try {
                    const res = await updateStaff(data);
                    await refreshStaffTable();
                    alert("Staff updated successfully");
                    modal.onsubmit = null;
                    modal.reset();
                } catch (e) {
                    console.error(e);
                }
                closeUpdateStaffModal();
            };

            openUpdateStaffModal();
        };

        viewButton.onclick = view;
        deleteButton.onclick = del;
        editButton.onclick = update;

        for (let element of [
            staffId,
            staffFirstName,
            staffLastName,
            staffPosition,
            staffEmail,
            staffHireDate,
            staffSalary,
            actions,
        ]) {
            element.className = tdClass;
            staffRow.appendChild(element);
        }

        staffTable.children[1].appendChild(staffRow);
    }
};

function openAddStaffModal() {
    document.getElementById("addStaffModal").style.display = "block";
}

function closeAddStaffModal() {
    document.getElementById("addStaffModal").style.display = "none";
}

function openUpdateStaffModal() {
    document.getElementById("updateStaffModal").style.display = "block";
}

function closeUpdateStaffModal() {
    document.getElementById("updateStaffModal").style.display = "none";
}

const handleAddStaff = async (e) => {
    e.preventDefault();
    const form = document.getElementById("addStaffForm");
    const fData = Object.fromEntries(new FormData(form));
    const data = {
        first_name: fData.firstName,
        last_name: fData.lastName,
        email: fData.email,
        salary: fData.salary,
        position: fData.position,
        password: fData.password,
    };
    if (
        !validateFieldsFilled(Object.values(data)) ||
        !handleEmail(data.email) ||
        !handleSalary(data.salary) ||
        !handlePasswordProblems(data.password)
    ) {
        return;
    }

    try {
        const res = await addStaff(data);
        await refreshStaffTable();
        alert("Staff added successfully");
        form.reset();
        closeAddStaffModal();
    } catch (e) {
        console.error(e);
    }
};

document.getElementById("addStaffForm").onsubmit = handleAddStaff;
document.getElementById("add-staff").onclick = openAddStaffModal;
document.getElementById("modal-close").onclick = closeAddStaffModal;
document.getElementById("update-modal-close").onclick = closeUpdateStaffModal;

async function refreshStaffTable() {
    const staff = await getAllStaff();
    const staffTable = document.getElementById("staff-table");
    staffTable.children[1].innerHTML = "";
    populateStaffTable(staff);
}

(async function () {
    await refreshStaffTable();
})();
