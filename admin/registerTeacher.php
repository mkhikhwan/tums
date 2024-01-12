<?php    
session_start();    
include '../config.php';

//submit form to register new children
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //get data from form 
    $username = $_POST['t_username'];
    $password = $_POST['t_password'];
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
        $query = "INSERT INTO teacher (t_username, t_password, t_name, t_noPhone, t_marritalStatus, t_qualification, t_program, t_role, t_age, t_race, t_address, t_gender, t_profilePicture)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'sssssssssssss', $username, $password, $name, $noPhone, $marritalStatus, $qualification, $program, $role, $age, $race, $address, $gender, $profilePicture);

        // Register success
        if (mysqli_stmt_execute($stmt)) {
            echo '<script>alert("New Teacher has successfully registered!"); window.location = "manageTeacher.php"</script>';
        } else {
            echo '<script>alert("Register Unsuccessful!")</script>';
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
                                            <input type="text" class="form-control" id="t_username" name="t_username" placeholder="Teacher Username">
                                        </div>
                                        <div class="form-group mt-3">
                                            <label for="t_password">Teacher Password:</label>
                                            <input type="password" class="form-control" id="t_password" name="t_password" placeholder="Teacher Password">
                                        </div>
                                        <div class="form-group mt-3">
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
        document.addEventListener('DOMContentLoaded', function () {
            autofillForm();
        });

        // DELETE THIS FUNCTION
        // THIS FUNCTION IS FOR TESTING PURPOSES
        function autofillForm() {
            // Replace this object with your data retrieval logic
            var teacherData = {
                t_username: 'TeacherDoe',
                t_password: 'teacher123',
                t_name: 'Teacher Doe',
                t_noPhone: '123-456-7890',
                t_marritalStatus: 'Married',
                t_qualification: 'Master in Education',
                t_program: 'Science',
                t_role: 'Teaching',
                t_age: '30',
                t_race: 'Caucasian',
                t_address: '456 Oak St',
                t_gender: 'Male'
            };

            // Set values in the form fields
            document.getElementById('t_username').value = teacherData.t_username;
            document.getElementById('t_password').value = teacherData.t_password;
            document.getElementById('t_name').value = teacherData.t_name;
            document.getElementById('t_noPhone').value = teacherData.t_noPhone;
            document.getElementById('t_marritalStatus').value = teacherData.t_marritalStatus;
            document.getElementById('t_qualification').value = teacherData.t_qualification;
            document.getElementById('t_program').value = teacherData.t_program;
            document.getElementById('t_role').value = teacherData.t_role;
            document.getElementById('t_age').value = teacherData.t_age;
            document.getElementById('t_race').value = teacherData.t_race;
            document.getElementById('t_address').value = teacherData.t_address;

            // Optional: Trigger the change event for elements like file input to show the image preview
            showImagePreview();
        }

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
