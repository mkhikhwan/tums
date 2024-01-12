<?php
session_start();
include '../config.php';

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the selectedItems are received
if (isset($_POST['selectedItems']) && is_array($_POST['selectedItems'])) {
    print_r($_POST['selectedItems']);

    foreach ($_POST['selectedItems'] as $teacherId) {
        $escapedTeacherId = mysqli_real_escape_string($conn, $teacherId);

        // Retrieve the filename of the profile picture
        $filenameQuery = "SELECT t_profilePicture FROM teacher WHERE t_id='$escapedTeacherId'";
        $filenameResult = mysqli_query($conn, $filenameQuery);

        if ($filenameResult && mysqli_num_rows($filenameResult) > 0) {
            $row = mysqli_fetch_assoc($filenameResult);
            $profilePictureFilename = $row['t_profilePicture'];

            // Delete the profile picture file
            $profilePicturePath = "../data/img/teacher/" . $profilePictureFilename;
            if (file_exists($profilePicturePath)) {
                unlink($profilePicturePath);
                echo "Profile picture for $escapedTeacherId deleted successfully.\n";
            } else {
                echo "Profile picture for $escapedTeacherId not found.\n";
            }
        } else {
            echo "Error retrieving profile picture filename for $escapedTeacherId: " . mysqli_error($conn) . "\n";
        }

        // Delete the record from the database
        $deleteQuery = "DELETE FROM teacher WHERE t_id = '$escapedTeacherId'";
        if (mysqli_query($conn, $deleteQuery)) {
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
