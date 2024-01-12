<?php    
session_start();    
include '../config.php';
$user_data =[];

// Check items selected to be checkout
if(isset($_POST['selected_rows'])){
    $checkout = $_POST['selected_rows'];

    // RUN SQL TO GET ALL PENDING PAYMENTS
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $query = "SELECT * FROM parentpayment WHERE c_id=? AND p_status='Pending'";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $_SESSION['childID']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $paymentDetails = array();

            while ($row = mysqli_fetch_assoc($result)) {
                $paymentDetails[] = $row;
            }
        
            if (count($paymentDetails) > 0) {
                //print_r($paymentDetails);
                //echo "I am Here";
            } else {
                echo "No rows found.";
            }

            // Free the result set
            mysqli_free_result($result);
        } else {
            echo "Error!" . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error!" . mysqli_error($conn);
    }

    // FILTER THEM BASED ON WHAT WAS SELECTED ON LAST PAGE

}else{
    header('Location: payment.php');
    exit();
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $query = "SELECT * FROM children WHERE c_username = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $_SESSION['username']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                $user_data = mysqli_fetch_assoc($result);
            } else {
                echo "Error!.";
            }
        } else {
            echo "Error!" . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error!" . mysqli_error($conn);
    }

    

    mysqli_close($conn);
} else {
    header('Location: dashboardTeacher.php');
}

function pidtoStripe($paymentDetails,$user_data){
    // THIS FUNCTION TELLS STRIPE ON WHAT ITEMS TO CHOOSE BASED IN THE CART
    // TO ADD ITEMS, ADD PRODUCTS IN STRIPE, COPY THE PRICE ID AND INSERT INTO THIS FUNCTION

    $stripeProducts = array();

    foreach ($paymentDetails as $rows) {
        $itemList = array();

        // Product 1 : Monthly Fee (0-12 months)
        if (strpos($rows['p_name'], 'Fee') !== false && $user_data["c_age"] === 0) {
            if(strpos($user_data['c_UNIMASstaff'] , 'Yes')){
                // Staff price
                $id = "price_1OWF3RFp8k55haph45Z3PuRI";
            }else{
                // Non-Staff price
                $id = "price_1OWF5YFp8k55haphq4IHXiTZ";
            }

            $itemList = [
                'price' => $id,
                'quantity' => 1
            ];
        }

        // Product 2 : Monthly Fee (1-2 yo)
        if (strpos($rows['p_name'], 'Fee') !== false && $user_data["c_age"] === 1) {
            if(strpos($user_data['c_UNIMASstaff'] , 'Yes')){
                // Staff price
                $id = "price_1OWH1BFp8k55haphpfHUp88f";
            }else{
                // Non-Staff price
                $id = "price_1OWH5NFp8k55haphstE7PPtJ";
            }

            $itemList = [
                'price' => $id,
                'quantity' => 1
            ];
        }

        // Product 3 : Monthly Fee (3-4 yo)
        if (strpos($rows['p_name'], 'Fee') !== false && $user_data["c_age"] === 2) {
            if(strpos($user_data['c_UNIMASstaff'] , 'Yes')){
                // Staff price
                $id = "price_1OWHGrFp8k55haphVd0mox5o";
            }else{
                // Non-Staff price
                $id = "price_1OWHHkFp8k55haphopYH985j";
            }

            $itemList = [
                'price' => $id,
                'quantity' => 1
            ];
        }

        array_push($stripeProducts,$itemList);
        unset($array);
    }
    return $stripeProducts;
}
?>

