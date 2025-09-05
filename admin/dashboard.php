<?php
session_start();
require_once '../database/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.html');
    exit();
}

$conn = getConnection();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Tracking Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white p-4">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">🚌 Bus Tracking Admin</h1>
            <div class="flex items-center space-x-4">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <button onclick="logout()" class="bg-red-500 hover:bg-red-700 px-4 py-2 rounded">Logout</button>
            </div>
        </div>
    </nav>

    <div class="container mx-auto p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold">Total Buses</h3>
                <p class="text-3xl font-bold text-blue-600" id="totalBuses">-</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold">Total Routes</h3>
                <p class="text-3xl font-bold text-green-600" id="totalRoutes">-</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold">Active Buses</h3>
                <p class="text-3xl font-bold text-yellow-600" id="activeBuses">-</p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold">Total Stops</h3>
                <p class="text-3xl font-bold text-purple-600" id="totalStops">-</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button onclick="showAddBusModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded">
                    ➕ Add New Bus
                </button>
                <button onclick="showAddRouteModal()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-4 rounded">
                    🛣️ Add New Route
                </button>
                <button onclick="refreshData()" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-3 px-4 rounded">
                    🔄 Refresh Data
                </button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Active Buses</h2>
            <div id="busesTable" class="overflow-x-auto">
                <!-- Buses will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadData();
        });

        function loadData() {
            loadBuses();
            loadRoutes();
        }

        function loadBuses() {
            fetch('../api/buses.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalBuses').textContent = data.length;
                    document.getElementById('activeBuses').textContent = data.filter(bus => bus.status === 'active').length;
                    
                    const table = document.getElementById('busesTable');
                    table.innerHTML = `
                        <table class="min-w-full table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Bus ID</th>
                                    <th class="px-6 py-3 text-left">Route</th>
                                    <th class="px-6 py-3 text-left">Location</th>
                                    <th class="px-6 py-3 text-left">Speed</th>
                                    <th class="px-6 py-3 text-left">Occupancy</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.map(bus => `
                                    <tr class="border-b">
                                        <td class="px-6 py-4">${bus.id}</td>
                                        <td class="px-6 py-4">${bus.route_name || 'No Route'}</td>
                                        <td class="px-6 py-4">${bus.current_lat}, ${bus.current_lng}</td>
                                        <td class="px-6 py-4">${bus.speed} km/h</td>
                                        <td class="px-6 py-4">${bus.occupancy}/${bus.capacity}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    `;
                })
                .catch(error => console.error('Error:', error));
        }

        function loadRoutes() {
            fetch('../api/routes.php')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('totalRoutes').textContent = data.length;
                    document.getElementById('totalStops').textContent = data.reduce((sum, route) => sum + (route.total_stops || 0), 0);
                })
                .catch(error => console.error('Error:', error));
        }

        function refreshData() {
            loadData();
        }

        function logout() {
            window.location.href = '../login.html';
        }

        function showAddBusModal() {
            alert('Add Bus Modal - Feature coming soon!');
        }

        function showAddRouteModal() {
            alert('Add Route Modal - Feature coming soon!');
        }
    </script>
</body>
</html>
