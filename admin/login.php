<?php
session_start();
include '../config.php';

$usernameErr = $passwordErr = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        $usernameErr = '<script>alert("Username is required!")</script>';
    } else {
        $username = test_input($_POST["username"]);
    }
    if (empty($_POST["password"])) {
        $passwordErr = '<script>alert("Password is required!")</script>';
    } else {
        $password = test_input($_POST["password"]);
    }

    if (empty($usernameErr) && empty($passwordErr)) {
        $query = "SELECT * FROM administrator WHERE a_username = '$username' AND a_password = '$password'";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            $_SESSION['logged_in'] = true;
            $userData = $result->fetch_assoc();
            $_SESSION['username'] = $userData['a_username'];

            header('Location: dashboard.php');
            exit();
        } else {
            // login failed - display an error message
            echo '<script>alert("Invalid username or password!")</script>';
        }
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!doctype html>
<html lang="en">
    <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Hammersmith+One&amp;display=swap">
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/css/login.css">

    <title>Login as Admin</title>

    <style>
        body{
            background-image: url("../assets/img/Background.png");
        }
    </style>
</head>
<body>
    <div class="container-fluid h-100 p-0 m-0">

        <div class="row m-0 p-0 gx-0 h-100">
            <div class="order-2 order-sm-1 col-sm-6 col-md-6 p-md-5 d-flex">
                <!-- LEFT -->
                <div class="login card w-100 p-2 m-1">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-12 text-center justify-content-center align-items-center">
                                        <p>Welcome Admin</p>
                                </div>
                            
                                <form method="POST">
                                    <div class="row p-0 m-1 gx-0">
                                        <label for="username_label">Username</label>
                                        <input type="text" name="username" id="username" class="form-control p-1 m-0" placeholder="">
                                    </div>

                                    <div class="row p-0 m-1 mt-3 gx-0 align-items-left">
                                        <label for="password_label">Password</label>
                                        <input type="password" name="password" id="password" class="form-control p-1 m-0" placeholder="">
                                    </div>

                                    <div class="text-center p-2 mt-3">
                                        <input type="submit" value="Login" class="login_button w-50">
                                    </div>
                                </form>

                                <?php
                                    if (isset($_SESSION['error'])) {
                                        echo '<div class="alert alert-warning">' . $_SESSION['error'] . '</div>';
                                        unset($_SESSION['error']);
                                    }
                                ?>
                                
                                <div class="text-center p-2">
                                <a href="../parent/login.php" class="login_button alternative w-100">Login as Parent</a>
                                </div>

                        
                                <div class="text-center p-2">
                                <a href="../teacher/login.php" class="login_button alternative w-100">Login as Teacher</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="order-1 order-sm-2 col-sm-6 col-md-6">
                <!-- LEFT -->
                <div class="logo card p-2 m-md-5 m-1">
                    <div class="card-body">
                        <div class="row">
                            <div class="col text-center m-0">
                                <p class="m-0 p-2">TASKA UNIMAS</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script> -->
</body>
</html>