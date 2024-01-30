<?php    
session_start();    
include '../config.php';

//submit form to register new children
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //get data from form 
    $username = cleanInput($_POST['c_username']);
    $password = cleanInput($_POST['c_password']);
    $email = cleanInput($_POST['c_email']);
    $registerID = cleanInput($_POST['c_registerID']);
    $name = cleanInput($_POST['c_name']);
    $age = cleanInput($_POST['c_age']);
    $enrollmentDate = cleanInput($_POST['c_enrollmentDate']);
    $gender = cleanInput($_POST['c_gender']);
    $race = cleanInput($_POST['c_race']);
    $address = cleanInput($_POST['c_address']);
    $birthCertificate = cleanInput($_POST['c_birthCertificate']);
    $FatherName = cleanInput($_POST['c_FatherName']);
    $FatherPhoneNo = cleanInput($_POST['c_FatherPhoneNo']);
    $MotherName = cleanInput($_POST['c_MotherName']);
    $MotherPhoneNo = cleanInput($_POST['c_MotherPhoneNo']);
    $UNIMASstaff = cleanInput($_POST['c_UNIMASstaff']);
    $Disabilities = cleanInput($_POST['c_Disabilities']);
    $Allergies = cleanInput($_POST['c_Allergies']);
    $program = cleanInput($_POST['c_program']);
    
    //Upload image - ikhwan 03-01-2023
    $targetDir = "../data/img/children/";
    if (isset($_FILES["c_profilePicture"]) && $_FILES["c_profilePicture"]["error"] == 0) {
        // Get the original filename
        $original_filename = basename($_FILES["c_profilePicture"]["name"]);

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
            if (move_uploaded_file($_FILES["c_profilePicture"]["tmp_name"], $target_file)) {
                $imageError = "The file " . htmlspecialchars($new_filename) . " has been uploaded.";
            } else {
                $imageError = "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        $imageError = "Error: " . $_FILES["c_profilePicture"]["error"];
    }

    //check if name(username) and name of that children utk elakkan duplicate existed
    $query = "SELECT * FROM children where c_username = ? OR c_name = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ss',$username, $name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0){
        //alert message = name(username) existed
        echo '<script>alert("Error: username OR name is already existed!")</script>';
    }else{
        $query = "INSERT INTO children (c_username, c_password, c_registerID, c_name, c_age, c_enrollmentDate, c_gender, c_race, c_address, c_birthCertificate, c_FatherName, c_FatherPhoneNo, c_MotherName, c_MotherPhoneNo, c_UNIMASstaff, c_Disabilities, c_Allergies, c_profilePicture, c_program,c_email)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = mysqli_prepare($conn, $query);

        // Assuming $program is a string, adjust the 's' in mysqli_stmt_bind_param if it's a different data type
        mysqli_stmt_bind_param($stmt, 'ssssssssssssssssssss', $username, $password, $registerID, $name, $age, $enrollmentDate, $gender, $race, $address, $birthCertificate, $FatherName, $FatherPhoneNo, $MotherName, $MotherPhoneNo, $UNIMASstaff, $Disabilities, $Allergies, $profilePicture, $program, $email);

        //register success
        if(mysqli_stmt_execute($stmt)){
            // Send account details to the email
            require_once '../phpmailer_load.php';
            $sendEmail = emailAccountDetails($email,$name,$username,$password);

            // echo '<script>alert("New Children has succesfully registered!"); window.location = "manageChildren.php"</script>';
            $_SESSION['message'] = "Register Child Successful";
            $_SESSION['message_type'] = "success";
            header('Location: manageChildren.php');
            exit();
        }else{
            $_SESSION['message'] = "Register Child Unsuccessful";
            $_SESSION['message_type'] = "warning";
            header('Location: manageChildren.php');
            exit();
        }
    } mysqli_stmt_close($stmt);
}

//login admin
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
} else {
    header('Location: login.php');
    exit();
}

