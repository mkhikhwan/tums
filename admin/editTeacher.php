<?php
session_start();
include '../config.php';

// Verify if User is logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $selectTeacherID = $_GET['id'];
    $profilePicture = loadImage($conn,$selectTeacherID);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $user_data = getTeacherData($conn, $selectTeacherID);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        modifyTeacherData($conn, $selectTeacherID, $user_data['t_username']);

        // Redirect to the same page using GET to avoid resubmission
        header("Location: manageTeacher.php");
        exit();
    }

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

function modifyTeacherData($conn, $teacherID, $username) {
    // Modify Teacher data
    // By Aina
    // Modified by Ikhwan 04-Jan-2024

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

    //Upload image - ikhwan 03-01-2023
    $targetDir = "../data/img/teacher/";
    if (isset($_FILES["t_profilePicture"]) && $_FILES["t_profilePicture"]["error"] == 0) {
        // Get the original filename
        $original_filename = basename($_FILES["t_profilePicture"]["name"]);

        // Extract the file extension
        $file_extension = pathinfo($original_filename, PATHINFO_EXTENSION);

        // Use username and file extension as the filename
        $new_filename = $username . '.' . $file_extension;
        $profilePicture = $new_filename;

        $target_file = $targetDir . $new_filename;

        // Check if the file already exists
        if (file_exists($target_file)) {
            $imageError = "Sorry, file already exists.";
        } else {
            // Move the uploaded file to the specified directory
            if (move_uploaded_file($_FILES["t_profilePicture"]["tmp_name"], $target_file)) {
                $imageError = "The file " . htmlspecialchars($new_filename) . " has been uploaded.";
            } else {
                $imageError = "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        $profilePicture = loadImage($conn, $teacherID);
    }

    // Query to edit the table "teachers"
    $query = "UPDATE teacher SET
        t_name=?, t_noPhone=?, t_marritalStatus=?, t_qualification=?,
        t_program=?, t_role=?, t_age=?, t_race=?, t_address=?, t_gender=?, t_profilePicture=?
        WHERE t_id=?";

    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ssssssisssss', 
            $name, $noPhone, $marritalStatus, $qualification,
            $program, $role, $age, $race, $address, $gender, $profilePicture, $teacherID);

        $success = mysqli_stmt_execute($stmt);

        // Add custom message
        // Ikhwan 04-01-2024
        if ($success) {
            $_SESSION['message'] = "Modify successful";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error modifying teacher data: " . mysqli_error($conn);
            $_SESSION['message_type'] = "warning";
        }

        mysqli_stmt_close($stmt);
    } else {
        echo '<script>alert("Error! ' . mysqli_error($conn) . '");</script>';
    }
}

function loadImage($conn, $id) {
    // Simple function to get image filename from database
    $profilePicture = "";

    $sql = "SELECT t_profilePicture FROM teacher WHERE t_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);  // Change "i" to "s" for a string
    $stmt->execute();
    $stmt->bind_result($profilePicture);

    $stmt->fetch();

    $stmt->close();

    // If $profilePicture is null, return an empty string
    return ($profilePicture !== null) ? $profilePicture : "";
}
?>

<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Edit Teacher</title>
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
                            <br><h4>Edit Profile : <span><?= $user_data['t_name']?></span></h4></label>
                    </div>
                </nav>

                <!-- MAIN CONTENT -->
                <div class="container-fluid">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-lg-12 col-xl-12 mb-4">
                                <div class="card text-white bg-primary shadow mt-4">
                                    <div class="container p-4">
                                        <div class="form-group">
                                            <label for="t_role">Role:</label>
                                            <input type="text" class="form-control" id="t_role" name="t_role" placeholder="Role" required>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_age">Age:</label>
                                            <input type="text" class="form-control" id="t_age" name="t_age" placeholder="Age" required>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_name">Teacher Name:</label>
                                            <input type="text" class="form-control" id="t_name" name="t_name" placeholder="Teacher Name" required>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_noPhone">No Phone:</label>
                                            <input type="tel" class="form-control" id="t_noPhone" name="t_noPhone" placeholder="Phone Number" required>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6 mt-3">
                                                <label for="profilePicture">Profile Picture:</label>
                                                <input type="file" class="form-control-file" id="t_profilePicture" name="t_profilePicture" placeholder="Upload Profile Picture" onchange="showImagePreview()">
                                            </div>

                                            <!-- Display assigned profile picture -->
                                            <div class="col-2 mt-3" id="imageDisplayContainer">
                                                <img src="../data/img/teacher/<?php echo $profilePicture ?>" id="imageDisplay" class="img-fluid" alt="Image Display">
                                            </div>

                                            <!-- Image preview container -->
                                            <div class="col-2 mt-3" id="imagePreviewContainer" style="display: none;">
                                                <img id="imagePreview" class="img-fluid" alt="Image Preview">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card text-white bg-primary shadow mt-4">
                                    <div class="container p-4">
                                        <div class="form-group">
                                            <label for="t_gender">Gender:</label>
                                            <select class="form-control" id="t_gender" name="t_gender" required>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_race">Race:</label>
                                            <input type="text" class="form-control" id="t_race" name="t_race" placeholder="Race" required>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_address">Address:</label>
                                            <textarea class="form-control" id="t_address" name="t_address" placeholder="Address" required></textarea>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_marritalStatus">Marital Status:</label>
                                            <input type="text" class="form-control" id="t_marritalStatus" name="t_marritalStatus" placeholder="Marital Status" required>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_qualification">Qualification:</label>
                                            <input type="text" class="form-control" id="t_qualification" name="t_qualification" placeholder="Qualification" required>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_program">Program:</label>
                                            <select class="form-control" id="t_program" name="t_program" required>
                                                <option value="Age 1">Age 1</option>
                                                <option value="Age 2">Age 2</option>
                                                <option value="Age 3">Age 3</option>
                                                <option value="Age 4">Age 4</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col d-flex justify-content-end">
                                <button type="submit" class="btn btn-success">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
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
            var input = document.getElementById('t_profilePicture');
            var imageDisplayContainer = document.getElementById('imageDisplayContainer');
            var imageDisplay = document.getElementById('imageDisplay');
            var imagePreviewContainer = document.getElementById('imagePreviewContainer');
            var imagePreview = document.getElementById('imagePreview');

            // Check if a file is selected
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                // Set up a callback for when the image is loaded
                reader.onload = function (e) {
                    // Display the selected image in the image display container
                    imageDisplay.src = e.target.result;
                    
                    // Show the image display container and hide the image preview container
                    imageDisplayContainer.style.display = 'block';
                    imagePreviewContainer.style.display = 'none';
                };

                // Read the selected file as a data URL
                reader.readAsDataURL(input.files[0]);
            } else {
                // If no file is selected, hide both containers
                imageDisplayContainer.style.display = 'none';
                imagePreviewContainer.style.display = 'none';
            }
        }

    </script>

</body>

</html>