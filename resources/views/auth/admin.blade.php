@extends('layouts.auth')

@section('title', 'Admin Database - FastAPI Auth')

@section('content')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
<style>
    /* Premium Dark Styling for DataTables */
    .dataTables_wrapper { color: #9ca3af; padding: 1.5rem; }
    .dataTables_length select { background: #1f2937; color: white; border: 1px solid #374151; border-radius: 0.5rem; padding: 0.25rem 0.5rem; }
    .dataTables_filter input { background: #1f2937; color: white; border: 1px solid #374151; border-radius: 0.5rem; padding: 0.25rem 0.75rem; margin-left: 0.5rem; }
    table.dataTable { border-collapse: collapse !important; border: none !important; }
    table.dataTable thead th { background: rgba(255,255,255,0.05) !important; border-bottom: 1px solid rgba(255,255,255,0.1) !important; color: #9ca3af !important; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; padding: 1.5rem 1rem !important; }
    table.dataTable tbody tr { background: transparent !important; border-bottom: 1px solid rgba(255,255,255,0.05) !important; }
    table.dataTable tbody tr:hover { background: rgba(255,255,255,0.02) !important; }
    table.dataTable tbody td { padding: 1rem !important; border: none !important; }
    .dataTables_info { padding-top: 1.5rem !important; color: #6b7280 !important; }
    .dataTables_paginate { padding-top: 1.5rem !important; }
    .paginate_button { color: #9ca3af !important; border-radius: 0.5rem !important; border: 1px solid #374151 !important; margin-left: 0.25rem !important; padding: 0.5rem 1rem !important; cursor: pointer; }
    .paginate_button.current { background: #dc2626 !important; border-color: #dc2626 !important; color: white !important; }
    .paginate_button:hover { background: rgba(255,255,255,0.1) !important; border-color: #4b5563 !important; }
    
    /* Modal styles */
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); backdrop-filter: blur(5px); }
    .modal-content { background: #151515; margin: 10% auto; padding: 40px; border: 1px solid rgba(255,255,255,0.1); width: 400px; border-radius: 24px; }
</style>

<div class="mt-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-4xl font-extrabold text-white tracking-tight">System Database</h1>
            <p class="text-gray-400 mt-2">Browse and manage all records in the system.</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="loadUsers()" class="glass p-3 rounded-xl hover:bg-white/10 transition text-gray-400 hover:text-white" title="Refresh Data">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="glass p-6 rounded-2xl border-l-4 border-red-500">
            <div class="text-sm font-bold text-gray-500 uppercase mb-2">Total Users</div>
            <div id="statTotal" class="text-4xl font-black text-white">0</div>
        </div>
        <div class="glass p-6 rounded-2xl border-l-4 border-green-500">
            <div class="text-sm font-bold text-gray-500 uppercase mb-2">Verified</div>
            <div id="statVerified" class="text-4xl font-black text-white">0</div>
        </div>
        <div class="glass p-6 rounded-2xl border-l-4 border-purple-500">
            <div class="text-sm font-bold text-gray-500 uppercase mb-2">Admins</div>
            <div id="statAdmins" class="text-4xl font-black text-white">0</div>
        </div>
    </div>

    <div class="flex items-center space-x-4 mb-6">
        <button id="viewTableBtn" onclick="switchView('table')" class="bg-red-600 text-white px-4 py-2 rounded-lg font-bold transition">Table View</button>
        <button id="viewRawBtn" onclick="switchView('raw')" class="bg-white/5 hover:bg-white/10 text-gray-400 px-4 py-2 rounded-lg font-bold transition">Raw JSON</button>
    </div>

    <div id="tableView" class="glass rounded-3xl overflow-hidden">
        <table id="userTable" class="w-full text-left">
            <thead>
                <tr>
                    <th class="p-6">ID</th>
                    <th class="p-6">User</th>
                    <th class="p-6">Email</th>
                    <th class="p-6">Role</th>
                    <th class="p-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                <!-- Data will be populated by JS -->
            </tbody>
        </table>
    </div>

    <div id="rawView" class="hidden glass rounded-3xl p-8 font-mono text-xs text-green-400 overflow-x-auto whitespace-pre">
        Loading raw data...
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content glass">
        <h2 class="text-2xl font-bold text-white mb-6">Edit User</h2>
        <form id="editForm" class="space-y-4">
            <input type="hidden" id="editUserId">
            <div>
                <label class="block text-sm text-gray-400 mb-1">Full Name</label>
                <input type="text" id="editFullName" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Email</label>
                <input type="email" id="editEmail" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Role</label>
                <select id="editRole" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white">
                    <option value="false">User</option>
                    <option value="true">Administrator</option>
                </select>
            </div>
            <div class="flex space-x-3 mt-8">
                <button type="submit" class="flex-1 bg-red-600 text-white font-bold py-2 rounded-lg">Save Changes</button>
                <button type="button" onclick="closeModal()" class="flex-1 bg-white/5 text-gray-400 py-2 rounded-lg">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- jQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

<script>
    let userData = [];
    const apiToken = "{{ $token }}";
    const backendUrl = "http://localhost:8085/api/v1";

    function switchView(view) {
        const table = document.getElementById('tableView');
        const raw = document.getElementById('rawView');
        const tableBtn = document.getElementById('viewTableBtn');
        const rawBtn = document.getElementById('viewRawBtn');

        if (view === 'table') {
            table.classList.remove('hidden');
            raw.classList.add('hidden');
            tableBtn.className = "bg-red-600 text-white px-4 py-2 rounded-lg font-bold transition";
            rawBtn.className = "bg-white/5 hover:bg-white/10 text-gray-400 px-4 py-2 rounded-lg font-bold transition";
        } else {
            table.classList.add('hidden');
            raw.classList.remove('hidden');
            rawBtn.className = "bg-red-600 text-white px-4 py-2 rounded-lg font-bold transition";
            tableBtn.className = "bg-white/5 hover:bg-white/10 text-gray-400 px-4 py-2 rounded-lg font-bold transition";
            renderRaw();
        }
    }

    function renderRaw() {
        document.getElementById('rawView').innerText = JSON.stringify(userData, null, 4);
    }

    async function loadUsers() {
        try {
            const response = await fetch(`${backendUrl}/users/`, {
                headers: { 'Authorization': `Bearer ${apiToken}` }
            });

            if (response.status === 401 || response.status === 403) {
                document.getElementById('tableView').innerHTML = `<div class="p-12 text-center text-red-500">Access Denied</div>`;
                return;
            }

            userData = await response.json();
            
            document.getElementById('statTotal').innerText = userData.length;
            document.getElementById('statVerified').innerText = userData.filter(u => u.is_verified).length;
            document.getElementById('statAdmins').innerText = userData.filter(u => u.is_superuser).length;

            if ($.fn.DataTable.isDataTable('#userTable')) {
                $('#userTable').DataTable().destroy();
            }

            const tbody = document.getElementById('userTableBody');
            tbody.innerHTML = userData.map(user => `
                <tr class="hover:bg-white/5 transition">
                    <td class="p-6 font-mono text-xs text-gray-500">${user.id}</td>
                    <td class="p-6">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-red-500 to-orange-400 flex items-center justify-center font-bold text-white mr-3 text-xs">
                                ${user.full_name ? user.full_name.charAt(0).toUpperCase() : user.email.charAt(0).toUpperCase()}
                            </div>
                            <div class="font-bold text-white text-sm">${user.full_name || 'Anonymous'}</div>
                        </div>
                    </td>
                    <td class="p-6 text-gray-400 text-sm">${user.email}</td>
                    <td class="p-6">
                        <span class="px-2 py-1 rounded-md text-[10px] font-bold ${user.is_superuser ? 'bg-purple-500/10 text-purple-500' : 'bg-gray-500/10 text-gray-400'}">
                            ${user.is_superuser ? 'ADMIN' : 'USER'}
                        </span>
                    </td>
                    <td class="p-6 text-right">
                        <button onclick="openEditModal(${user.id})" class="text-blue-400 hover:text-blue-300 mr-3 transition">Edit</button>
                        <button onclick="deleteUser(${user.id})" class="text-red-500 hover:text-red-400 transition">Delete</button>
                    </td>
                </tr>
            `).join('');

            $('#userTable').DataTable({
                pageLength: 10,
                language: { search: "", searchPlaceholder: "Search records..." }
            });

        } catch (err) {
            console.error(err);
        }
    }

    async function deleteUser(id) {
        if (!confirm('Are you sure you want to delete this user?')) return;

        try {
            const response = await fetch(`${backendUrl}/users/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${apiToken}` }
            });

            if (response.ok) {
                alert('User deleted successfully');
                loadUsers();
            } else {
                const err = await response.json();
                alert(err.detail || 'Delete failed');
            }
        } catch (err) {
            alert('An error occurred');
        }
    }

    function openEditModal(id) {
        const user = userData.find(u => u.id === id);
        if (!user) return;

        document.getElementById('editUserId').value = user.id;
        document.getElementById('editFullName').value = user.full_name || '';
        document.getElementById('editEmail').value = user.email;
        document.getElementById('editRole').value = user.is_superuser.toString();
        
        document.getElementById('editModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    document.getElementById('editForm').onsubmit = async (e) => {
        e.preventDefault();
        const id = document.getElementById('editUserId').value;
        const data = {
            full_name: document.getElementById('editFullName').value,
            email: document.getElementById('editEmail').value,
            is_superuser: document.getElementById('editRole').value === 'true'
        };

        try {
            const response = await fetch(`${backendUrl}/users/${id}`, {
                method: 'PUT',
                headers: { 
                    'Authorization': `Bearer ${apiToken}`,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                alert('User updated successfully');
                closeModal();
                loadUsers();
            } else {
                const err = await response.json();
                alert(err.detail || 'Update failed');
            }
        } catch (err) {
            alert('An error occurred');
        }
    };

    $(document).ready(function() {
        loadUsers();
    });
</script>
@endsection