function cleanInput($data) {
    // Use mysqli_real_escape_string if not using prepared statements
    // $data = mysqli_real_escape_string($conn, $data);

    // Use parameterized queries instead of cleaning input if possible
    // Adjust the data type based on your database schema
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>


<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Register New Child</title>
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
                            <br><h4>Register New Children</h4></label>
                    </div>
                </nav>

                <!-- MAIN CONTENT -->
                <div class="container-fluid">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col">
                                <div class="row">
                                    <div class="col-lg-12 col-xl-12 mb-4">
                                        <div class="card text-white bg-primary shadow">
                                            <div class="container p-4">
                                                <!-- Username -->
                                                <div class="form-group">
                                                    <label for="username">Username:</label>
                                                    <input type="text" class="form-control" id="username" name="c_username" placeholder="Username" required pattern="[a-zA-Z0-9]+">
                                                </div>

                                                <!-- Password -->
                                                <div class="form-group mt-3">
                                                    <label for="password">Password:</label>
                                                    <input type="password" class="form-control" id="password" name="c_password" placeholder="Password" required>
                                                </div>

                                                <!-- Email -->
                                                <div class="form-group mt-3">
                                                    <label for="email">Email:</label>
                                                    <input type="email" class="form-control" id="email" name="c_email" placeholder="Email" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="row">
                                    <div class="col-lg-12 col-xl-12 mb-4">
                                        <div class="card text-white bg-primary shadow">
                                            <div class="container p-4">
                                                <!-- Child Information -->
                                                <div class="form-group">
                                                    <label for="childID">Child ID:</label>
                                                    <input type="text" class="form-control" id="childID" name="c_registerID" placeholder="Child ID" required>
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="age">Age:</label>
                                                    <input type="text" class="form-control" id="age" name="c_age" placeholder="Age" required pattern="[1-4]" title="Enter a valid age (1-4)">
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="name">Name:</label>
                                                    <input type="text" class="form-control" id="name" name="c_name" placeholder="Name" required pattern="[a-zA-Z\s]+" title="Only letters and spaces are allowed">
                                                </div>

                                                <div class="form-row">
                                                    <div class="form-group col-md-6 mt-3">
                                                        <label for="enrollmentDate">Enrollment Date:</label>
                                                        <input type="date" class="form-control" id="enrollmentDate" name="c_enrollmentDate" required>
                                                    </div>
                                                    <div class="form-group col-md-6 mt-3">
                                                        <label for="profilePicture">Profile Picture:</label>
                                                        <input type="file" class="form-control-file" id="profilePicture" name="c_profilePicture" placeholder="Upload Profile Picture" onchange="showImagePreview()" required>
                                                    </div>

                                                    <!-- Image preview container -->
                                                    <div class="col-2 mt-3" id="imagePreviewContainer" style="display: none;">
                                                        <img id="imagePreview" class="img-fluid" alt="Image Preview">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col">
                                <div class="row">
                                    <div class="col-lg-12 col-xl-12 mb-4">
                                        <div class="card text-white bg-primary shadow">
                                            <div class="container p-4">
                                                <!-- Additional fields -->
                                                <div class="form-group">
                                                    <label for="gender">Gender:</label>
                                                    <select class="form-control" id="gender" name="c_gender" required>
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                    </select>
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="race">Race:</label>
                                                    <input type="text" class="form-control" id="race" name="c_race" placeholder="Race" required>
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="address">Address:</label>
                                                    <textarea class="form-control" id="address" name="c_address" placeholder="Address" required></textarea>
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="birthCertificate">Birth Certificate:</label>
                                                    <input type="text" class="form-control" id="birthCertificate" name="c_birthCertificate" placeholder="Birth Certificate" required>
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="fathersName">Father's Name:</label>
                                                    <input type="text" class="form-control" id="fathersName" name="c_FatherName" placeholder="Father's Name" required>
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="fathersPhone">Father's Phone:</label>
                                                    <input type="tel" class="form-control" id="fathersPhone" name="c_FatherPhoneNo" placeholder="Father's Phone" required pattern="[0-9]+">
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="mothersName">Mother's Name:</label>
                                                    <input type="text" class="form-control" id="mothersName" name="c_MotherName" placeholder="Mother's Name" required>
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="mothersPhone">Mother's Phone:</label>
                                                    <input type="tel" class="form-control" id="mothersPhone" name="c_MotherPhoneNo" placeholder="Mother's Phone" required pattern="[0-9]+">
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="unimasStaff">UNIMAS Staff (Yes/No):</label>
                                                    <select class="form-control" id="unimasStaff" name="c_UNIMASstaff" required>
                                                        <option value="Yes">Yes</option>
                                                        <option value="No">No</option>
                                                    </select>
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="disabilities">Disabilities:</label>
                                                    <input type="text" class="form-control" id="disabilities" name="c_Disabilities" placeholder="Disabilities" required>
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="allergies">Allergies:</label>
                                                    <input type="text" class="form-control" id="allergies" name="c_Allergies" placeholder="Allergies" required>
                                                </div>

                                                <div class="form-group mt-3">
                                                    <label for="programSelect">Select Program:</label>
                                                    <select class="form-control" id="programSelect" name="c_program" required>
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
                            </div>
                        </div>

                        <div class="row">
                            <div class="col d-flex justify-content-end">
                               <button class="btn btn-success text-white" type="Register" value="Submit">Register</button>
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