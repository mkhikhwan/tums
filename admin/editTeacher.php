<?php
session_start();
include '../config.php';

// Verify if User is logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $selectTeacherID = $_GET['id'];

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        modifyTeacherData($conn, $selectTeacherID);

        // Redirect to the same page using GET to avoid resubmission
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    $user_data = getTeacherData($conn, $selectTeacherID);

    mysqli_close($conn);
} else {
    header('Location: login.php');
    exit();
}

function getTeacherData($conn, $teacherID) {
    $query = "SELECT * FROM teacher WHERE t_id = ?";

    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $teacherID);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $user_data = mysqli_fetch_assoc($result);
                mysqli_stmt_close($stmt);
                return $user_data;
            } else {
                echo "";  // Consider handling this case differently based on your requirements
            }
        } else {
            echo "Error! " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error! " . mysqli_error($conn);
    }

    return null;
}

function modifyTeacherData($conn, $teacherID) {
    // Modify Teacher data
    // By Aina
    // Modified by Ikhwan 04-Jan-2024

    $username = mysqli_real_escape_string($conn, $_POST['t_username']);
    $password = mysqli_real_escape_string($conn, $_POST['t_password']);
    $name = mysqli_real_escape_string($conn, $_POST['t_name']);
    $noPhone = mysqli_real_escape_string($conn, $_POST['t_noPhone']);
    $marritalStatus = mysqli_real_escape_string($conn, $_POST['t_marritalStatus']);
    $qualification = mysqli_real_escape_string($conn, $_POST['t_qualification']);
    $program = mysqli_real_escape_string($conn, $_POST['t_program']);
    $role = mysqli_real_escape_string($conn, $_POST['t_role']);
    $age = mysqli_real_escape_string($conn, $_POST['t_age']);
    $race = mysqli_real_escape_string($conn, $_POST['t_race']);
    $address = mysqli_real_escape_string($conn, $_POST['t_address']);
    $gender = mysqli_real_escape_string($conn, $_POST['t_gender']);

    // Query to edit the table "teachers"
    $query = "UPDATE teachers SET
        t_username=?, t_password=?, t_name=?, t_noPhone=?, t_marritalStatus=?, t_qualification=?,
        t_program=?, t_role=?, t_age=?, t_race=?, t_address=?, t_gender=?
        WHERE t_id=?";
    
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ssssssssssss', 
            $username, $password, $name, $noPhone, $marritalStatus, $qualification,
            $program, $role, $age, $race, $address, $gender, $teacherID);

        $success = mysqli_stmt_execute($stmt);

        // Add custom message
        // Ikhwan 04-01-2024
        if ($success) {
            $_SESSION['modify_message'] = "Modify successful";
        } else {
            $_SESSION['modify_message'] = "Error modifying teacher data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo '<script>alert("Error! ' . mysqli_error($conn) . '");</script>';
    }
}
?>
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Register New Teacher</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hammersmith+One&amp;display=swap">
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/css/untitled.css">
</head>

