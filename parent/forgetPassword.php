<?php
include '../config.php';

function getUserCredentialsByEmail($email) {
    global $conn;

    if (!$conn) {
        echo "Error connecting to the database: " . mysqli_connect_error();
        return null;
    }

    $query = "SELECT c_name, c_username, c_password FROM children WHERE c_email = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $user_data = mysqli_fetch_assoc($result);
            } else {
                echo "No user found with the given email.";
            }
        } else {
            echo "Error fetching result: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }

    mysqli_close($conn);

    return $user_data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once '../phpmailer_load.php';

    $email = $_POST['email'];
    $user_data = getUserCredentialsByEmail($email);

    if ($user_data !== null) {
        $result = emailAccountDetails($email, $user_data['c_name'], $user_data['c_username'], $user_data['c_password']);

        if ($result === true) {
            echo 'An email has been sent with your account details.';
        } else {
            echo 'Error sending email: ' . $result;
        }
    } else {
        echo 'Error retrieving user data. Check the provided email address.';
    }
}
?>