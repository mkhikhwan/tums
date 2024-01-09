<?php
session_start();
include '../config.php';

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the selectedItems are received
if (isset($_POST['selectedItems']) && is_array($_POST['selectedItems'])) {
    // Loop through the selectedItems and delete corresponding records
    foreach ($_POST['selectedItems'] as $teacherId) {
        $escapedTeacherId = mysqli_real_escape_string($conn, $teacherId);
        $query = "DELETE FROM teacher WHERE t_id = '$escapedTeacherId'";

        // Execute the query
        if (mysqli_query($conn, $query)) {
            // Success, you can handle the response if needed
            echo "Record for Teacher ID $escapedTeacherId deleted successfully.\n";
        } else {
            // Error, you can handle the error response
            echo "Error deleting record for Teacher ID $escapedTeacherId: " . mysqli_error($conn) . "\n";
        }
    }
} else {
    // If selectedItems are not received, handle accordingly
    echo "No items selected for deletion.\n";
}

// Close the database connection
mysqli_close($conn);
?>
