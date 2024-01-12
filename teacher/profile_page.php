<?php    
session_start();    
include '../config.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $query = "SELECT * FROM teacher WHERE t_username = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $_SESSION['username']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $user_data = mysqli_fetch_assoc($result);
            } else {
                echo "Error!.";
            }
        } else {
            echo "Error!" . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error!" . mysqli_error($conn);
    }

    mysqli_close($conn);
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>View Profile</title>
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
                            <br><h4>My Profile : <span><?= $user_data['t_name']?></span></h4></label>
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
                                                    <img src="..\assets/img/male-placeholder-image.jpeg" alt="Profile Picture" class="picture rounded-circle">
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
                                                                <span><?= $user_data['t_role']?></span>
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
                                                                <span><?= $user_data['t_name']?></span>
                                                            </div>
                                                        </div>
                                                    </div>
    
                                                    <!-- AGE -->
                                                    <div class="row m-0 p-1">
                                                        <!-- Left column for type of information -->
                                                        <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                            <p>Age</p>
                                                        </div>
    
                                                        <!-- Right column for corresponding information -->
                                                        <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                            <div class="white_box">
                                                                <span><?= $user_data['t_age']?></span>
                                                            </div>
                                                        </div>
                                                    </div>
    
                                                    <!-- PHONE -->
                                                    <div class="row m-0 p-1">
                                                        <!-- Left column for type of information -->
                                                        <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                            <p>Phone Num.</p>
                                                        </div>
    
                                                        <!-- Right column for corresponding information -->
                                                        <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                            <div class="white_box">
                                                                <span><?= $user_data['t_noPhone']?></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- PROFILE DETAILS -->
                            <div class="row">
                                <!-- COPY THIS ROW DIV TO CREATE NEW BOX -->
                                <div class="col-lg-12 col-xl-12 mb-4">
                                    <div class="card text-white bg-primary shadow">
                                        <div class="container p-2" id="textCont"><!--Alex:25/12/23-->
                                            <!-- COPY CLASS row m-0 p-1 TO CREATE NEW ROW/FIELD BOX -->

                                            <!-- Row for Gender -->
                                            <div class="row m-0 p-1">
                                                <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                    <p>Gender</p>
                                                </div>
                                                <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                    <div class="white_box">
                                                        <span><?= $user_data['t_gender']?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row for Race -->
                                            <div class="row m-0 p-1">
                                                <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                    <p>Race</p>
                                                </div>
                                                <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                    <div class="white_box">
                                                        <span><?= $user_data['t_race']?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row for Address -->
                                            <div class="row m-0 p-1">
                                                <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                    <p>Address</p>
                                                </div>
                                                <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                    <div class="white_box">
                                                        <span><?= $user_data['t_address']?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row for Status -->
                                            <div class="row m-0 p-1">
                                                <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                    <p>Status</p>
                                                </div>
                                                <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                    <div class="white_box">
                                                        <span><?= $user_data['t_marritalStatus']?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row for Qualification -->
                                            <div class="row m-0 p-1">
                                                <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                    <p>Qualification</p>
                                                </div>
                                                <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                    <div class="white_box">
                                                        <span><?= $user_data['t_qualification']?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row for Program -->
                                            <div class="row m-0 p-1">
                                                <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                    <p>Program</p>
                                                </div>
                                                <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                    <div class="white_box">
                                                        <span><?= $user_data['t_program']?></span>
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
    header('Location: login.php');
}
?>
