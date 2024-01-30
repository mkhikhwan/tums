<?php    
session_start();    
include '../config.php';

//submit form to register new children
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //get data from form 
    $username = $_POST['t_username'];
    $password = $_POST['t_password'];
    $email = $_POST['t_email'];
    $name = $_POST['t_name'];
    $noPhone = $_POST['t_noPhone'];
    $marritalStatus = $_POST['t_marritalStatus'];
    $qualification = $_POST['t_qualification'];
    $program = $_POST['t_program'];
    $role = $_POST['t_role'];
    $age = $_POST['t_age'];
    $race = $_POST['t_race'];
    $address = $_POST['t_address'];
    $gender = $_POST['t_gender'];
    
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
        $imageError = "Error: " . $_FILES["t_profilePicture"]["error"];
    }

    //check if name(username) and name of that children utk elakkan duplicate existed
    // Check if username exists
    $query = "SELECT * FROM teacher WHERE t_username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Alert message = username already exists
        echo '<script>alert("Error: username is already existed!")</script>';
    } else {
        // Insert new teacher data into the database
        $query = "INSERT INTO teacher (t_username, t_password, t_name, t_noPhone, t_marritalStatus, t_qualification, t_program, t_role, t_age, t_race, t_address, t_gender, t_profilePicture, t_email)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ssssssssssssss', $username, $password, $name, $noPhone, $marritalStatus, $qualification, $program, $role, $age, $race, $address, $gender, $profilePicture, $email);
        
        // Register success
        if (mysqli_stmt_execute($stmt)) {
            // Send account details to the email
            require_once '../phpmailer_load.php';
            $sendEmail = emailAccountDetails($email,$name,$username,$password);

            $_SESSION['message'] = "Register Teacher Successful";
            $_SESSION['message_type'] = "success";
            header('Location: manageTeacher.php');
            exit();
        } else {
            $_SESSION['message'] = "Register Teacher Unsuccessful";
            $_SESSION['message_type'] = "warning";
            header('Location: manageChildren.php');
            exit();
        }
    }
    mysqli_stmt_close($stmt);
}

// Login admin
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
                echo "Error!";
            }
        } else {
            echo "Error!" . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error!" . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    header('Location: login.php');
    exit();
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
        <!-- Include using php -->
        <?php include('sidemenu.php'); ?>

        
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">

                <!-- HEADER -->
                <nav class="navbar navbar-expand bg-white shadow mb-4 topbar static-top navbar-light">
                    <div class="container-fluid header"><button class="btn btn-link d-md-none rounded-circle me-3" id="sidebarToggleTop" type="button">
                        <i class="fas fa-bars"></i></button>
                        <label class="form-label fs-3 text-nowrap" id="label_welcome">
                            <br><h4>Register New Teacher</h4></label>
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
                                            <label for="t_username">Teacher Username:</label>
                                            <input type="text" class="form-control" id="t_username" name="t_username" placeholder="Teacher Username" required>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_password">Teacher Password:</label>
                                            <input type="password" class="form-control" id="t_password" name="t_password" placeholder="Teacher Password" required>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_email">Email:</label>
                                            <input type="text" class="form-control" id="t_email" name="t_email" placeholder="Teacher Email" required>
                                        </div>
                                    </div>
                                </div>

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
                                            <label for="t_noPhone">Phone Number:</label>
                                            <input type="tel" class="form-control" id="t_noPhone" name="t_noPhone" placeholder="Phone Number" required>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_profilePicture">Profile Picture:</label>
                                            <input type="file" class="form-control-file" id="t_profilePicture" name="t_profilePicture" placeholder="Upload Profile Picture" onchange="showImagePreview()" required>
                                        </div>
                                        <!-- Image preview container -->
                                        <div class="col-2 mt-3" id="imagePreviewContainer" style="display: none;">
                                            <img id="imagePreview" class="img-fluid" alt="Image Preview">
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
                                <button type="submit" class="btn btn-success text-white">Register</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/js/theme.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            autofillForm();
        });

        function showImagePreview() {
            // Get the file input element
            var input = document.getElementById('t_profilePicture');

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