<body id="page-top">
    <div id="wrapper">
        <nav class="navbar align-items-start sidebar sidebar-dark accordion bg-gradient-primary navbar-dark" id="sidebar">
            <div class="container-fluid d-flex flex-column p-0"><a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="#" style="font-size: larger;">
                    <div class="sidebar-brand-text mx-3"><span id="sidebar_label">taska unimas</span></div>
                </a>
                <hr class="sidebar-divider my-0">
                <ul class="navbar-nav text-light mr-auto" id="accordionSidebar">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><img class="logoH" src="..\assets\img\icons\home.png" alt=""></i><span>HOME</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="manageChildren.php"><img class="logoH" src="..\assets\img\icons\student.png" alt=""></i><span>Manage Children</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="manageTeacher.php"><img class="logoH" src="..\assets\img\icons\Teacher.png" alt=""></i><span>Manage Teachers</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="mentorMentee.php"><img class="logoH" src="..\assets\img\icons\mentor.png" alt=""></i><span>Mentor Mentee</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="viewPayment.php"><img class="logoH" src="..\assets\img\icons\credit-card.png" alt=""></i><span>Payment</span></a></li>
                </ul>
                <div class="text-center d-none d-md-inline"><button class="btn rounded-circle border-0" id="sidebarToggle" type="button"></button></div>
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
                            <br><h4>Edit Teacher</h4></label>
                    </div>
                </nav>

                <!-- MAIN CONTENT -->
                <div class="container-fluid">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-lg-12 col-xl-12 mb-4">
                                <div class="card text-white bg-primary shadow">
                                    <div class="container p-4">
                                        <!-- Teacher Information -->
                                        <div class="form-group">
                                            <label for="t_name">Teacher Name:</label>
                                            <input type="text" class="form-control" id="t_name" name="t_name" placeholder="Teacher Name">
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_noPhone">Phone Number:</label>
                                            <input type="tel" class="form-control" id="t_noPhone" name="t_noPhone" placeholder="Phone Number">
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_marritalStatus">Marital Status:</label>
                                            <input type="text" class="form-control" id="t_marritalStatus" name="t_marritalStatus" placeholder="Marital Status">
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_qualification">Qualification:</label>
                                            <input type="text" class="form-control" id="t_qualification" name="t_qualification" placeholder="Qualification">
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_program">Program:</label>
                                            <input type="text" class="form-control" id="t_program" name="t_program" placeholder="Program">
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_role">Role:</label>
                                            <input type="text" class="form-control" id="t_role" name="t_role" placeholder="Role">
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_age">Age:</label>
                                            <input type="text" class="form-control" id="t_age" name="t_age" placeholder="Age">
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_race">Race:</label>
                                            <input type="text" class="form-control" id="t_race" name="t_race" placeholder="Race">
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_address">Address:</label>
                                            <textarea class="form-control" id="t_address" name="t_address" placeholder="Address"></textarea>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_gender">Gender:</label>
                                            <select class="form-control" id="t_gender" name="t_gender">
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_profilePicture">Profile Picture:</label>
                                            <input type="file" class="form-control-file" id="t_profilePicture" name="t_profilePicture" placeholder="Upload Profile Picture" onchange="showImagePreview()">
                                        </div>
                                        <!-- Image preview container -->
                                        <div class="col-2 mt-3" id="imagePreviewContainer" style="display: none;">
                                            <img id="imagePreview" class="img-fluid" alt="Image Preview">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col d-flex justify-content-end">
                                <input type="submit" value="Register Teacher">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div style="padding-top: 5rem;"></div>
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
        document.addEventListener('DOMContentLoaded', function() {
            function loadData() {
                // Function to load data into the form fields
                // Ikhwan 04-Jan-2024

                var userData = {
                    t_name: '<?php echo $user_data['t_name']; ?>',
                    t_noPhone: '<?php echo $user_data['t_noPhone']; ?>',
                    t_marritalStatus: '<?php echo $user_data['t_marritalStatus']; ?>',
                    t_qualification: '<?php echo $user_data['t_qualification']; ?>',
                    t_program: '<?php echo $user_data['t_program']; ?>',
                    t_role: '<?php echo $user_data['t_role']; ?>',
                    t_age: '<?php echo $user_data['t_age']; ?>',
                    t_race: '<?php echo $user_data['t_race']; ?>',
                    t_address: '<?php echo $user_data['t_address']; ?>',
                    t_gender: '<?php echo $user_data['t_gender']; ?>'
                };

                // Set values in the form fields
                document.getElementById('t_name').value = userData.t_name;
                document.getElementById('t_noPhone').value = userData.t_noPhone;
                document.getElementById('t_marritalStatus').value = userData.t_marritalStatus;
                document.getElementById('t_qualification').value = userData.t_qualification;
                document.getElementById('t_program').value = userData.t_program;
                document.getElementById('t_role').value = userData.t_role;
                document.getElementById('t_age').value = userData.t_age;
                document.getElementById('t_race').value = userData.t_race;
                document.getElementById('t_address').value = userData.t_address;
                document.getElementById('t_gender').value = userData.t_gender;
            }

            loadData();
        });

        function showImagePreview() {
            // Get the file input element
            var input = document.getElementById('profilePicture');

            // Get the image preview container and image element
            var imagePreviewContainer = document.getElementById('imagePreviewContainer');
            var imagePreview = document.getElementById('imagePreview');

            // Display the image preview container
            imagePreviewContainer.style.display = 'block';

            // Check if a file is selected
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                // Set the image source when the file is loaded
                reader.onload = function (e) {
                    imagePreview.src = e.target.result;
                };

                // Read the file as a data URL
                reader.readAsDataURL(input.files[0]);
            }
        }

    </script>

</body>

</html>