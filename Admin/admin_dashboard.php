<?php
session_start();

// Check if user is admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: admin_register.php");
    exit;
}

// Include database connection
include('../config.php');

// Fetching real-time data (number of users, products, etc.)
try {
    // Number of users
    $stmt = $conn->prepare("SELECT COUNT(*) AS user_count FROM users");
    $stmt->execute();
    $userCount = $stmt->fetch(PDO::FETCH_ASSOC)['user_count'];

    // Number of products
    $stmt = $conn->prepare("SELECT COUNT(*) AS product_count FROM products");
    $stmt->execute();
    $productCount = $stmt->fetch(PDO::FETCH_ASSOC)['product_count'];

    // Number of services
    $stmt = $conn->prepare("SELECT COUNT(*) AS service_count FROM services");
    $stmt->execute();
    $serviceCount = $stmt->fetch(PDO::FETCH_ASSOC)['service_count'];

    // Number of contracts
    $stmt = $conn->prepare("SELECT COUNT(*) AS contract_count FROM contracts");
    $stmt->execute();
    $contractCount = $stmt->fetch(PDO::FETCH_ASSOC)['contract_count'];

} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

<!-- Dashboard Container -->
<div class="container mx-auto p-6">

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
        <p class="text-lg text-gray-600">Welcome, <?php echo $_SESSION['username']; ?>!</p>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-gray-700">Users</h3>
            <p class="text-3xl font-bold text-gray-900"><?php echo $userCount; ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-gray-700">Products</h3>
            <p class="text-3xl font-bold text-gray-900"><?php echo $productCount; ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-gray-700">Services</h3>
            <p class="text-3xl font-bold text-gray-900"><?php echo $serviceCount; ?></p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-xl font-semibold text-gray-700">Contracts</h3>
            <p class="text-3xl font-bold text-gray-900"><?php echo $contractCount; ?></p>
        </div>
    </div>

    <!-- Navigation Links -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Manage Content</h2>
        <ul class="space-y-3">
            <li><a href="manage_users.php" class="text-blue-500 hover:underline">Manage Users</a></li>
            <li><a href="manage_products_services.php" class="text-blue-500 hover:underline">Manage Products & Services</a></li>
            <li><a href="manage_contracts.php" class="text-blue-500 hover:underline">Manage Contracts</a></li>
            <li><a href="activity_log.php" class="text-blue-500 hover:underline">User Activity Log</a></li>
            <li><a href="new_invoice.php" class="text-blue-500 hover:underline">New Invoices</a></li>
        </ul>
    </div>

    <!-- Chart Section -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Activity Overview</h2>
        <canvas id="myChart"></canvas>
    </div>

</div>

<script>
    // Data for the chart
    const data = {
        labels: ['Users', 'Products', 'Services', 'Contracts'],
        datasets: [{
            label: 'Total Counts',
            data: [<?php echo $userCount; ?>, <?php echo $productCount; ?>, <?php echo $serviceCount; ?>, <?php echo $contractCount; ?>],
            backgroundColor: ['#4CAF50', '#FF9800', '#2196F3', '#F44336'],
            borderColor: ['#388E3C', '#F57C00', '#1976D2', '#D32F2F'],
            borderWidth: 1
        }]
    };

    // Chart.js Configuration
    const config = {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.raw + " items";
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        },
    };

    // Initialize the chart
    const myChart = new Chart(
        document.getElementById('myChart'),
        config
    );
</script>

</body>
</html>
