<?php    
session_start();    
include '../config.php';
$user_data =[];
$user_data2 =[];

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Fikri 01/01/2024
    // $sql = "SELECT * FROM parentpayment WHERE c_id=?";
    // $result = $conn->query($sql);

    // Ikhwan 02/01/2024
    // New query
    $query = "SELECT * FROM parentpayment WHERE c_id = ? AND p_status='Pending'";
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
                //echo "Error!. 1";
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
                        <div class="col">
                            <!-- MAIN PROFILE -->
                            <form action="checkout.php" method="post">
                                <div class="card text-white bg-primary shadow">
                                    <div class="container p-4">
                                        <div class="row">
                                            <div class="container">
                                                <div class="row">
                                                    <div class="col-1">No.</div>
                                                    <div class="col-1">ID</div>
                                                    <div class="col-5">Payment Details</div>
                                                    <div class="col-2">Fee (RM)</div>
                                                    <div class="col-2">Status</div>
                                                    <div class="col-1">Select</div>
                                                </div>

                                                <?php if (!empty($user_data)): ?>
                                                    <?php foreach ($user_data as $index => $row): ?>
                                                        <div class="row bg-white rounded text-black mt-2 py-1">
                                                            <div class="col-1"><?= $index + 1 ?></div>
                                                            <div class="col-1"><?= $row['p_id'] ?></div>
                                                            <div class="col-5"><?= $row['p_name'] ?></div>
                                                            <div class="col-2"><?= $row['p_price'] ?></div>
                                                            <div class="col-2"><?= $row['p_status'] ?></div>
                                                            <div class="col-1 d-flex align-items-center justify-content-center">
                                                                <input type="checkbox" name="selected_rows[]" value="<?= $row['p_id'] ?>">
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <div class="row mt-3">
                                                        <div class="col-12 text-center fw-bold">
                                                            There are no outstanding fees currently.
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col d-flex justify-content-between mt-2">
                                        <a href="viewPaymentHistory.php" class="btn btn-warning text-white" role="button">Payment History</a>
                                        <button type="submit" class="btn btn-success text-white">Checkout</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>

</html>

<?php
    } else {
        header('Location: login.php');
    }
?>
