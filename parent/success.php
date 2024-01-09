<?php

session_start();    
include '../config.php';
// Check if the form has been submitted
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

    // Display the data in an invoice-like format
    echo "<h2>Invoice</h2>";
    echo "<hr>";
    echo "<p><strong>Customer Name:</strong> $name</p>";
    echo "<p><strong>Email:</strong> $email</p>";
    echo "<p><strong>Contact Number:</strong> $contact</p>";
    echo "<p><strong>Billing Address:</strong> $address1</p>";
    echo "<p><strong>Postcode:</strong> $postcode</p>";
    echo "<p><strong>State:</strong> $state</p>";
    echo "<p><strong>Payment Details :<br><br></strong> $paymentdetail</p>";
    echo "<hr>";
    echo "<p><strong>Sub Total:</strong> $subTotal</p>";
    echo "<p><strong>Tax:</strong> $tax</p>";
    echo "<p><strong>Service Charge:</strong> $serviceCharge</p>";
    echo "<p><strong>Total:</strong> $total</p>";
    
    // Redirect or display a success message
    echo '<script>';
    echo 'alert("Payment Successful.");';
    echo 'alert("Invoice of the payment has been sent to your email.");';
    echo '</script>';

    // Assuming you have a valid database connection in $conn
    if ($conn) {
        // Initialize an array to store p_ids
        $extracted_ids = array();

        // Use regular expression to match and extract p_ids
        preg_match_all('/ID: (\d+)/', $paymentdetail, $matches);

        // Check if matches were found
        if (isset($matches[1])) {
            // Add extracted p_ids to the array
            $extracted_ids = $matches[1];

            // Loop through each extracted p_id and update the corresponding row in the database
            foreach ($extracted_ids as $target_p_id) {
                $query = "UPDATE parentpayment SET p_status = 'Pending' WHERE p_id = ?";
                $stmt = mysqli_prepare($conn, $query);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'i', $target_p_id);
                    mysqli_stmt_execute($stmt);

                    mysqli_stmt_close($stmt);
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
            }
        }

        // Close the database connection
        mysqli_close($conn);
    }

    
    // Optionally, you can include a back button or a link to redirect the user
    echo '<a href="payment.php">Back</a>';
} else {
    // If the form hasn't been submitted, display an error or redirect
    echo "Error: Form data not submitted.";
    echo '<a href="javascript:history.back()">Back</a>';
    // You may want to add a redirect header or additional error handling.
}
?>
