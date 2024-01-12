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
                echo "Error!. 1";
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
    <title>View Payment</title>
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
                <div class="container p-4">
                    <div class="row">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Payment Detail</th>
                                    <th scope="col">Fee (RM)</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Invoice</th>
                                    <!-- Add more columns as needed -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Loop through the fetched data and display it in the table
                                // Fikri 01-01-2024
                                foreach ($user_data as $row) {
                                    echo "<tr>";
                                    echo "<td>" . $row['p_id'] . "</td>";
                                    echo "<td>" . $row['p_name'] . "</td>";
                                    echo "<td>" . $row['p_price'] . "</td>";
                                    echo "<td>" . $row['p_date'] . "</td>";
                                    echo '<td>
                                            <button class="btn btn-link view-invoice" data-pinvoice="' . $row['p_invoice'] . '">View Invoice</button>
                                        </td>';
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

            <div style="padding-top: 5rem;"></div> <!-- Alex: 26/12/23 Add empty space between footer-->
            <footer class="bg-white sticky-footer">
                <div class="container my-auto">
                    <div class="text-center my-auto copyright"><span>Copyright © Brand 2023</span></div>
                </div>
            </footer>
        </div><a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>
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
