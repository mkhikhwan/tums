<?php
session_start();

// Check if the user is logged in before attempting to log out
if (isset($_SESSION['logged_in'])) {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Destroy the session
    session_destroy();

    // Redirect the user to the login page
    echo '<script>alert("Logout successful")</script>';
    header("refresh:1;url=loginTeacher.php");
    exit();
} else {
    // Display a message if the user is not logged in
    echo '<script>alert("You are not logged in. Redirecting to the login page...")</script>';
    header("refresh:1;url=loginTeacher.php"); // Redirect to loginTeacher.php after 5 seconds
    exit();
}
?>
