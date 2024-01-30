<?php
session_start();
include '../config.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $mentorID = $_GET['tid']; //Get from GET tid
    $teacherData = getTeacherData($conn, $mentorID);
    $teacherName = $teacherData['t_name'];
    $teacherProgram = $teacherData['t_program'];

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Call the function to get all children
    // $childList = getAllChild($conn);
    $childList = getChildListByProgram($conn, $teacherProgram);

    // If form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['assignCheckbox'])) {
            $selectedChildren = $_POST['assignCheckbox'];

            // Begin assign mentee list to mentor
            assignMentorMentee($conn, $mentorID, $selectedChildren, $teacherProgram);

            // Redirect
            header('Location: mentorMentee.php');
            exit();
        }else{
            // Set error message
            $_SESSION['message_mm'] = "Assign unsuccessful.";
            $_SESSION['message_mm_type'] = "warning";

            // Redirect
            header('Location: mentorMentee.php');
            exit();
        }
    }

    mysqli_close($conn);
} else {
    header('Location: login.php');
}

function assignMentorToChild($conn, $mentorID, $menteeID) {
    $query = "INSERT INTO mentormentee (MentorID, MenteeID) VALUES ('$mentorID', '$menteeID')";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error: " . mysqli_error($conn));
    }
}

function getChildListByProgram($conn, $teacherProgram) {
    // Filter by Child who is not assigned to the mentormentee table and by program
    $query = "SELECT c_id, c_name
              FROM children
              WHERE c_program = '$teacherProgram'
              AND NOT EXISTS (
                  SELECT 1
                  FROM mentormentee
                  WHERE mentormentee.MenteeID = children.c_id
              )";
    
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error: " . mysqli_error($conn));
    }

    $childList = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $childList[] = $row;
    }

    return $childList;
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

function assignMentorMentee($conn, $mentorID, $selectedChildren, $teacherProgram) {
    // Check the constraint based on the teacher program

    $maxChildrenAllowed = 0;
    switch ($teacherProgram) {
        case 'Age 1':
            $maxChildrenAllowed = 3;
            break;
        case 'Age 2':
            $maxChildrenAllowed = 5;
            break;
        case 'Age 3':
        case 'Age 4':
            $maxChildrenAllowed = 8;
            break;

        default:
            die("Error: Invalid teacher program '$teacherProgram'");
    }

    // Count the current number of assigned children
    $assignedChildrenQuery = "SELECT COUNT(*) as count FROM mentormentee WHERE MentorID = '$mentorID'";
    $assignedChildrenResult = mysqli_query($conn, $assignedChildrenQuery);

    if (!$assignedChildrenResult) {
        die("Error: " . mysqli_error($conn));
    }

    $currentAssignedChildren = mysqli_fetch_assoc($assignedChildrenResult)['count'];

    // Check if the constraint is met
    if ($currentAssignedChildren + count($selectedChildren) > $maxChildrenAllowed) {
        $_SESSION['message_mm'] = "Assign unsuccessful. Maximum limit reached for teacher program '$teacherProgram'.";
        $_SESSION['message_mm_type'] = "warning";
        return;
    }

    // Loop through selected children and assign the mentor
    foreach ($selectedChildren as $menteeID) {
        assignMentorToChild($conn, $mentorID, $menteeID);
    }

    // Set success message
    $_SESSION['message_mm'] = "Mentee(s) Assigned to Mentor Successfully";
    $_SESSION['message_mm_type'] = "success";
}
?>

<!-- start -->
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Assign Mentee</title>
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
                            <br><h4>Mentor : <?php echo $teacherName?></h4></label>
                    </div>
                </nav>

                <!-- MAIN CONTENT -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">

                            <form action="" method="post">
                                <div class="row">
                                    <div class="col-lg-12 col-xl-12 mb-2">
                                        <div class="mb-3">
                                            <p class="fs-5 fw-bold">Program : <?= $teacherData['t_program'] ?>
                                            <?php
                                            if ($teacherData['t_program'] == "Age 1") {
                                                echo " (Limit : 3)";
                                            } elseif ($teacherData['t_program'] == "Age 2") {
                                                echo " (Limit : 5)";
                                            } elseif ($teacherData['t_program'] == "Age 3") {
                                                echo " (Limit : 8)";
                                            } elseif ($teacherData['t_program'] == "Age 4") {
                                                echo " (Limit : 8)";
                                            }
                                            ?>
                                            </p>
                                        </div>
                                        
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
                                                        <p>Name of Children</p>                                         
                                                    </div>
                                                </div>

                                                <!-- Children list -->
                                                <!-- View All Children to Assign to Teacher -->
                                                <!-- View All Children to Assign to Teacher -->
                                                <?php
                                                // Check if there are results
                                                if (count($childList) > 0) {
                                                    // Output data of each row
                                                    $rowNumber = 1; // Variable to track row number

                                                    foreach ($childList as $row) {
                                                        echo '<div class="row child-row my-2" data-cid="' . $row['c_id'] . '">';
                                                        
                                                        // Number
                                                        echo '<div class="col-md-1 text-center p-1">';
                                                        echo "<p>{$rowNumber}</p>";
                                                        echo '</div>';
                                                        
                                                        // Data
                                                        echo '<div class="col-md-11 p-1 bg-white rounded-pill text-black d-inline-flex justify-content-between">';
                                                        
                                                        // Child Name
                                                        echo '<div class="col d-flex align-items-center">';
                                                        echo "<p class='px-3'>{$row['c_name']}</p>";
                                                        echo '</div>';
                                                        
                                                        // Checkbox
                                                        echo '<div class="col-1 text-right align-items-center d-flex">';
                                                        echo "<input type='checkbox' name='assignCheckbox[]' value='{$row['c_id']}'>";
                                                        echo '</div>';
                                                        
                                                        echo '</div>';
                                                        echo '</div>';

                                                        $rowNumber++; // Increment row number for the next iteration
                                                    }
                                                } else {
                                                    echo "<p>No children found.</p>";
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12 d-flex justify-content-end">
                                        <input type="submit" value="Assign Mentee(s)" class="btn btn-success text-white">
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
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