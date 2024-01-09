<?php
session_start();
include '../config.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // sepatutnya join use id from table user(id) join with teacher(name) and children(name)
    // $query = "SELECT teachers.name AS mentor_name, children.name AS children_name
    //           FROM teachers
    //           INNER JOIN children ON teachers.program = children.program
    //           WHERE teachers.username = ?";

    // New query by ikhwan 28/12/2023
    // Query does is show all mentee from the mentor with row number and child name.
    // Row number is to make viewMentee much more easier.
    $query="SELECT ROW_NUMBER() OVER (ORDER BY mt.mentormentee_id) 
    AS row_numberA,mt.mentormentee_id, t.t_name 
    AS mentor_name, c.c_id 
    AS mentee_id, c.c_name 
    AS children_name
            FROM mentormentee mt
            JOIN teacher t ON mt.MentorID = t.t_id
            JOIN children c ON mt.MenteeID = c.c_id
            WHERE t.t_username = ?";

    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $_SESSION['username']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $item_data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            } else {
                echo "";
            }
        } else {
            echo "Error! " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error! " . mysqli_error($conn);
    }

    mysqli_close($conn);
?>

<!-- START HTML -->
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Mentee List</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hammersmith+One&amp;display=swap">
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/css/untitled.css">
</head>

<body id="page-top">
    <div id="wrapper">
        <nav class="navbar align-items-start sidebar sidebar-dark accordion bg-gradient-primary navbar-dark" id="sidebar"> <!--Alex:25/12/23-->
            <div class="container-fluid d-flex flex-column p-0" ><a class="navbar-brand d-flex justify-content-center align-items-center sidebar-brand m-0" href="#" style="font-size: larger;">
                    
                    <div class="sidebar-brand-text mx-3"><span id="sidebar_label">taska unimas</span></div>
                </a>
                <hr class="sidebar-divider my-0">
                <ul class="navbar-nav text-light mr-auto" id="accordionSidebar">
                    <li class="nav-item"><a class="nav-link" href="dashboardTeacher.php"><img class="logoH" src="..\assets\img\icons\home.png" alt=""></i><span>HOME</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="profilepageTeacher.php"><img class="logoH" src="..\assets\img\icons\profile.png" alt=""></i><span>PROFILE</span></a></li>
                    <li class="nav-item"><a class="nav-link" href="menteeTeacher.php"><img class="logoH" src="..\assets\img\icons\mentor.png" alt=""></i><span>MENTOR MENTEE</span></a></li>
                </ul>
                <div class="text-center d-none d-md-inline"><button class="btn rounded-circle border-0" id="sidebarToggle" type="button"></button></div>
                <!-- <button class="btn btn-primary" id="logout" type="button">Log out</button> -->
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
                            <br><h4>Mentee List</h4></label>
                    </div>
                </nav>

                <!-- Main Content -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <div class="row">
                                <div class="col-lg-12 col-xl-12 mb-0 m-0 p-0">
                                    <div class="card text-white bg-primary shadow">

                                        <!-- TABLE -->
                                        <div class="row p-0 m-0">
                                            <div class="container-fluid m-0 p-2" id="textCont"> <!--Alex:25/10/23-->
                                                <!-- HEADER -->
                                                <div class="row p-0 m-0 gx-0 text-center justify-content-center text-nowrap">
                                                    <!-- Number -->
                                                    <div class="col-sm-1 col-2">
                                                        <p>No.</p>
                                                    </div>

                                                    <!-- DATA -->
                                                    <div class="col-sm-11 col-10">
                                                        <p>Name of Children</p>                                         
                                                    </div>
                                                </div>

                                                <!-- INSERT DATA HERE -->
                                                <!-- For loop that prints mentee list -->
                                                <?php if (!empty($item_data)): ?>

                                                    <?php foreach ($item_data as $index => $mentee): ?>
                                                        <div class="row p-0 m-0 gx-0 text-center align-items-center justify-content-center d-flex">
                                                            <div class="col-sm-1 col-2">
                                                                <p><?= $index + 1 ?></p>
                                                            </div>

                                                            <div class="p-2 col-sm-11 col-10">
                                                            <div style = "cursor:pointer" class="white_box clickable" data-mentee-name="<?= $mentee['children_name'] ?>">
                                                                    <p><?= $mentee['children_name'] ?></p>   
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>

                                                <?php else: ?>
                                                    <p>No mentees found.</p>
                                                <?php endif; ?>

                                            </div> 
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
        // Add click event listener to all elements with class 'clickable'
        const clickableElements = document.querySelectorAll('.clickable');
        clickableElements.forEach(element => {
            element.addEventListener('click', function() {
                // Get the mentee name from the 'data-mentee-name' attribute
                const menteeName = this.getAttribute('data-mentee-name');

                // Redirect to the child's profile page with the name as a parameter
                window.location.href = 'viewMentee.php?name=' + encodeURIComponent(menteeName);
            });
        });
    </script>

</body>

</html>

<!-- END HTML -->

<?php
} else {
    header('Location: login.php');
}
?>
