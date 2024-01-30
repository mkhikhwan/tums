<?php
session_start();
include '../config.php';

$childid = $_GET['id'];

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }


    // query by ikhwan 28/12/2023
    $query="SELECT * FROM children WHERE c_id = ?";

    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $childid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $user_data = mysqli_fetch_assoc($result);

                $image_path = '../data/img/children/' . $user_data['c_profilePicture'];

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

    $query2="SELECT t.t_name, t.t_id as t_id FROM mentormentee mt 
    JOIN teacher t on mt.MentorID = t.t_id 
    JOIN children c ON mt.MenteeID = c.c_id 
    WHERE c.c_id = ? ";

    $stmt2 = mysqli_prepare($conn, $query2);

    if ($stmt2) {
        mysqli_stmt_bind_param($stmt2, 's', $childid);
        mysqli_stmt_execute($stmt2);
        $result2 = mysqli_stmt_get_result($stmt2);

        if ($result2) {
            if (mysqli_num_rows($result2) > 0) {
                $user_data2 = mysqli_fetch_assoc($result2);
            } else {
                $noMentor = true;
            }
        } else {
            echo "Error!" . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt2);
    } else {
        echo "Error!" . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    // IF NOT LOGGED IN, redirect to login page
    header('Location: login.php');
    exit();
}
?>

<!-- START HTML -->
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
                                                    <img src="<?php echo $image_path ?>" alt="Profile Picture" class="picture rounded-circle" style="width: 160px; height: 160px; object-fit: cover;">
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
                                                    <p>Disabilities</p>
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

                                            <!-- Row for Program -->
                                            <div class="row m-0 p-1">
                                                <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                    <p>Program</p>
                                                </div>
                                                <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                    <div class="white_box">
                                                        <span><?= $user_data['c_program']?></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row for Mentor -->
                                            <div class="row m-0 p-1">
                                                <div class="text-nowrap text-center col-sm-2 col-md-2 d-flex justify-content-center align-items-center">
                                                    <p>Mentor</p>
                                                </div>
                                                <div class="col-sm-10 col-md-10 d-flex gx-0 justify-content-center align-items-center text-center">
                                                    <div class="white_box">
                                                        <span>
                                                            <?php
                                                                if ($noMentor) {
                                                                    echo "None";
                                                                } else {
                                                                    // Assuming $user_data2['t_id'] is the identifier for the mentor
                                                                    echo '<a href="viewTeacher.php?id=' . $user_data2['t_id'] . '">' . $user_data2['t_name'] . '</a>';
                                                                }
                                                            ?>
                                                        </span>
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

<!-- END HTML -->
