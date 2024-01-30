<?php    
session_start();    
include '../config.php';
$user_data =[];
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $query = "SELECT * FROM administrator WHERE a_username = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $_SESSION['username']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $user_data = mysqli_fetch_assoc($result);
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

    mysqli_close($conn);
?>

<!-- start -->
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Dashboard</title>
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
                            <br><h4>Welcome <?= $user_data['a_name']?> !</h4></label>
                    </div>
                </nav>

                <!-- MAIN CONTENT -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">

                            <!-- MAIN PROFILE -->
                            <div class="row">
                            <!-- COPY THIS ROW DIV TO CREATE NEW BOX -->
                                <div class="col-lg-12 col-xl-12 mb-4">
                                    <div class="card text-white bg-primary shadow">
                                        <div class="container p-2">
                                            <div class="row m-0" id="textCont"> <!-- Alex:25/10/23: Add ID -->

                                                <!-- Profile Picture Column (Left) -->
                                                <div class="col-md-3 d-flex justify-content-center align-items-center">
                                                    <img src="../data/img/admin/<?= $user_data['a_profilePicture'] ?> " alt="Profile Picture" style="width: 150px; height: 150px; object-fit: cover;" class="picture rounded-circle">
                                                </div>
    
    
                                                <!-- Profile Details Column (Right) -->
                                                <div class="m-0 p-0 col-md-9">
                                                    <!-- COPY ROW TO CREATE NEW ROW/FIELD BOX -->
    
                                                    <!-- ROLES -->
                                                    <div class="row m-0 p-1">
                                                        <!-- Left column for type of information -->
                                                        <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                            <p>Roles</p>
                                                        </div>
    
                                                        <!-- Right column for corresponding information -->
                                                        <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                            <div class="white_box">
                                                                <span>Admin</span>
                                                            </div>
                                                        </div>
                                                    </div>
    
                                                    <!-- NAME -->
                                                    <div class="row m-0 p-1">
                                                        <!-- Left column for type of information -->
                                                        <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                            <p>Name</p>
                                                        </div>
    
                                                        <!-- Right column for corresponding information -->
                                                        <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                            <div class="white_box">
                                                                <span><?= $user_data['a_name']?></span>
                                                            </div>
                                                        </div>
                                                    </div>
    
                                                    <!-- PHONE -->
                                                    <div class="row m-0 p-1">
                                                        <!-- Left column for type of information -->
                                                        <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                            <p>No Phone</p>
                                                        </div>
    
                                                        <!-- Right column for corresponding information -->
                                                        <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                            <div class="white_box">
                                                                <span><?= $user_data['a_phoneNo']?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
    
                                                </div>
                                            </div>
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
</body>

</html>
<!-- end -->

<?php
} else {
    header('Location: loginAdmin.php');
}
?>


