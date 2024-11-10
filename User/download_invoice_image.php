<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: user_login.php");
    exit;
}

include('../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $customer_name = $_POST['customer_name'];
    $mobile_number = $_POST['mobile_number'];
    $product = $_POST['product'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];
    $discount = $_POST['discount'];

    // Calculate total price with discount
    $total_price = ($quantity * $unit_price) * (1 - $discount / 100);

    // Create the image
    $width = 800;
    $height = 400;
    $image = imagecreatetruecolor($width, $height);

    // Set colors
    $background_color = imagecolorallocate($image, 255, 255, 255);
    $text_color = imagecolorallocate($image, 0, 0, 0);
    $highlight_color = imagecolorallocate($image, 0, 102, 204);

    // Fill the background
    imagefilledrectangle($image, 0, 0, $width, $height, $background_color);

    // Add text to the image
    $font = 5; // Use built-in font
    $x = 20;
    $y = 20;
    imagestring($image, $font, $x, $y, "Customer Name: $customer_name", $text_color);
    imagestring($image, $font, $x, $y + 20, "Mobile Number: $mobile_number", $text_color);
    imagestring($image, $font, $x, $y + 40, "Product: $product", $text_color);
    imagestring($image, $font, $x, $y + 60, "Quantity: $quantity", $text_color);
    imagestring($image, $font, $x, $y + 80, "Unit Price: $unit_price", $text_color);
    imagestring($image, $font, $x, $y + 100, "Discount: $discount%", $text_color);
    imagestring($image, $font, $x, $y + 120, "Total Price: $$total_price", $highlight_color);

    // Output the image as a PNG for download
    header("Content-Type: image/png");
    header("Content-Disposition: attachment; filename=invoice.png");
    imagepng($image);
    imagedestroy($image);
    exit;
}
?>

