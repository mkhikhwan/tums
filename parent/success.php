<?php
function printDetails($data) {
    // Retrieve the form data
    $name = isset($data['name']) ? $data['name'] : '';
    $email = isset($data['email']) ? $data['email'] : '';
    $contact = isset($data['contact']) ? $data['contact'] : '';
    $address1 = isset($data['address1']) ? $data['address1'] : '';
    $postcode = isset($data['postcode']) ? $data['postcode'] : '';
    $state = isset($data['state']) ? $data['state'] : '';
    $paymentdetail = isset($data['paymentdetail']) ? $data['paymentdetail'] : '';
    $subTotal = isset($data['subtotal']) ? $data['subtotal'] : '';
    $tax = isset($data['tax']) ? $data['tax'] : ''; 
    $serviceCharge = isset($data['servicecharge']) ? $data['servicecharge'] : '';
    $total = isset($data['total']) ? $data['total'] : '';

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
}

session_start();
$invoice_details = $_SESSION['invoice'];
?>

<!-- START OF HTML -->
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Payment</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hammersmith+One&amp;display=swap">
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/css/untitled.css">

</head>

<body id="page-top">
    <div id="wrapper">
        <!-- Include using php -->
        <?php include('sidemenu.php'); ?>


        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">

                <!-- HEADER -->
                <nav class="navbar navbar-expand bg-white shadow mb-4 topbar static-top navbar-light">
                    <div class="container-fluid header"><button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button">
                        <i class="fas fa-bars"></i></button>
                        <label class="form-label fs-3 text-nowrap" id="label_welcome">
                            <br><h4>Payment</h4></label>
                    </div>
                </nav>

                <!-- MAIN CONTENT -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="
                            p-3
                            col bg-light 
                            d-flex-inline 
                            justify-content-center 
                            align-items-center 
                            text-center
                            border border-success
                            ">
                                <div class="col d-flex-inline align-items-center justify-content-center mt-3">
                                    <img src="..\assets\img\checkmark-xxl.png" alt="Checkmark" style="width: 30px; height: auto;" class="mb-1">
                                    <h3 class="text-success fw-bold fs-2 mt-2">Payment Successful</h3>
                                </div>

                                <div class="col mt-2">
                                    <p>
                                        We have received your payment successfully. A receipt is also sent to your E-mail.
                                    </p>
                                    <p>
                                        Any Inquiries please contact <strong>Admin @ TASKA UNIMAS</strong>
                                    </p>
                                </div>

                                <div class="mt-4">
                                    <button class="btn btn-primary me-2" onclick="window.location.href='payment.php'">Back</button>
                                    <!-- Use the appropriate function or link for viewing the receipt -->
                                    <button class="btn btn-success text-white" onclick="viewReceipt(<?php echo $_SESSION['invoice_id']; ?>)">View Receipt</button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/js/theme.js"></script>
    <script>
        function viewReceipt(invoiceID) {
            // Create a form element
            var form = document.createElement('form');
            form.action = '../generateInvoice.php'; // Replace with the actual path
            form.method = 'post';
            form.target = '_blank'; // Set the target to open in a new tab

            // Create a hidden input field for 'invoiceID'
            var inputInvoiceID = document.createElement('input');
            inputInvoiceID.type = 'hidden';
            inputInvoiceID.name = 'invoiceID';
            inputInvoiceID.value = invoiceID;
            form.appendChild(inputInvoiceID);

            // Append the form to the document body
            document.body.appendChild(form);

            // Submit the form
            form.submit();
        }
    </script>
</body>

</html>