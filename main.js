

// Display Expenses
// function displayExpenses() {
//     const list = document.getElementById('expenses-list');
    
//     if (expenses.length === 0) {
//         list.innerHTML = '<p class="text-gray-500 text-center py-8">No expenses found. Click "Add Expense" to get started.</p>';
//         return;
//     }

//     list.innerHTML = expenses.map((expense, idx) => `
//         <div class="flex items-center justify-between bg-red-50 p-4 rounded-lg border border-red-200">
//             <div>
//                 <h4 class="font-semibold text-gray-800">${expense.name}</h4>
//                 <p class="text-red-600 font-bold">$${expense.amount}</p>
//                 <p class="text-gray-500 text-sm">${expense.date}</p>
//             </div>
//             <div class="flex gap-2">
//                 <button onclick="openEditExpenseModal(${idx})" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">Edit</button>
//                 <button onclick="openDeleteExpenseConfirm(${idx})" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">Delete</button>
//             </div>
//         </div>
//     `).join('');
// }

// Add Income Modal
function openAddIncomeModal() {
    document.getElementById('add-income-modal').classList.remove('hidden');
    document.getElementById('income-name').value = '';
    document.getElementById('income-amount').value = '';
    document.getElementById('income-date').value = '';
    
}

function closeAddIncomeModal() {
    document.getElementById('add-income-modal').classList.add('hidden');
}

// function openIncomeConfirm() {
//     const name = document.getElementById('income-name').value.trim();
//     const amount = document.getElementById('income-amount').value.trim();
//     const date = document.getElementById('income-date').value.trim();

//     if (!name || !amount || !date) {
//         alert('Please fill all fields');
//         return;
//     }

//     currentAction = () => {
//         incomes.push({ name, amount: parseFloat(amount), date });
//         closeAddIncomeModal();
//         closeConfirmModal();
//         displayIncomes();
//     };

//     showConfirmModal('Add Income', 'Are you sure you want to add this income?');
// }

// Add Expense Modal
function openAddExpenseModal() {
    document.getElementById('add-expense-modal').classList.remove('hidden');
    document.getElementById('expense-name').value = '';
    document.getElementById('expense-amount').value = '';
    document.getElementById('expense-date').value = '';
}

function closeAddExpenseModal() {
    document.getElementById('add-expense-modal').classList.add('hidden');
}

function closeEditIncomeModal() {
    document.getElementById('edit-income-modal').classList.add('hidden');
}
function closeDeleteIncomeModal() {
    document.getElementById('delete-income-modal').classList.add('hidden');
}

function closeEditExpenseModal() {
    document.getElementById('edit-expense-modal').classList.add('hidden');
    currentEditId = null;
}

function openEditExpenseConfirm() {
    const name = document.getElementById('edit-expense-name').value.trim();
    const amount = document.getElementById('edit-expense-amount').value.trim();
    const date = document.getElementById('edit-expense-date').value.trim();

    if (!name || !amount || !date) {
        alert('Please fill all fields');
        return;
    }

    currentAction = () => {
        expenses[currentEditId] = { name, amount: parseFloat(amount), date };
        closeEditExpenseModal();
        closeConfirmModal();
        displayExpenses();
    };

    showConfirmModal('Update Expense', 'Are you sure you want to update this expense?');
}


document.addEventListener('DOMContentLoaded', () => {
    
    // const currentPath = window.location.pathname;

    // if (currentPath === '/incomes.php'){
    const editButtons = document.querySelectorAll('.editBtn');

    editButtons.forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault(); 

            const id = btn.dataset.id;
            const amount = btn.dataset.amount;
            const desc = btn.dataset.description;
            const rawDate = btn.dataset.date; 

            document.getElementById('edit-income-amount').value = amount;
            document.getElementById('edit-income-description').value = desc;
            
            if (rawDate) {
                document.getElementById('edit-income-date').value = rawDate.split(' ')[0];
            }

            const form = document.querySelector("#edit-income-modal form");
            
            const existingIdInput = form.querySelector("input[name='id']");
            if (existingIdInput) {
                existingIdInput.remove();
            }

            const hiddenIdInput = document.createElement("input");
            hiddenIdInput.type = "hidden";
            hiddenIdInput.name = "id";
            hiddenIdInput.value = id;
            form.appendChild(hiddenIdInput);
            
            document.getElementById('edit-income-modal').classList.remove('hidden');
        });
    });

    const deleteButtons = document.querySelectorAll('.delete-item');

    deleteButtons.forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault(); 

            const id = btn.dataset.id;
            const amount = btn.dataset.amount;
            const desc = btn.dataset.description;
            const rawDate = btn.dataset.date; 

            document.getElementById('delete-income-amount').value = amount;
            document.getElementById('delete-income-description').value = desc;
            
            if (rawDate) {
                document.getElementById('delete-income-date').value = rawDate.split(' ')[0];
            }

            const form = document.querySelector("#delete-income-modal form");

            const hiddenIdInput = document.createElement("input");
            hiddenIdInput.type = "hidden";
            hiddenIdInput.name = "delete-id";
            hiddenIdInput.value = id;
            form.appendChild(hiddenIdInput);
            
            document.getElementById('delete-income-modal').classList.remove('hidden');
        });
    });
    // }

    // else if (currentPath === '/expenses.php'){
    const expense_editButtons = document.querySelectorAll('.expense-editBtn');

    expense_editButtons.forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault(); 

            const id = btn.dataset.id;
            const amount = btn.dataset.amount;
            const desc = btn.dataset.description;
            const rawDate = btn.dataset.date; 

            document.getElementById('edit-expense-amount').value = amount;
            document.getElementById('edit-expense-description').value = desc;
            
            if (rawDate) {
                document.getElementById('edit-expense-date').value = rawDate.split(' ')[0];
            }

            const form = document.querySelector("#edit-expense-modal form");
            
            const existingIdInput = form.querySelector("input[name='id']");
            if (existingIdInput) {
                existingIdInput.remove();
            }

            const hiddenIdInput = document.createElement("input");
            hiddenIdInput.type = "hidden";
            hiddenIdInput.name = "id";
            hiddenIdInput.value = id;
            form.appendChild(hiddenIdInput);
            
            document.getElementById('edit-expense-modal').classList.remove('hidden');
        });
    });

    const expense_deleteButtons = document.querySelectorAll('.expense-delete-item');

    expense_deleteButtons.forEach(btn => {
        btn.addEventListener('click', e => {
            e.preventDefault(); 

            const id = btn.dataset.id;
            const amount = btn.dataset.amount;
            const desc = btn.dataset.description;
            const rawDate = btn.dataset.date; 

            document.getElementById('delete-expense-amount').value = amount;
            document.getElementById('delete-expense-description').value = desc;
            
            if (rawDate) {
                document.getElementById('delete-expense-date').value = rawDate.split(' ')[0];
            }

            const form = document.querySelector("#delete-expense-modal form");

            const hiddenIdInput = document.createElement("input");
            hiddenIdInput.type = "hidden";
            hiddenIdInput.name = "delete-id";
            hiddenIdInput.value = id;
            form.appendChild(hiddenIdInput);
            
            document.getElementById('delete-expense-modal').classList.remove('hidden');
        });
    });
    // }

});

function closeDeleteExpenseModal() {
    document.getElementById('delete-expense-modal').classList.add('hidden');
}
