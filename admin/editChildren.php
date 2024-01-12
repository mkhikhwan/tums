<?php
session_start();
include '../config.php';

// Verify if User is logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $selectChildID = $_GET['id'];

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $user_data = getChildData($conn, $selectChildID);
    $profilePicture = loadImage($conn, $selectChildID);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        modifyChildData($conn, $selectChildID, $user_data['c_username']);

        // Redirect to the same page using GET to avoid resubmission
        header("Location: manageChildren.php");
        exit();
    }

    mysqli_close($conn);
} else {
    header('Location: login.php');
    exit();
}

function getChildData($conn, $childID) {
    $query = "SELECT * FROM children WHERE c_id = ?";

    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $childID);
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

function modifyChildData($conn, $childID, $username) {
    // Modify Child data
    // By Aina
    // Modified by Ikhwan 04-Jan-2024

    $registerID = mysqli_real_escape_string($conn, $_POST['c_registerID']);
    $name = mysqli_real_escape_string($conn, $_POST['c_name']);
    $age = mysqli_real_escape_string($conn, $_POST['c_age']);
    $enrollmentDate = mysqli_real_escape_string($conn, $_POST['c_enrollmentDate']);
    $gender = mysqli_real_escape_string($conn, $_POST['c_gender']);
    $race = mysqli_real_escape_string($conn, $_POST['c_race']);
    $address = mysqli_real_escape_string($conn, $_POST['c_address']);
    $birthCertificate = mysqli_real_escape_string($conn, $_POST['c_birthCertificate']);
    $FatherName = mysqli_real_escape_string($conn, $_POST['c_FatherName']);
    $FatherPhoneNo = mysqli_real_escape_string($conn, $_POST['c_FatherPhoneNo']);
    $MotherName = mysqli_real_escape_string($conn, $_POST['c_MotherName']);
    $MotherPhoneNo = mysqli_real_escape_string($conn, $_POST['c_MotherPhoneNo']);
    $UNIMASstaff = mysqli_real_escape_string($conn, $_POST['c_UNIMASstaff']);
    $Disabilities = mysqli_real_escape_string($conn, $_POST['c_Disabilities']);
    $Allergies = mysqli_real_escape_string($conn, $_POST['c_Allergies']);
    
    //Upload image - ikhwan 03-01-2023
    // $targetDir = "../data/img/children/";
    $profilePicture = $username . ".png";
    // if (isset($_FILES["c_profilePicture"]) && $_FILES["c_profilePicture"]["error"] == 0) {
    //     // Get the original filename
    //     $original_filename = basename($_FILES["c_profilePicture"]["name"]);

    //     // Extract the file extension
    //     $file_extension = pathinfo($original_filename, PATHINFO_EXTENSION);

    //     // Use username and file extension as the filename
    //     $new_filename = $username . '.' . $file_extension;
    //     $profilePicture = $new_filename;

    //     $target_file = $targetDir . $new_filename;

    //     // Check if the file already exists
    //     if (file_exists($target_file)) {
    //         // Delete the existing file
    //         unlink($target_file);

    //         // Move the uploaded file to the specified directory
    //         if (move_uploaded_file($_FILES["c_profilePicture"]["tmp_name"], $target_file)) {
    //             $profilePicture = $new_filename;
    //             $imageError = "The file " . htmlspecialchars($new_filename) . " has been replaced and uploaded.";
    //         } else {
    //             $imageError = "Sorry, there was an error uploading your file.";
    //         }
    //     } else {
    //         // Move the uploaded file to the specified directory
    //         if (move_uploaded_file($_FILES["c_profilePicture"]["tmp_name"], $target_file)) {
    //             $profilePicture = $new_filename;
    //             $imageError = "The file " . htmlspecialchars($new_filename) . " has been uploaded.";
    //         } else {
    //             $imageError = "Sorry, there was an error uploading your file.";
    //         }
    //     }
    // } else {
    //     $imageError = "Error: " . $_FILES["c_profilePicture"]["error"];
    // }
    
    // Query to edit the table "children"
    $query = "UPDATE children SET
        c_registerID=?, c_name=?, c_age=?, c_enrollmentDate=?, c_gender=?, c_race=?,
        c_address=?, c_birthCertificate=?, c_FatherName=?, c_FatherPhoneNo=?,
        c_MotherName=?, c_MotherPhoneNo=?, c_UNIMASstaff=?, c_Disabilities=?, c_Allergies=?,
        c_profilePicture=?  -- Add the column for profile picture here
        WHERE c_id=?";
        
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ssissssssssssssss', 
            $registerID, $name, $age, $enrollmentDate, $gender, $race,
            $address, $birthCertificate, $FatherName, $FatherPhoneNo,
            $MotherName, $MotherPhoneNo, $UNIMASstaff, $Disabilities, $Allergies, $profilePicture, $childID);

        $success = mysqli_stmt_execute($stmt);

        // Add custom message
        // Ikhwan 04-01-2024
        if ($success) {
            $_SESSION['message'] = "Modify successful";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Error modifying child data: " . mysqli_error($conn);
            $_SESSION['message_type'] = 'warning';
        }

        mysqli_stmt_close($stmt);
    } else {
        echo '<script>alert("Error! ' . mysqli_error($conn) . '");</script>';
    }
}

