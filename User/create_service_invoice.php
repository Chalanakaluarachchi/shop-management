<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: user_login.php");
    exit;
}

include('../config.php');

// Fetch all available services from the database
$services = $conn->query("SELECT * FROM services")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $customer_name = $_POST['customer_name'];
    $mobile_number = $_POST['mobile_number'];
    $service_id = $_POST['service_id'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];
    $discount = $_POST['discount'];

    // Calculate total price with discount
    $total_price = ($quantity * $unit_price) * (1 - $discount / 100);

    $stmt = $conn->prepare("INSERT INTO invoices (user_id, customer_name, mobile_number, service_id, quantity, unit_price, total_price, discount) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $customer_name, $mobile_number, $service_id, $quantity, $unit_price, $total_price, $discount]);

    header("Location: user_dashboard.php");
    exit;
}

// Edit service logic if service ID is passed
if (isset($_GET['edit_service_id'])) {
    $service_id = $_GET['edit_service_id'];
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$service_id]);
    $service_to_edit = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle service update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_service_id'])) {
    $service_id = $_POST['edit_service_id'];
    $service_name = $_POST['service_name'];
    $service_price = $_POST['service_price'];
    $service_status = $_POST['service_status'];

    $stmt = $conn->prepare("UPDATE services SET service_name = ?, service_price = ?, service_status = ? WHERE id = ?");
    $stmt->execute([$service_name, $service_price, $service_status, $service_id]);

    header("Location: user_dashboard.php"); // Redirect to the dashboard after update
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Service Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        function updatePrice() {
            var serviceSelect = document.getElementById("service_id");
            var selectedServiceId = serviceSelect.value;
            var unitPriceInput = document.getElementById("unit_price");
            var services = <?php echo json_encode($services); ?>;
            var selectedService = services.find(service => service.id == selectedServiceId);
            
            if (selectedService) {
                unitPriceInput.value = selectedService.service_price;
            }
        }

        function updateTotal() {
            var quantity = parseFloat(document.getElementById("quantity").value);
            var unitPrice = parseFloat(document.getElementById("unit_price").value);
            var discount = parseFloat(document.getElementById("discount").value);

            if (!isNaN(quantity) && !isNaN(unitPrice)) {
                var totalPrice = (quantity * unitPrice) * (1 - (discount / 100));
                document.getElementById("total_price").value = totalPrice.toFixed(2);
            }
        }
    </script>
</head>
<body class="bg-gray-100">

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-lg mt-10">
        <h2 class="text-3xl font-semibold text-gray-800 mb-6">Create Service Invoice</h2>

        <form method="POST" class="space-y-6">
            <div class="flex flex-col">
                <label for="customer_name" class="text-lg font-medium text-gray-700">Customer Name</label>
                <input type="text" name="customer_name" class="border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="flex flex-col">
                <label for="mobile_number" class="text-lg font-medium text-gray-700">Mobile Number</label>
                <input type="text" name="mobile_number" class="border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="flex flex-col">
                <label for="service_id" class="text-lg font-medium text-gray-700">Select Service</label>
                <select name="service_id" id="service_id" class="border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="updatePrice()" required>
                    <option value="">--Select Service--</option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= $service['id'] ?>"><?= htmlspecialchars($service['service_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex flex-col">
                <label for="unit_price" class="text-lg font-medium text-gray-700">Unit Price</label>
                <input type="number" id="unit_price" name="unit_price" class="border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" step="0.01" readonly required>
            </div>

            <div class="flex flex-col">
                <label for="quantity" class="text-lg font-medium text-gray-700">Quantity</label>
                <input type="number" id="quantity" name="quantity" class="border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required oninput="updateTotal()">
            </div>

            <div class="flex flex-col">
                <label for="discount" class="text-lg font-medium text-gray-700">Discount (%)</label>
                <input type="number" id="discount" name="discount" class="border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" step="0.01" required oninput="updateTotal()">
            </div>

            <div class="flex flex-col">
                <label for="total_price" class="text-lg font-medium text-gray-700">Total Price</label>
                <input type="number" id="total_price" class="border border-gray-300 p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" step="0.01" readonly>
            </div>

            <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">Submit Invoice</button>
        </form>

        <!-- Service List Section -->
        <h2 class="text-3xl font-semibold text-gray-800 mt-10">Service List</h2>
        <table class="min-w-full bg-white mt-6 border">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Service Name</th>
                    <th class="py-2 px-4 border-b">Unit Price</th>
                    <th class="py-2 px-4 border-b">Status</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td class="py-2 px-4 border-b"><?= htmlspecialchars($service['service_name']) ?></td>
                        <td class="py-2 px-4 border-b">$<?= number_format($service['service_price'], 2) ?></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($service['status'] ?? 'No Status'); ?></td>
                        <td class="py-2 px-4 border-b">
                            <a href="?edit_service_id=<?= $service['id'] ?>" class="text-blue-500 hover:underline">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Edit Service Form -->
        <?php if (isset($service_to_edit)): ?>
            <div class="mt-10 bg-white p-6 rounded-lg shadow-md">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Edit Service</h2>
                <form method="POST">
                    <div class="flex flex-col mb-4">
                        <input type="hidden" name="edit_service_id" value="<?= $service_to_edit['id'] ?>">
                        <label for="service_name" class="text-lg font-medium text-gray-700">Service Name</label>
                        <input type="text" name="service_name" value="<?= htmlspecialchars($service_to_edit['service_name']) ?>" class="border border-gray-300 p-2 rounded-lg" required>
                    </div>
                    <div class="flex flex-col mb-4">
                        <label for="service_price" class="text-lg font-medium text-gray-700">Service Price</label>
                        <input type="number" name="service_price" value="<?= $service_to_edit['service_price'] ?>" class="border border-gray-300 p-2 rounded-lg" step="0.01" required>
                    </div>
                    <div class="flex flex-col mb-4">
                        <label for="service_status" class="text-lg font-medium text-gray-700">Status</label>
                        <select name="service_status" class="border border-gray-300 p-2 rounded-lg" required>
                            <option value="active" <?= $service_to_edit['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $service_to_edit['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-lg">Update Service</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
