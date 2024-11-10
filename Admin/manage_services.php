<?php
include('../config.php');

// Add service if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_name'])) {
    $service_name = $_POST['service_name'];
    $service_price = $_POST['service_price'];
    $contract_id = $_POST['contract_id'];

    $stmt = $conn->prepare("INSERT INTO services (service_name, service_price, contract_id, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$service_name, $service_price, $contract_id]);
    header("Location: manage_services.php");
}

// Fetch services
$services = $conn->query("SELECT * FROM services")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="container mx-auto">
        <h1 class="text-3xl font-bold mb-4">Manage Services</h1>

        <!-- Add Service Form -->
        <form method="POST" action="" class="bg-white p-4 rounded-lg shadow-md mb-6">
            <h2 class="text-xl font-semibold mb-2">Add New Service</h2>
            <div class="mb-4">
                <label for="service_name" class="block text-gray-700 font-semibold">Service Name</label>
                <input type="text" id="service_name" name="service_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="service_price" class="block text-gray-700 font-semibold">Service Price</label>
                <input type="number" id="service_price" name="service_price" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="mb-4">
                <label for="contract_id" class="block text-gray-700 font-semibold">Contract ID</label>
                <input type="text" id="contract_id" name="contract_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-700">Add Service</button>
        </form>

        <!-- Services Table -->
        <table class="min-w-full bg-white border rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-200 text-gray-600 text-left text-sm leading-normal">
                    <th class="py-3 px-6">ID</th>
                    <th class="py-3 px-6">Service Name</th>
                    <th class="py-3 px-6">Service Price</th>
                    <th class="py-3 px-6">Contract ID</th>
                    <th class="py-3 px-6">Created At</th>
                    <th class="py-3 px-6">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm font-light">
                <?php foreach ($services as $service): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6"><?= htmlspecialchars($service['id']) ?></td>
                    <td class="py-3 px-6"><?= htmlspecialchars($service['service_name']) ?></td>
                    <td class="py-3 px-6"><?= htmlspecialchars($service['service_price']) ?></td>
                    <td class="py-3 px-6"><?= htmlspecialchars($service['contract_id']) ?></td>
                    <td class="py-3 px-6"><?= htmlspecialchars($service['created_at']) ?></td>
                    <td class="py-3 px-6 space-x-4">
                        <a href="edit_service.php?id=<?= $service['id'] ?>" class="text-blue-500 hover:underline">Edit</a>
                        <a href="delete_service.php?id=<?= $service['id'] ?>" class="text-red-500 hover:underline" onclick="return confirm('Are you sure you want to delete this service?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
