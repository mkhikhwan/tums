<?php
function generateRandomString($length = 10) {
  $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  $randomString = '';

  for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
  }

  return $randomString;
}

// Stripe Checkout Backend
// By ikhwan 05-01-2024
use Stripe\Terminal\Location;

session_start();
require_once('../../lib/stripe/init.php');
require_once '../../lib/stripe/secrets.php';

\Stripe\Stripe::setApiKey($stripeSecretKey);
header('Content-Type: application/json');
$YOUR_DOMAIN = 'https://taskaunimas.online';

// Grab items from last page
$stripeCart = $_SESSION['stripeCart'];

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

  // Convert the PHP array to a JSON string
  $jsonData = json_encode($data);

  // ID to reference when successful
  $tempID = generateRandomString();
  $filename = $tempID . '.json';

  // Specify the folder path
  $folderPath = 'tmp/';

  // Save the JSON string to a file in the specified folder
  file_put_contents($folderPath . $filename, $jsonData);
}

// Open Stripe Session
$checkout_session = \Stripe\Checkout\Session::create([
  'line_items' => $stripeCart,
  'mode' => 'payment',
  'success_url' => $YOUR_DOMAIN . '/parent/checkout/processInvoice.php?temp=' . $tempID,
  'cancel_url' => $YOUR_DOMAIN . '/parent/payment.php',
]);

header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);
?>