<?php
// Stripe Checkout Backend
// By ikhwan 05-01-2024

session_start();
require_once('stripe/init.php');
require_once 'stripe/secrets.php';

\Stripe\Stripe::setApiKey($stripeSecretKey);
header('Content-Type: application/json');
$YOUR_DOMAIN = 'http://localhost';

// Grab items from last page
$stripeCart = $_SESSION['stripeCart'];

// Open Stripe Session
$checkout_session = \Stripe\Checkout\Session::create([
  'line_items' => $stripeCart,
  'mode' => 'payment',
  'success_url' => $YOUR_DOMAIN . '/parent/checkout/success.php',
  'cancel_url' => $YOUR_DOMAIN . '/parent/payment.php',
]);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Retrieve the form data
  $name = isset($_POST['name']) ? $_POST['name'] : '';
  $email = isset($_POST['email']) ? $_POST['email'] : '';
  $contact = isset($_POST['contact']) ? $_POST['contact'] : '';
  $address1 = isset($_POST['address1']) ? $_POST['address1'] : '';
  $postcode = isset($_POST['postcode']) ? $_POST['postcode'] : '';
  $state = isset($_POST['state']) ? $_POST['state'] : '';
  $paymentdetail = isset($_POST['paymentdetail']) ? $_POST['paymentdetail'] : '';
  $subTotal = isset($_POST['subtotal']) ? $_POST['subtotal'] : '';
  $tax = isset($_POST['tax']) ? $_POST['tax'] : ''; 
  $serviceCharge = isset($_POST['servicecharge']) ? $_POST['servicecharge'] : '';
  $total = isset($_POST['total']) ? $_POST['total'] : '';

  // Create an array with your data
  $data = array(
    'name' => $name,
    'email' => $email,
    'contact' => $contact,
    'address1' => $address1,
    'postcode' => $postcode,
    'state' => $state,
    'paymentdetail' => $paymentdetail,
    'subTotal' => $subTotal,
    'tax' => $tax,
    'serviceCharge' => $serviceCharge,
    'total' => $total
  );

  // Use the session username as part of the filename
  $filename = $_SESSION['username'] . '_temp_data.json';

  // Specify the folder path
  $folderPath = 'tmp/';

  // Save the JSON string to a file in the specified folder
  file_put_contents($folderPath . $filename, $jsonData);
}

header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);
?>