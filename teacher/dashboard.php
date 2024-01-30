<?php    
session_start();    
include '../config.php';
$user_data =[];
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

<!-- START OF HTML -->
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
                            <br><h4>Welcome <span><?= $user_data['t_name']?></span> !</h4></label>
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
                                                    <img src="../data/img/teacher/<?= $user_data['t_profilePicture'] ?>" style="width:150px; height:150px; object-fit:cover;" alt="Profile Picture" class="picture rounded-circle">
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
    
                                                    <!-- PHONE -->
                                                    <div class="row m-0 p-1">
                                                        <!-- Left column for type of information -->
                                                        <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                            <p>No Phone</p>
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
                                            
                                            <!-- Bootstrap carousel -->
                                            <!-- Ikhwan 31 Dec -->
                                            <div class="row text-center p-2">
                                                    <h4>Timetables</h4>
                                                </div>
                                                <div class="row p-2">
                                                    <!-- CAROUSEL FOR TIMETABLES -->
                                                    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                                                        <div class="carousel-inner">
                                                          <div class="carousel-item active">
                                                            <img src="..\assets\img\timetables\jadual1.jpg" class="d-block w-100" alt="...">
                                                          </div>
                                                          <?php if ($user_data['t_program'] == "Under Age 1" || $user_data['t_program'] == "Age 2"): ?>
                                                            <div class="carousel-item active">
                                                                <img src="..\assets\img\timetables\jadual2.jpg" class="d-block w-100" alt="...">
                                                            </div>
                                                          <?php endif; ?> <!-- Alex 11.1.24 - Add function to detect teacher program and display schedule onl related to the program -->
                                                          <!-- <div class="carousel-item">
                                                            <img src="..\assets\img\timetables\jadual2.jpg" class="d-block w-100" alt="...">
                                                          </div> -->
                                                          <?php if ($user_data['t_program'] == "Age 3" || $user_data['t_program'] == "Age 4"): ?>
                                                            <div class="carousel-item active">
                                                                <img src="..\assets\img\timetables\jadual3.jpg" class="d-block w-100" alt="...">
                                                            </div>
                                                          <?php endif; ?> <!-- Alex 11.1.24 - Add function to detect teacher program and display schedule onl related to the program -->
                                                          <!-- <div class="carousel-item"> 
                                                            <img src="..\assets\img\timetables\jadual3.jpg" class="d-block w-100" alt="...">
                                                          </div> -->
                                                          <div class="carousel-item">
                                                            <img src="..\assets\img\timetables\jadual4.jpg" class="d-block w-100" alt="...">
                                                          </div>
                                                          <div class="carousel-item">
                                                            <img src="..\assets\img\timetables\jadual5.jpg" class="d-block w-100" alt="...">
                                                          </div>
                                                        </div>
                                                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                                                          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                          <span class="visually-hidden">Previous</span>
                                                        </button>
                                                        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                                                          <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                          <span class="visually-hidden">Next</span>
                                                        </button>

                                                        <div class="carousel-indicators">
                                                            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                                            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                                            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                                                            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3" aria-label="Slide 3"></button>
                                                            <!-- <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="4" aria-label="Slide 3"></button> -->
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

<?php
} else {
    header('Location: login.php');
}
?>
