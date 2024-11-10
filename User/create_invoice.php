<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'];
    $mobile_number = $_POST['mobile_number'];
    $total_price = $_POST['total_price'];
    $discount = $_POST['discount'];

    // Ensure discount does not exceed user's limit
    if ($discount <= $_SESSION['discount_limit']) {
        $stmt = $conn->prepare("INSERT INTO invoices (user_id, customer_name, mobile_number, total_price, discount, status) VALUES (?, ?, ?, ?, ?, 'approved')");
        $stmt->bind_param("issdd", $_SESSION['user_id'], $customer_name, $mobile_number, $total_price, $discount);
        $stmt->execute();
        echo "Invoice created successfully!";
    } else {
        echo "Discount exceeds authorized limit.";
    }
}
?>

<!-- HTML Form with Tailwind CSS -->
<form method="POST" class="max-w-md mx-auto mt-10">
    <div class="mb-4">
        <label class="block text-gray-700">Customer Name</label>
        <input type="text" name="customer_name" class="w-full px-3 py-2 border rounded-lg">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700">Mobile Number</label>
        <input type="text" name="mobile_number" class="w-full px-3 py-2 border rounded-lg">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700">Total Price</label>
        <input type="text" name="total_price" class="w-full px-3 py-2 border rounded-lg">
    </div>
    <div class="mb-4">
        <label class="block text-gray-700">Discount</label>
        <input type="text" name="discount" class="w-full px-3 py-2 border rounded-lg">
    </div>
    <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg">Create Invoice</button>
</form>
