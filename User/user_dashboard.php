<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: user_login.php");
    exit;
}

include('../config.php');
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f3e7e9 10%, #e3eeff 100%);
            font-family: 'Arial', sans-serif;
        }
        .card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .clock {
            font-size: 1.2rem;
            color: #555;
            margin-top: 2rem;
            font-weight: bold;
        }
    </style>
    <script>
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString();
            document.getElementById('clock').textContent = timeString;
        }
        setInterval(updateTime, 1000);
        document.addEventListener('DOMContentLoaded', updateTime);
    </script>
</head>
<body class="p-6">

    <div class="max-w-lg mx-auto">
        <!-- Welcome Card -->
        <div class="card text-center">
            <h1 class="text-4xl font-bold text-blue-600 mb-4">Welcome, <?= htmlspecialchars($user['username']) ?>!</h1>
            <p class="text-lg text-gray-700 mb-8">Explore your dashboard and manage your invoices below.</p>

            <div class="space-y-4">
                <!-- Product Invoice Button -->
                <a href="create_product_invoice.php" class="block bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-full transition duration-300">
                    Create Product Invoice
                </a>
                <!-- Service Invoice Button -->
                <a href="create_service_invoice.php" class="block bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-full transition duration-300">
                    Create Service Invoice
                </a>
            </div>
        </div>

        <!-- Logout Button -->
        <div class="text-center mt-6">
            <a href="logout.php" class="text-red-500 hover:text-red-700 font-semibold">Logout</a>
        </div>

        <!-- Real-Time Clock -->
        <div class="clock text-center mt-8">
            <span>Current Time: <span id="clock"></span></span>
        </div>
    </div>
    
</body>
</html>
