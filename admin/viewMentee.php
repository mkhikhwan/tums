<?php
session_start();
include '../config.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $teacherID = $_GET['tid'];
    $teacherData = getTeacherData($conn, $teacherID);
    $teacherName = $teacherData['t_name'];

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Get mentor-mentee relationships
    $mentorMenteeList = getMentorMenteeList($conn, $teacherID);

    // If form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['deleteCheckbox'])) {
            $selectedRows = $_POST['deleteCheckbox'];

            // Loop through selected rows and delete
            foreach ($selectedRows as $mentorMenteeID) {
                deleteMentorMenteeRelationship($conn, $mentorMenteeID);
            }


            // Set success message
            $_SESSION['message'] = "Unassign Child to Teacher successful";
            $_SESSION['message_type'] = "success";

            // Refresh the page after deletion
            header('Location: mentorMentee.php?tid=' . $teacherID);
            exit();
        }
    }

    mysqli_close($conn);
} else {
    header('Location: login.php');
}

// Function to delete a mentor-mentee relationship
function deleteMentorMenteeRelationship($conn, $mentorMenteeID) {
    $query = "DELETE FROM mentormentee WHERE mentormentee_id = '$mentorMenteeID'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error: " . mysqli_error($conn));
    }
}

function getMentorMenteeList($conn, $mentorID) {
    $mentorMenteeList = array();

    // Use prepared statement to prevent SQL injection
    $query = "SELECT mentormentee.*, children.c_name FROM mentormentee JOIN children ON mentormentee.MenteeID = children.c_id WHERE mentormentee.MentorID = ?";
    $stmt = mysqli_prepare($conn, $query);

    // Bind the parameter
    mysqli_stmt_bind_param($stmt, "s", $mentorID);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Get the result
    $result = mysqli_stmt_get_result($stmt);

    if (!$result) {
        die("Error: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $mentorMenteeList[] = $row;
        }
    } else {
        // echo "No mentor-mentee relationships found.";
    }

    return $mentorMenteeList;
}

function getTeacherData($conn, $teacherID) {
    $query = "SELECT t_program, t_name FROM teacher WHERE t_id = '$teacherID'";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error: " . mysqli_error($conn));
    }

    $teacherData = mysqli_fetch_assoc($result);

    return $teacherData;
}

?>


<!-- start -->
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
                            <br><h4>Mentor : <?php echo $teacherName; ?></h4></label>
                    </div>
                </nav>

                <!-- MAIN CONTENT -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">

                            <form action="" method="post">
                                <div class="row">
                                    <div class="col-lg-12 col-xl-12 mb-2">
                                        <div class="card text-white bg-primary shadow">
                                            <div class="container py-2">

                                                <!-- HEADER -->
                                                <div class="row text-center justify-content-center my-2">
                                                    <!-- Number -->
                                                    <div class="col-sm-1 col-2">
                                                        <p>No.</p>
                                                    </div>

                                                    <!-- DATA -->
                                                    <div class="col-sm-11 col-10">
                                                        <p>Name of Child</p>                                         
                                                    </div>
                                                </div>

                                                <!-- Children list -->
                                                <?php
                                                // Check if there are mentor-mentee relationships
                                                if (count($mentorMenteeList) > 0) {
                                                    // Output data of each row
                                                    $index = 1; // Variable to track the number

                                                    foreach ($mentorMenteeList as $row) {
                                                        echo '<div class="row text-center my-2">';
                                                        
                                                        // Number
                                                        echo "<div class='col-sm-1 col-2 text-center align-items-center d-flex text-center justify-content-center'><p>{$index}</p></div>";
                                                        
                                                        // Data
                                                        echo "<div class='col-sm-11 col-10 rounded-pill bg-white text-black d-inline-flex justify-content-between px-3 py-1 align-items-center'>";
                                                        echo "<p>{$row['c_name']}</p>";
                                                        echo "<div class='col-1 align-items-center d-flex justify-content-center'>";
                                                        echo "<input type='checkbox' name='deleteCheckbox[]' value='{$row['mentormentee_id']}'>";
                                                        echo "</div>";
                                                        echo "</div>";
                                                        
                                                        echo '</div>';

                                                        $index++;
                                                    }
                                                } else {
                                                    echo "<p class='text-center'>No mentees are assigned to this mentor.</p>";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 d-flex justify-content-end">
                                        <input type="submit" value="Delete" class="btn btn-danger">
                                    </div>
                                </div>
                            </form>

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
    <script>
        function assignFunction(tid) {
            var dataTid = document.querySelector(`.teacher-row[data-tid="T-${tid}"]`).getAttribute('data-tid');
            
            // Redirect to "assignMentee.php" with data-tid as a GET variable
            window.location.href = `assignMentee.php?tid=${dataTid}`;
        }

        function editFunction(tid) {
            var dataTid = document.querySelector(`.teacher-row[data-tid="T-${tid}"]`).getAttribute('data-tid');
            
            // Redirect to "assignMentee.php" with data-tid as a GET variable
            window.location.href = `viewMentee.php?tid=${dataTid}`;
        }
    </script>
</body>

</html>
<!-- end -->