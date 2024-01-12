<?php
session_start();    
include '../../config.php';

function updatePaymentStatus($conn, $paymentdetail, $invoice_id) {
    if ($conn) {
        // Initialize an array to store p_ids
        $extracted_ids = array();

        // Use regular expression to match and extract p_ids
        preg_match_all('/ID: (\d+)/', $paymentdetail, $matches);

        // Check if matches were found
        if (isset($matches[1])) {
            // Add extracted p_ids to the array
            $extracted_ids = $matches[1];

            // Initialize a variable to track the success of all updates
            $allUpdatesSuccess = true;

            // Loop through each extracted p_id and update the corresponding row in the database
            foreach ($extracted_ids as $target_p_id) {
                // Insert into invoiceItems table
                $insertItemSuccess = insertIntoInvoiceItem($conn, $invoice_id, $target_p_id);

                // Check if the item insertion was successful
                if ($insertItemSuccess) {
                    // Update parentpayment table
                    $query = "UPDATE parentpayment SET p_status = 'Paid', p_invoice = ?, p_date = CURRENT_DATE WHERE p_id = ?";
                    $stmt = mysqli_prepare($conn, $query);

                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, 'ii', $invoice_id, $target_p_id);
                        mysqli_stmt_execute($stmt);

                        // Check if the update was successful
                        $success = mysqli_stmt_affected_rows($stmt) > 0;

                        // Update the overall success status
                        $allUpdatesSuccess = $allUpdatesSuccess && $success;

                        mysqli_stmt_close($stmt);
                    } else {
                        echo "Error: " . mysqli_error($conn);
                        $allUpdatesSuccess = false;
                    }
                } else {
                    // Handle the item insertion error
                    echo "Error inserting item into invoiceItems table.";
                    $allUpdatesSuccess = false;
                }
            }

            // Return the overall success status
            return $allUpdatesSuccess;
        }

        // Close the database connection
        mysqli_close($conn);
    }

    // Return false if the connection is not established
    return false;
}

function insertIntoInvoice($conn, $data) {
    // Define the SQL query with the new paymentDate column
    $query = "INSERT INTO invoice (name, email, contact, address1, address2, address3, postcode, state, subTotal, tax, serviceCharge, total, paymentDate)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";  // NOW() sets the paymentDate to the current date and time

    // Prepare the query
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, 'ssssssssddds', 
            $data['name'],
            $data['email'],
            $data['contact'],
            $data['address1'],
            $data['address2'],
            $data['address3'],
            $data['postcode'],
            $data['state'],
            $data['subTotal'],
            $data['tax'],
            $data['serviceCharge'],
            $data['total']);

        // Execute the statement
        $success = mysqli_stmt_execute($stmt);

        // Get the last inserted ID
        $invoice_id = mysqli_insert_id($conn);

        // Close the statement
        mysqli_stmt_close($stmt);

        return $success ? $invoice_id : false;
    } else {
        // Handle the error appropriately
        echo "Error: " . mysqli_error($conn);
        return false;
    }
}

function insertIntoInvoiceItem($conn, $invoice_id, $p_id) {
    // Define the SQL query
    $query = "INSERT INTO invoiceItems (invoice_id, p_id) VALUES (?, ?)";

    // Prepare the query
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        // Bind parameters
        mysqli_stmt_bind_param($stmt, 'ii', $invoice_id, $p_id);

        // Execute the statement
        $success = mysqli_stmt_execute($stmt);

        // Close the statement
        mysqli_stmt_close($stmt);

        return $success;
    } else {
        // Handle the error appropriately
        echo "Error: " . mysqli_error($conn);
        return false;
    }
}

unset($_SESSION['stripeCart']);
$tempID = $_GET['temp'];
$error = false;

$filename = $tempID . '.json';
$folderPath = 'tmp/';


echo 'Processing your payment.\n';
echo 'Please do not close the browser...\n';
echo 'Please contact TASKA UNIMAS if you encounter any problems.\n';


// Get Payment details from tmp file
// If file does not exist, Transaction fails
if (file_exists($folderPath . $filename)) {
    // Read the JSON content from the file
    $jsonContent = file_get_contents($folderPath . $filename);
    $data = json_decode($jsonContent, true);
    $_SESSION['invoice'] = $data;

    // Update fee/item status to paid
    // By: Alex / Ikhwan
    $invoice_id = insertIntoInvoice($conn, $data); //Returns false if fail, else, return invoice id for next operation
    $paymentStatusSuccess = updatePaymentStatus($conn, $data['paymentdetail'], $invoice_id);
    

    // Delete the temporary file
    unlink($folderPath . $filename);

    if ($paymentStatusSuccess && $invoice_id) {
        echo "Payment was Successful. Redirecting...\n";
        $_SESSION['invoice_id'] = $invoice_id;
        
        // Redirect to success.php
        header("Location: ../success.php");
        exit(); // Ensure that no further output is sent
    } else {
        echo 'There was a problem occurred. Please contact admin @ TASKA UNIMAS\n';
        echo '<a href="../payment.php">Back</a>';
    }
} else {
    echo "There was a problem occurred. Please contact admin @ TASKA UNIMAS\n";
    echo '<a href="../payment.php">Back</a>';
}

?>