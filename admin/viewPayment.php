<?php
session_start();
include '../config.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Get unique p_name values for the dropdown menu
    $distinctPNamesQuery = "SELECT DISTINCT p_name FROM parentpayment";
    $distinctPNamesResult = mysqli_query($conn, $distinctPNamesQuery);

    if (!$distinctPNamesResult) {
        die("Query failed: " . mysqli_error($conn));
    }

    // Check if a specific p_name is selected
    $selectedPName = isset($_GET['p_name']) ? $_GET['p_name'] : '';

    // Check if a specific year is selected
    $selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y'); // Default to the current year if not specified

    // Get table children connected with parentpayment based on the selected p_name and year
    $query = "SELECT c.c_registerID, c.c_id, c.c_name, p.p_id, p.p_name, p.p_date, p.p_status
              FROM children c
              JOIN parentpayment p ON c.c_id = p.c_id
              WHERE p.p_name LIKE '%$selectedYear%'";

    // If a specific p_name is selected, add it to the query
    if ($selectedPName != '') {
        $query .= " AND p.p_name = '$selectedPName'";
    }

    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    mysqli_close($conn);



    // Reset the data pointer to the beginning
    mysqli_data_seek($result, 0);

    // Associative array to store child information
    $childInfo = [];

    // Loop through the result set
    while ($row = mysqli_fetch_assoc($result)) {
        $childID = $row['c_registerID'];
        $childName = $row['c_name'];

        // If child information is not already stored, add it to the array
        if (!isset($childInfo[$childID])) {
            $childInfo[$childID] = [
                'c_id' => $childID,
                'c_name' => $childName,
                'p_status' => [], // Array to store payment status for each month
            ];
        }

        // Add payment status for the current month
        $childInfo[$childID]['p_status'][] = $row['p_status'];
    }


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
                            <br><h4>Payment</h4></label>
                    </div>
                </nav>

                <!-- MAIN CONTENT -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <form action="" method="get">
                                <div class="row">
                                    <!-- COPY THIS ROW DIV TO CREATE NEW BOX -->
                                    <div class="col-lg-12 col-xl-12 mb-4">
                                        <div class="card text-white bg-primary shadow mb-2">
                                            <div class="container p-1 d-inline-flex justify-content-left align-items-center">
                                                <label class="mx-2" for="year">Select Year:</label>
                                                <div class="col-2">
                                                    <select class="w-100" name="year" id="year">
                                                        <option value="2023">2023</option>
                                                        <option value="2024">2024</option>
                                                        <option value="2025">2025</option>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-success mx-2 px-2 py-0 text-white">Filter</button>
                                            </div>
                                        </div>

                                        <div class="card text-white bg-primary shadow">
                                            <div class="container p-2">


                                                <div class="row m-0" id="textCont"> <!-- Alex:25/10/23: Add ID -->
                                                        <div class="container">
                                                            <div class="row font-weight-bold text-center">
                                                                <div class="col-md-1">R-ID</div>
                                                                <div class="col-md-3">Child Name</div>
                                                                <div class="col-md-8 d-inline-flex justified-content-center align-items-center">
                                                                    <div class="col-1 text-center">Jan</div>
                                                                    <div class="col-1 text-center">Feb</div>
                                                                    <div class="col-1 text-center">Mar</div>
                                                                    <div class="col-1 text-center">Apr</div>
                                                                    <div class="col-1 text-center">May</div>
                                                                    <div class="col-1 text-center">Jun</div>
                                                                    <div class="col-1 text-center">Jul</div>
                                                                    <div class="col-1 text-center">Aug</div>
                                                                    <div class="col-1 text-center">Sep</div>
                                                                    <div class="col-1 text-center">Oct</div>
                                                                    <div class="col-1 text-center">Nov</div>
                                                                    <div class="col-1 text-center">Dec</div>
                                                                </div>
                                                            </div>

            
                                                            <?php foreach ($childInfo as $child) { ?>
                                                                <div class="row my-1">
                                                                    <div class="col-1">
                                                                        <div class=" bg-white text-black rounded-pill text-center w-100"><?php echo $child['c_id']; ?></div>
                                                                    </div>
                                                                    <div class="col-3 bg-white text-black rounded-pill text-center"><?php echo $child['c_name']; ?></div>
                                                                    <div class="col-8 d-inline-flex">
                                                                        <?php foreach ($child['p_status'] as $status) { ?>
                                                                            <div class="col-1 text-center">
                                                                                <?php if ($status == 'Paid') : ?>
                                                                                    <span class="bg-white text-black rounded-pill px-3 user-select-none">&#10003;</span> <!-- Checkmark symbol -->
                                                                                <?php else : ?>
                                                                                    <span class="bg-white rounded-pill px-3 user-select-none">&#10003;</span> <!-- Blank box -->
                                                                                <?php endif; ?>
                                                                            </div>
                                                                        <?php } ?>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                        </div>
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
</body>

</html>
<!-- end -->