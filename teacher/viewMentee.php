<?php
session_start();
include '../config.php';

$mentee_name = $_GET['name'];

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }


    // query by ikhwan 28/12/2023
    $query="SELECT * FROM children WHERE c_name = ?";

    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $mentee_name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $user_data = mysqli_fetch_assoc($result);

                // DEBUGGING PURPOSES. THIS JUST PRINTS THE RESULT INTO CONSOLE LOG
                // $user_data_json = json_encode($user_data);
                // echo '<script>console.log("User Data:", ' . $user_data_json . ');</script>';
            } else {
                echo "";
            }
        } else {
            echo "Error! " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error! " . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    // IF NOT LOGGED IN, redirect to login page
    header('Location: loginTeacher.php');
    exit();
}
?>

<!-- START HTML -->
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>View Profile : <?= $user_data['name']?></title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hammersmith+One&amp;display=swap">
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/css/untitled.css">
</head>

<body id="page-top">
    <div id="wrapper">
        <nav class="navbar align-items-start sidebar sidebar-dark accordion bg-gradient-primary navbar-dark" id="sidebar"> <!--Alex:25/12/23-->
            <div class="container-fluid d-flex flex-column p-0" ><a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="#" style="font-size: larger;">
                    
                    <div class="sidebar-brand-text mx-3"><span id="sidebar_label">taska unimas</span></div>
                </a>
                <hr class="sidebar-divider my-0">
                <ul class="navbar-nav text-light mr-auto" id="accordionSidebar">
                    <li class="nav-item"><a class="nav-link" href="dashboardTeacher.php"><img class="logoH" src="..\assets\img\icons\home.png" alt=""></i><span>HOME</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="profilepageTeacher.php"><img class="logoH" src="..\assets\img\icons\profile.png" alt=""></i><span>PROFILE</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="menteeTeacher.php"><img class="logoH" src="..\assets\img\icons\mentor.png" alt=""></i><span>MENTOR MENTEE</span></a></li>
                </ul>
                <div class="text-center d-none d-md-inline"><button class="btn rounded-circle border-0" id="sidebarToggle" type="button"></button></div>
                <!-- <button class="btn btn-primary" id="logout" type="button">Log out</button> -->
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
                            <br><h4>View Profile : <span><?= $user_data['c_name']?></span></span></h4></label>
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
                                            <div class="row m-0" id="textCont"> <!--Alex:25/10/23-->

                                                <!-- Profile Picture Column (Left) -->
                                                <div class="col-md-3 d-flex justify-content-center align-items-center">
                                                    <img src="..\assets/img/boy-placeholder-image.jpg" alt="Profile Picture" class="picture rounded-circle">
                                                </div>
    
    
                                                <!-- Profile Details Column (Right) -->
                                                <div class="m-0 p-0 col-md-9">
                                                    <!-- COPY ROW TO CREATE NEW ROW/FIELD BOX -->
    
                                                    <!-- ROLES -->
                                                    <div class="row m-0 p-1">
                                                        <!-- Left column for type of information -->
                                                        <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                            <p>Child's ID</p>
                                                        </div>
    
                                                        <!-- Right column for corresponding information -->
                                                        <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                            <div class="white_box">
                                                                <span><?= $user_data['c_registerID']?></span>
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
                                                                <span><?= $user_data['c_name']?></span>
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
                                                                <span><?= $user_data['c_age']?></span>
                                                            </div>
                                                        </div>
                                                    </div>
    
                                                    <!-- PHONE -->
                                                    <div class="row m-0 p-1">
                                                        <!-- Left column for type of information -->
                                                        <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                            <p>Enrollment Date</p>
                                                        </div>
    
                                                        <!-- Right column for corresponding information -->
                                                        <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                            <div class="white_box">
                                                                <span><?= $user_data['c_enrollmentDate']?></span>
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
                                                        <span><?= $user_data['c_gender']?></span>
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
                                                        <span><?= $user_data['c_race']?></span>
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
                                                        <span><?= $user_data['c_address']?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row for Birth Cert. -->
                                            <div class="row m-0 p-1">
                                                <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                    <p>Birth Certificate</p>
                                                </div>
                                                <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                    <div class="white_box">
                                                        <span><?= $user_data['c_birthCertificate']?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row for Father's Name -->
                                            <div class="row m-0 p-1">
                                                <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                    <p>Father's Name</p>
                                                </div>
                                                <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                    <div class="white_box">
                                                        <span><?= $user_data['c_FatherName']?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row for Father's Phone -->
                                            <div class="row m-0 p-1">
                                                <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                    <p>Father's Phone</p>
                                                </div>
                                                <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                    <div class="white_box">
                                                        <span><?= $user_data['c_FatherPhoneNo']?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row for Mother's Name -->
                                            <div class="row m-0 p-1">
                                                <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                    <p>Mother's Name</p>
                                                </div>
                                                <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                    <div class="white_box">
                                                        <span><?= $user_data['c_MotherName']?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row for Mother's Phone -->
                                            <div class="row m-0 p-1">
                                                <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                    <p>Mother's Phone</p>
                                                </div>
                                                <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                    <div class="white_box">
                                                        <span><?= $user_data['c_MotherPhoneNo']?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row for UNIMAS STAFF -->
                                            <div class="row m-0 p-1">
                                                <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                    <p>UNIMAS Staff</p>
                                                </div>
                                                <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                    <div class="white_box">
                                                        <span><?= $user_data['c_UNIMASstaff']?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row for Disabilities -->
                                            <div class="row m-0 p-1">
                                                <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                    <p>DISABILITIES</p>
                                                </div>
                                                <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                    <div class="white_box">
                                                        <span><?= $user_data['c_Disabilities']?></span>
                                                    </div>
                                                </div>
                                            </div>


                                            <!-- Row for Allergies -->
                                            <div class="row m-0 p-1">
                                                <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                    <p>Allergies</p>
                                                </div>
                                                <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                    <div class="white_box">
                                                        <span><?= $user_data['c_Allergies']?></span>
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

<!-- END HTML -->
