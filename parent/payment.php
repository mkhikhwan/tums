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
    <title>View Payment</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hammersmith+One&amp;display=swap">
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/css/untitled.css">

</head>

<body id="page-top">
    <div id="wrapper">
        <nav class="navbar align-items-start sidebar sidebar-dark accordion bg-gradient-primary navbar-dark" id="sidebar"> <!-- Alex:25/12/23: Add ID -->
            <div class="container-fluid d-flex flex-column p-0" ><a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="#" style="font-size: larger;">
                    
                    <div class="sidebar-brand-text mx-3"><span id="sidebar_label">taska unimas</span></div>
                </a>
                <hr class="sidebar-divider my-0">
                <ul class="navbar-nav text-light mr-auto" id="accordionSidebar">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><img class="logoH" src="..\assets\img\icons\home.png" alt=""></i><span>HOME</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="profilepageChild.php"><img class="logoH" src="..\assets\img\icons\profile.png" alt=""></i><span>PROFILE</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="payment.php"><img class="logoH" src="..\assets\img\icons\credit-card.png" alt=""></i><span>PAYMENT</span></a></li>
                </ul>
                <div class="text-center d-none d-md-inline"><button class="btn rounded-circle border-0" id="sidebarToggle" type="button"></button></div>
                    <!-- <button class="btn btn-primary" id="logout" type="button"></button> -->
                    <a href="../logout.php" class="btn btn-primary" id="logout">Log out</a>
            </div>
        </nav>

        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">

                <!-- HEADER -->
                <nav class="navbar navbar-expand bg-white shadow mb-4 topbar static-top navbar-light">
                    <div class="container-fluid header"><button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button">
                        <i class="fas fa-bars"></i></button>
                        <label class="form-label fs-3 text-nowrap" id="label_welcome">
                            <br><h4>My Profile : <span><?= $user_data2['c_name']?></span></h4></label>
                    </div>
                </nav>

                <!-- MAIN CONTENT -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <!-- MAIN PROFILE -->
                            <div class="container p-4">
                                <form action="checkout.php" method="post">
                                    <div class="row">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Payment Detail</th>
                                                    <th scope="col">Fee (RM)</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Select</th>
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
                                                    echo "<td>" . $row['p_status'] . "</td>";
                                                    echo '<td><input type="checkbox" name="selected_rows[]" value="' . $row['p_id'] . '"></td>';

                                                    echo "</tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="row">
                                        <div class="col d-flex justify-content-between">
                                            <a href="viewPaymentHistory.php" class="btn btn-warning" role="button">Payment History</a>
                                            <button type="submit" class="btn btn-success">Checkout</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
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
</body>

</html>

<?php
    } else {
        header('Location: viewPayment.php');
    }
?>
