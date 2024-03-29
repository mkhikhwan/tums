<?php
session_start();
include '../config.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // display all children names
    $query = "SELECT * FROM teacher";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error: " . mysqli_error($conn));
    }

    $teacherList = array(); // Corrected variable name

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $teacherList[] = $row;
        }
    } else {
        echo "Error! No data found.";
    }

    mysqli_close($conn);
} else {
    header('Location: login.php');
}
?>


<!-- start -->
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Mentor Mentee</title>
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
                            <br><h4>Assign Mentor Mentee</h4></label>
                    </div>
                </nav>

                <!-- MAIN CONTENT -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <div class="row px-2">
                                <?php
                                    // Display modify_message if set
                                    if (isset($_SESSION['message_mm'])) {
                                        echo '
                                        <div class="alert alert-' . ($_SESSION['message_mm_type'] ?? 'info') . ' alert-dismissible fade show" role="alert">
                                            ' . $_SESSION['message_mm'] . '
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>';
                                        unset($_SESSION['message_mm']); // Clear the session variable
                                        unset($_SESSION['message_mm_type']); // Clear the session variable
                                    }
                                ?>
                            </div>


                            <div class="row">
                                <div class="col-lg-12 col-xl-12 mb-4">
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
                                                    <p>Name of Teachers</p>                                         
                                                </div>
                                            </div>

                                            <!-- TEACHER LIST -->
                                            <?php
                                                // Check if there are results
                                                if (count($teacherList) > 0) {
                                                    // $rowNumber = 0;

                                                    // Have to set to 2 for some unknown reason
                                                    $rowNumber = 1; 
                                                    
                                                    foreach ($teacherList as $row) {
                                                        echo '<div class="row my-2 teacher-row" data-tid="' . $row['t_id'] . '">';
                                                        // Numberd
                                                        echo '<div class="col-sm-1 col-2 text-center p-1">';
                                                        echo "<p>{$rowNumber}</p>";
                                                        echo '</div>';

                                                        // Data
                                                        echo '<div class="col-sm-11 col-10 p-1 bg-white rounded-pill text-black d-inline-flex justify-content-between">';
                                                        // Teacher Name
                                                        echo '<div class="col d-flex align-items-center">';
                                                        echo "<p class='px-3'>{$row['t_name']}</p>";
                                                        echo '</div>';

                                                        // Assign and Edit Buttons
                                                        echo '<div class="col-2">';
                                                        echo "<input class='btn btn-primary p-0 px-2' type='button' value='View' onclick='editFunction(\"{$row['t_id']}\")'>";
                                                        echo "<input class='btn btn-warning mx-2 p-0 px-2' type='button' value='Assign' onclick='assignFunction(\"{$row['t_id']}\")'>";
                                                        echo '</div>';

                                                        echo '</div>';
                                                        echo '</div>';
                                                        echo "\n";

                                                        $rowNumber++; // Increment row number for the next iteration
                                                    }
                                                } else {
                                                    echo "<p>0 results</p>";
                                                }
                                            ?>

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
    <script>
        function assignFunction(tid) {
            // Construct the URL with the GET variable
            var redirectURL = "assignMentee.php?tid=" + tid;

            // Redirect to the constructed URL
            window.location.href = redirectURL;
        }

        function editFunction(tid) {
            // Construct the URL with the GET variable
            var redirectURL = "viewMentee.php?tid=" + tid;

            // Redirect to the constructed URL
            window.location.href = redirectURL;
        }
    </script>
</body>

</html>
<!-- end -->