<!-- START OF HTML -->
<!DOCTYPE html>
<html data-bs-theme="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Checkout</title>
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
                            <br><h4>Checkout</h4></label>
                    </div>
                </nav>

                <!-- MAIN CONTENT -->
                <form action="checkout/processPayment.php" method="POST" onsubmit="return validateForm()">
                <div class="container-fluid">
                    <div class="row">
                        <!-- Form Box -->
                        <div class="col-lg-8 mb-4">
                            <div class="card text-white bg-primary shadow">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col d-flex justify-content-center mb-3 ">
                                            <h4>Payer Details</h4>
                                        </div>
                                    </div>

                                    <!-- Checkout Form -->
                                    <!-- <form> -->
                                        <!-- Name -->
                                        <div class="form-group row mb-3">
                                            <label for="name" class="col-sm-3 col-form-label">Name*</label>
                                            <div class="col-sm-9">
                                                <!-- Input with value of the current login user-->
                                                <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($user_data['c_name']) ? $user_data['c_name'] : ''; ?>" required >
                                            </div>
                                        </div>

                                        <!-- Email Address -->
                                        <div class="form-group row mb-3">
                                            <label for="email" class="col-sm-3 col-form-label">Email Address*</label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control" id="email" name="email" required>
                                            </div>
                                        </div>

                                        <!-- Contact Number -->
                                        <div class="form-group row mb-3">
                                            <label for="contact" class="col-sm-3 col-form-label">Contact Number*</label>
                                            <div class="col-sm-9">
                                                <input type="tel" class="form-control" id="contact" name="contact" value="<?php echo isset($user_data['c_FatherPhoneNo']) ? $user_data['c_FatherPhoneNo'] : ''; ?>" required>
                                            </div>
                                        </div>

                                        <!-- Billing Address -->
                                        <div class="form-group row mb-3">
                                            <label for="address1" class="col-sm-3 col-form-label">Billing Address*</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="address1" name="address1" value="<?php echo isset($user_data['c_address']) ? $user_data['c_address'] : ''; ?>" required style="margin-bottom: 10px;">
                                                <input type="text" class="form-control" id="address2" name="address2" style="margin-bottom: 10px;">
                                                <input type="text" class="form-control" id="address3" name="address3" style="margin-bottom: 10px;">
                                            </div>
                                        </div>

                                        <!-- Postcode -->
                                        <div class="form-group row mb-3">
                                            <label for="postcode" class="col-sm-3 col-form-label">Postcode*</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="postcode" name="postcode" required>
                                            </div>
                                        </div>

                                        <!-- Alex 6/1/2024 -->
                                        <!-- Split the string into an array of words -->
                                        <?php 
                                        $words = explode(", ", $user_data['c_address']);

                                        //Get the last word, which normally in an address is the state
                                        $state = end($words);
                                        ?>

                                        <!-- State -->
                                        <div class="form-group row mb-3">
                                            <label for="state" class="col-sm-3 col-form-label">State*</label>
                                            <div class="col-sm-9">
                                                <!--Alex 6/1/2023 - Check the $state value with the option, if match echo 'selected' -->
                                                <select class="form-control" id="state" name="state" required>
                                                <option value="Johor" <?php echo isset($state) && $state == 'Johor' ? 'selected' : ''; ?>>Johor</option>
                                                <option value="Kedah" <?php echo isset($state) && $state == 'Kedah' ? 'selected' : ''; ?>>Kedah</option>
                                                <option value="Kelantan" <?php echo isset($state) && $state == 'Kelantan' ? 'selected' : ''; ?>>Kelantan</option>
                                                <option value="Kuala Lumpur" <?php echo isset($state) && $state == 'Kuala Lumpur' ? 'selected' : ''; ?>>Kuala Lumpur</option>
                                                <option value="Labuan" <?php echo isset($state) && $state == 'Labuan' ? 'selected' : ''; ?>>Labuan</option>
                                                <option value="Melaka" <?php echo isset($state) && $state == 'Melaka' ? 'selected' : ''; ?>>Melaka</option>
                                                <option value="Negeri Sembilan" <?php echo isset($state) && $state == 'Negeri Sembilan' ? 'selected' : ''; ?>>Negeri Sembilan</option>
                                                <option value="Pahang" <?php echo isset($state) && $state == 'Pahang' ? 'selected' : ''; ?>>Pahang</option>
                                                <option value="Perak" <?php echo isset($state) && $state == 'Perak' ? 'selected' : ''; ?>>Perak</option>
                                                <option value="Perlis" <?php echo isset($state) && $state == 'Perlis' ? 'selected' : ''; ?>>Perlis</option>
                                                <!-- <option value="Penang" <?//php echo isset($state) && $state == 'Penang' ? 'selected' : ''; ?>>Penang</option> -->
                                                <option value="Penang" <?php echo isset($state) && ($state == 'Penang' || $state == 'Pulau Pinang') ? 'selected' : ''; ?>>Penang</option>
                                                <option value="Sabah" <?php echo isset($state) && $state == 'Sabah' ? 'selected' : ''; ?>>Sabah</option>
                                                <option value="Sarawak" <?php echo isset($state) && $state == 'Sarawak' ? 'selected' : ''; ?>>Sarawak</option>
                                                <option value="Terengganu" <?php echo isset($state) && $state == 'Terengganu' ? 'selected' : ''; ?>>Terengganu</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group text-white">
                                            <p >Field marked with * are required.</p>
                                            <p>For billing address, line 2 and 3 are optional.</p>
                                        </div>
                                    <!-- </form> -->
                                </div>
                            </div>
                        </div>

                        <!-- Item Box -->
                        <div class="col-lg-4 mb-4">
                            <div class="card text-white bg-primary shadow">
                                <div class="card-body">

                                    <!-- Item Content -->
                                    <h5 class="card-title">Payment Details</h5>

                                    <!-- Print items @ Ikhwan--> 
                                    <!-- Alex 6/1/24 -->
                                    <?php
                                        $totalSum = 0;
                                        $printedData = '';
                                        $selected_p_ids = array(); // Initialize an empty array to store selected p_ids
                                        $selected_items = array(); // Ikhwan -> For stripe purposes

                                        foreach ($paymentDetails as $row) {
                                            // Check if the current row ID is in the selected rows
                                            if (in_array($row['p_id'], $_POST['selected_rows'])) {
                                                array_push($selected_items,$row);
                                                echo '<div class="col-lg-12 col-xl-12 mb-4">';
                                                    echo '<div class="card text-white shadow">';
                                                        echo '<div id="textCont" style="color: #3B4174; padding: 10px;">';
                                                            // Print the selected row details
                                                            print_r('ID: ' . $row['p_id'] . ' | ' . $row['p_name'] . ' | RM' . $row['p_price']);
                                                             // Append the selected row details to the variable - Alex:6/1/24
                                                            $printedData .= 'ID: ' . $row['p_id'] . ' | ' . $row['p_name'] . ' | RM' . $row['p_price'] . '<br>';
                                                        echo '</div>';
                                                    echo '</div>';
                                                echo '</div>';
                                                $totalSum += $row['p_price'];
                                            }
                                        }
                                        $_SESSION['stripeCart'] = pidtoStripe($selected_items,$user_data);
                                    ?>
                                    <input class="mb-0" type="hidden" name="paymentdetail" id="paymentdetail" value="<?php echo $printedData; ?>" />

                                    <hr>
                                    <!-- Alex 6/1/24 -->
                                    <div class="row">
                                        <div class="col">
                                            <p class="mb-0">Sub-total :</p>
                                            <p class="mb-0">Tax :</p>
                                            <p class="mb-0">Service Charge :</p>
                                            <p class="font-weight-bold">TOTAL :</p>
                                        </div>
                                        <div class="col text-right">
                                            <?php
                                                $subTotal = $totalSum;
                                                $tax = 0;
                                                $serviceCharge = 0;

                                                // Calculate the total by adding the individual amounts
                                                $total = $subTotal + $tax + $serviceCharge;
                                            ?>

                                            <p class="mb-0">RM <?php echo number_format($subTotal, 2); ?></p>
                                            <p class="mb-0">RM <?php echo number_format($tax, 2); ?></p>
                                            <p class="mb-0">RM <?php echo number_format($serviceCharge, 2); ?></p>
                                            <p class="font-weight-bold">RM <?php echo number_format($total, 2); ?></p>
                                            
                                            <input class="mb-0" type="hidden" name="subtotal" id="subtotal" value="<?php echo number_format($subTotal, 2); ?>" />
                                            <input class="mb-0" type="hidden" name="tax" id="tax" value="<?php echo number_format($tax, 2); ?>" />
                                            <input class="mb-0" type="hidden" name="servicecharge" id="servicecharge" value="<?php echo number_format($serviceCharge, 2); ?>" />
                                            <input class="font-weight-bold" type="hidden" name="total" id="total" value="RM <?php echo number_format($total, 2); ?>" />

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- <a href="../logout.php" class="btn btn-primary" id="logout">Back</a> -->
                            <div style="margin-top: 10px" class="text-end">
                                <button class="btn btn-primary me-2" type="button" onclick="goBack()" 
                                style=" width: 30% !important; 
                                        --bs-btn-color: #fff !important;
                                        --bs-btn-bg: #FD8D4B !important;
                                        --bs-btn-border-color: #FD8D4B !important;
                                        --bs-btn-hover-color: #fff !important;
                                        --bs-btn-hover-bg: #aa6035 !important;
                                        --bs-btn-hover-border-color: #aa6035 !important;
                                        --bs-btn-focus-shadow-rgb: 105, 136, 228 !important;
                                        --bs-btn-active-color: #fff !important;
                                        --bs-btn-active-bg: #ff5e00 !important;
                                        --bs-btn-active-border-color: #ff5e00 !important;
                                        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125 !important);
                                        --bs-btn-disabled-color: #fff !important;
                                        --bs-btn-disabled-bg: #ff5e00 !important;
                                        --bs-btn-disabled-border-color: #ff5e00 !important;
                                ">Go Back</button>

                                <!-- <a href="#ToBeFill" class="btn btn-primary" style=" width: 30% !important;  -->
                                <!-- <button name="payment" class="btn btn-primary" type="submit" style=" width: 30% !important; -->
                                <input value="Pay" name="payment" class="btn btn-primary" type="submit" style="width: 30% !important;
                                        --bs-btn-color: #fff !important;
                                        --bs-btn-bg: #00CE15 !important;
                                        --bs-btn-border-color: #00CE15 !important;
                                        --bs-btn-hover-color: #fff !important;
                                        --bs-btn-hover-bg: #1a9d27 !important;
                                        --bs-btn-hover-border-color: #1a9d27 !important;
                                        --bs-btn-focus-shadow-rgb: 105, 136, 228 !important;
                                        --bs-btn-active-color: #fff !important;
                                        --bs-btn-active-bg: #00ff1a !important;
                                        --bs-btn-active-border-color: #00ff1a !important;
                                        --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125 !important);
                                        --bs-btn-disabled-color: #fff !important;
                                        --bs-btn-disabled-bg: #00ff1a !important;
                                        --bs-btn-disabled-border-color: #00ff1a !important;
                                ">
                                <!-- Pay</button> -->
                            </div>
                        </div>
                    </div>
                </div>
                </form>

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
    <script> //Alex 6/1/24
        // JavaScript function to go back
        function goBack() {
            window.history.back();
        }

        function validateForm() {
        // Get form inputs
        var name = document.getElementById('name').value;
        var email = document.getElementById('email').value;
        var contact = document.getElementById('contact').value;
        var address1 = document.getElementById('address1').value;
        var postcode = document.getElementById('postcode').value;
        var state = document.getElementById('state').value;

        // Check for empty values
        if (name === "" || email === "" || contact === "" || address1 === "" || postcode === "" || state === "") {
            alert("Please fill in all required fields.");
            return false;
        }
        return true;
        }
    </script>
</body>

</html>