function loadImage($conn, $id) {
    // Simple function to get image filename from database
    $profilePicture = "";

    $sql = "SELECT c_profilePicture FROM children WHERE c_id = ?";

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
                            <br><h4>Edit Child</h4></label>
                    </div>
                </nav>

                <!-- MAIN CONTENT -->
                <div class="container-fluid">
                    <?php
                        // Display modify_message if set
                        if (isset($_SESSION['modify_message'])) {
                            echo '
                            <div class="alert alert-' . ($_SESSION['modify_message_type'] ?? 'info') . ' alert-dismissible fade show" role="alert">
                                ' . $_SESSION['modify_message'] . '
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>';
                            unset($_SESSION['modify_message']); // Clear the session variable
                            unset($_SESSION['modify_message_type']); // Clear the session variable
                        }
                    ?>

                    <form action="" method="post">
                        <div class="row">
                            <div class="col">
                                <div class="row">
                                    <div class="col-lg-12 col-xl-12 mb-4">
                                        <div class="card text-white bg-primary shadow">
                                            <div class="container p-4">
                                                <!-- Child Information -->
                                                <div class="form-group">
                                                    <label for="childID">Child ID:</label>
                                                    <input type="text" class="form-control" id="c_registerID" name="c_registerID" placeholder="Child ID">
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="age">Age:</label>
                                                    <input type="text" class="form-control" id="c_age" name="c_age" placeholder="Age">
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="name">Name:</label>
                                                    <input type="text" class="form-control" id="c_name" name="c_name" placeholder="Name">
                                                </div>

                                                <div class="form-row">
                                                    <div class="form-group col-md-6 mt-3">
                                                        <label for="enrollmentDate">Enrollment Date:</label>
                                                        <input type="date" class="form-control" id="c_enrollmentDate" name="c_enrollmentDate">
                                                    </div>
                                                    <div class="form-group col-md-6 mt-3">
                                                        <label for="profilePicture">Profile Picture:</label>
                                                        <input type="file" class="form-control-file" id="c_profilePicture" name="c_profilePicture" placeholder="Upload Profile Picture" onchange="showImagePreview()">
                                                    </div>

                                                    <!-- Display assigned profile picture -->
                                                    <div class="col-2 mt-3" id="imageDisplayContainer">
                                                        <img src="../data/img/children/<?php echo $profilePicture ?>" id="imageDisplay" class="img-fluid" alt="Image Display">
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
                                                    <select class="form-control" id="c_gender" name="c_gender">
                                                        <option value="Male">Male</option>
                                                        <option value="Female">Female</option>
                                                    </select>
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="race">Race:</label>
                                                    <input type="text" class="form-control" id="c_race" name="c_race" placeholder="Race">
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="address">Address:</label>
                                                    <textarea class="form-control" id="c_address" name="c_address" placeholder="Address"></textarea>
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="birthCertificate">Birth Certificate:</label>
                                                    <input type="text" class="form-control" id="c_birthCertificate" name="c_birthCertificate" placeholder="Birth Certificate">
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="fathersName">Father's Name:</label>
                                                    <input type="text" class="form-control" id="c_FatherName" name="c_FatherName" placeholder="Father's Name">
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="fathersPhone">Father's Phone:</label>
                                                    <input type="tel" class="form-control" id="c_FatherPhoneNo" name="c_FatherPhoneNo" placeholder="Father's Phone">
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="mothersName">Mother's Name:</label>
                                                    <input type="text" class="form-control" id="c_MotherName" name="c_MotherName" placeholder="Mother's Name">
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="mothersPhone">Mother's Phone:</label>
                                                    <input type="tel" class="form-control" id="c_MotherPhoneNo" name="c_MotherPhoneNo" placeholder="Mother's Phone">
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="unimasStaff">UNIMAS Staff (Yes/No):</label>
                                                    <select class="form-control" id="c_UNIMASstaff" name="c_UNIMASstaff">
                                                        <option value="Yes">Yes</option>
                                                        <option value="No">No</option>
                                                    </select>
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="disabilities">Disabilities:</label>
                                                    <input type="text" class="form-control" id="c_Disabilities" name="c_Disabilities" placeholder="Disabilities">
                                                </div>
                                                <div class="form-group mt-3">
                                                    <label for="allergies">Allergies:</label>
                                                    <input type="text" class="form-control" id="c_Allergies" name="c_Allergies" placeholder="Allergies">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col d-flex justify-content-end">
                               <input type="submit" value="Submit" class="btn btn-success">
                            </div>
                        </div>
                    </form>


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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function loadData() {
                // Function to load data into the form fields
                // Ikhwan 04-Jan-2024

                var userData = {
                c_registerID: '<?php echo $user_data['c_registerID']; ?>',
                c_name: '<?php echo $user_data['c_name']; ?>',
                c_age: '<?php echo $user_data['c_age']; ?>',
                c_enrollmentDate: '<?php echo $user_data['c_enrollmentDate']; ?>',
                c_gender: '<?php echo $user_data['c_gender']; ?>',
                c_race: '<?php echo $user_data['c_race']; ?>',
                c_address: '<?php echo $user_data['c_address']; ?>',
                c_birthCertificate: '<?php echo $user_data['c_birthCertificate']; ?>',
                c_FatherName: '<?php echo $user_data['c_FatherName']; ?>',
                c_FatherPhoneNo: '<?php echo '0' . $user_data['c_FatherPhoneNo']; ?>',
                c_MotherName: '<?php echo $user_data['c_MotherName']; ?>',
                c_MotherPhoneNo: '<?php echo '0' . $user_data['c_MotherPhoneNo']; ?>',
                c_UNIMASstaff: '<?php echo $user_data['c_UNIMASstaff']; ?>',
                c_Disabilities: '<?php echo $user_data['c_Disabilities']; ?>',
                c_Allergies: '<?php echo $user_data['c_Allergies']; ?>',
                };

                // Set values in the form fields
                document.getElementById('c_registerID').value = userData.c_registerID;
                document.getElementById('c_name').value = userData.c_name;
                document.getElementById('c_age').value = userData.c_age;
                document.getElementById('c_enrollmentDate').value = userData.c_enrollmentDate;
                document.getElementById('c_gender').value = userData.c_gender;
                document.getElementById('c_race').value = userData.c_race;
                document.getElementById('c_address').value = userData.c_address;
                document.getElementById('c_birthCertificate').value = userData.c_birthCertificate;
                document.getElementById('c_FatherName').value = userData.c_FatherName;
                document.getElementById('c_FatherPhoneNo').value = userData.c_FatherPhoneNo;
                document.getElementById('c_MotherName').value = userData.c_MotherName;
                document.getElementById('c_MotherPhoneNo').value = userData.c_MotherPhoneNo;
                document.getElementById('c_UNIMASstaff').value = userData.c_UNIMASstaff;
                document.getElementById('c_Disabilities').value = userData.c_Disabilities;
                document.getElementById('c_Allergies').value = userData.c_Allergies;
            }

            loadData();
        });

        function showImagePreview() {
            var input = document.getElementById('c_profilePicture');
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