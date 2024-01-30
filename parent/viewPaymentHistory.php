<?php    
session_start();    
include '../config.php';
$user_data =[];
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Fikri 01/01/2024
    // $sql = "SELECT * FROM parentpayment WHERE c_id=?";
    // $result = $conn->query($sql);

    // Ikhwan 02/01/2024
    // New query
    $query = "SELECT * FROM parentpayment WHERE c_id=? AND p_status='Paid'";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $_SESSION['childID']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                // Initialize an array to store all rows
                $user_data = array();

                // Fetch all rows and store them in the array
                while ($row = mysqli_fetch_assoc($result)) {
                    $user_data[] = $row;
                }
            } else {
                $noPaymentHistory = true;
            }
        } else {
            echo "Error! 2" . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error! 3" . mysqli_error($conn);
    }

    $query2 = "SELECT * FROM children WHERE c_username = ?";
    $stmt2 = mysqli_prepare($conn, $query2);

    if ($stmt2) {
        mysqli_stmt_bind_param($stmt2, 's', $_SESSION['username']);
        mysqli_stmt_execute($stmt2);
        $result2 = mysqli_stmt_get_result($stmt2);

        if ($result2) {
            if (mysqli_num_rows($result2) > 0) {
                $user_data2 = mysqli_fetch_assoc($result2);
            } else {
                echo "Error!. 1";
            }
        } else {
            echo "Error! 2" . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt2);
    } else {
        echo "Error! 3" . mysqli_error($conn);
    }

    mysqli_close($conn);
?>

<!-- START OF HTML -->
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Payment History</title>
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
                            <br><h4>Payment History</h4></label>
                    </div>
                </nav>

                <!-- MAIN PROFILE -->
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="card bg-primary shadow text-white">
                                <div class="container p-4">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-1">No.</div>
                                            <div class="col-1">ID</div>
                                            <div class="col-4">Payment Details</div>
                                            <div class="col-2">Fee (RM)</div>
                                            <div class="col-2">Date</div>
                                            <div class="col-2">Invoice</div>
                                        </div>

                                        <?php if (!empty($user_data)): ?>
                                            <?php foreach ($user_data as $index => $row): ?>
                                                <div class="row bg-white rounded text-black mt-2 py-1">
                                                    <div class="col-1"><?= $index + 1 ?></div>
                                                    <div class="col-1"><?= $row['p_id'] ?></div>
                                                    <div class="col-4"><?= $row['p_name'] ?></div>
                                                    <div class="col-2"><?= $row['p_price'] ?></div>
                                                    <div class="col-2"><?= $row['p_date'] ?></div>
                                                    <div class="col-2">
                                                        <button class="btn btn-link view-invoice p-0 m-0" data-pinvoice="<?= $row['p_invoice'] ?>">View Invoice</button>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="row mt-3">
                                                <div class="col-12 text-center fw-bold">
                                                    No payment has been done.
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/js/theme.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Add a click event listener to all elements with the class 'view-invoice'
            var viewInvoiceButtons = document.querySelectorAll('.view-invoice');
            viewInvoiceButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    // Get the value of 'data-pinvoice' attribute
                    var pinvoice = this.getAttribute('data-pinvoice');

                    // Create a form element
                    var form = document.createElement('form');
                    form.action = '../generateInvoice.php';
                    form.method = 'post';

                    // Create a hidden input field for 'p_invoice'
                    var inputPInvoice = document.createElement('input');
                    inputPInvoice.type = 'hidden';
                    inputPInvoice.name = 'invoiceID';
                    inputPInvoice.value = pinvoice;

                    // Append the hidden input field to the form
                    form.appendChild(inputPInvoice);

                    // Append the form to the document body
                    document.body.appendChild(form);

                    // Submit the form
                    form.submit();
                });
            });
        });
    </script>
</body>

</html>

<?php
    } else {
        header('Location: viewPayment.php');
    }
?>
