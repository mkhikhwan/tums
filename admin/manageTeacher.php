<?php
session_start();
include '../config.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // display all teachers names
    $query = "SELECT t_name, t_id
    FROM teacher
    ORDER BY CAST(SUBSTRING(t_id, 3) AS UNSIGNED)";

     $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Error: " . mysqli_error($conn));
    }

    $user_data = array();


    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $item_data[] = $row;
        }
    } else {
        echo "Error! No data found.";
    }

    mysqli_close($conn);
} else {
    header('Location: login.php');
}
?>


<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Manage Teacher</title>
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
                            <br><h4>Manage Teachers</h4></label>
                    </div>
                </nav>

                <!-- MAIN CONTENT -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <a href='registerTeacher.php' class="btn btn-primary">Register New Teacher</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-2">
                            <input type="text" id='searchInput' class="form-control" placeholder="Search Teacher Name">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <div class="row">
                                <div class="col-lg-12 col-xl-12 mb-2">
                                    <div class="card text-white bg-primary shadow">
                                        <div class="container p-2">
                                        <div class="row m-0">
                                        <div class="col-2">No.</div>
                                        <div class="col-6">Name of Teachers</div>
                                        <div class="col-2"></div>
                                        <div class="col-2 justify-content-end align-items-center d-flex"><button id="select-all" class="btn btn-primary p-1 m-0">Select All</button></div>
                                    </div>
                                            
                                            <?php foreach ($item_data as $index => $teacher): ?>
                                                <div class="row m-0 mt-1 white_box" data-teachername="<?= $teacher['t_name'] ?>" data-id="<?= $teacher['t_id'] ?>">
                                                <div class="col-2"><?= $index + 1 ?></div>
                                                <div class="col-7"><?= $teacher['t_name'] ?></div>
                                                <div class="col-3 d-inline-flex justify-content-end align-items-center">
                                                    <button class="btn btn-primary view m-0 p-0 px-3 me-2">View</button>
                                                    <button class="btn btn-warning edit m-0 p-0 px-3 me-3">Edit</button>
                                                    <input type="checkbox">
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 d-flex justify-content-end">
                            <button class="btn btn-danger" id="deleteSelected">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="../assets/js/theme.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {
            $('#searchInput').on('input', function() {
                var searchValue = String($(this).val()).toLowerCase();
        
                $('.white_box').each(function() {
                var teacherName = String($(this).data('teachername')).toLowerCase();
                
                //Hide div that doesnt have similar data-tag   
                if (teacherName.includes(searchValue)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
                });
            });

            $('#deleteSelected').on('click', function() {
                // Function that deletes selected child

                var selectedItems = [];

                // Iterate over each checked checkbox
                $('.white_box input[type="checkbox"]:checked').each(function() {
                    // Get the data-teachername attribute value
                    var teacherid = $(this).closest('.white_box').data('id');
                    selectedItems.push(teacherid);
                });

                // Show a confirmation box
                var confirmation = confirm('Are you sure you want to delete the selected items?');

                // If the user confirms
                if (confirmation) {
                    // Make an AJAX request to deleteTeacher.php
                    $.ajax({
                        type: 'POST',
                        url: 'deleteTeacher.php',
                        data: { selectedItems: selectedItems },
                        success: function(response) {
                            // console.log(response);
                            alert("Selected items deleted successfully.");
                            location.reload();
                        },
                        error: function(error) {
                            // console.error('Error:', error);
                            alert("Error deleting selected items: " + error.responseText);
                        }
                    });
                }
            });

            // Attach click event to the Edit button
            $('.white_box .btn-warning').on('click', function() {
                // Find the closest white_box element
                var row = $(this).closest('.white_box');

                // Get the data-id attribute value
                var t_id = row.data('id');

                // Redirect to editChildren.php with the c_id parameter
                window.location.href = 'editTeacher.php?id=' + t_id;
            });

            // Attach click event to the View button
            $('.white_box .btn-primary').on('click', function() {
                // Find the closest white_box element
                var row = $(this).closest('.white_box');

                // Get the data-id attribute value
                var t_id = row.data('id');

                // Redirect to editChildren.php with the c_id parameter
                window.location.href = 'viewTeacher.php?id=' + t_id;
            });

            $("#select-all").on("click", function(){
                // Check if any checkbox is already checked
                var anyChecked = $(".white_box input[type='checkbox']:checked").length > 0;

                // Toggle checkbox states
                $(".white_box input[type='checkbox']").prop("checked", !anyChecked);
            });
        });
    </script>
</body>

</html>