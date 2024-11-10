<?php
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $mobile_number = $_POST['mobile_number'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];  // 'admin' or 'user'
    $discount_limit = $_POST['discount_limit'];

    $stmt = $conn->prepare("INSERT INTO users (full_name, mobile_number, username, password, role, discount_limit) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssd", $full_name, $mobile_number, $username, $password, $role, $discount_limit);
    $stmt->execute();

    echo "User registered successfully!";
}
?>

<!-- Tailwind CSS Form -->
<form method="POST" class="max-w-md mx-auto mt-10">
    <div class="mb-4"><label>Full Name:</label><input type="text" name="full_name" class="w-full px-3 py-2 border rounded-lg"></div>
    <div class="mb-4"><label>Mobile Number:</label><input type="text" name="mobile_number" class="w-full px-3 py-2 border rounded-lg"></div>
    <div class="mb-4"><label>Username:</label><input type="text" name="username" class="w-full px-3 py-2 border rounded-lg"></div>
    <div class="mb-4"><label>Password:</label><input type="password" name="password" class="w-full px-3 py-2 border rounded-lg"></div>
    <div class="mb-4"><label>Role:</label><select name="role" class="w-full px-3 py-2 border rounded-lg"><option value="admin">Admin</option><option value="user">User</option></select></div>
    <div class="mb-4"><label>Discount Limit:</label><input type="number" name="discount_limit" class="w-full px-3 py-2 border rounded-lg"></div>
    <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg">Register</button>
</form>
