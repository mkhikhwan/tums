<?php
session_start();
require('lib/fpdf/fpdf.php');
include 'config.php';

class PDFReceipt extends FPDF
{
    private $companyName = "TASKA UNIMAS";
    private $companyAddress = "94300 KOTA SAMARAHAN, SARAWAK";
    private $companyPhone = "TEL : 082-345401";

    function header()
    {
        $this->SetFont('Arial', 'B', 24);
        $this->Cell(0, 10, $this->companyName, 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, $this->companyAddress, 0, 1, 'C');
        $this->Cell(0, 10, $this->companyPhone, 0, 1, 'C');
        $this->Ln(10);
    }

    function footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }

    function addBillingDetails($customerName, $email, $contactNumber, $billingAddress1, $billingAddress2 = '', $billingAddress3 = '', $postcode, $state)
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'Billing Details', 0, 1);
    
        $this->SetFont('Arial', '', 12);
    
        $this->Cell(40, 10, 'Name:', 0);
        $this->Cell(80, 10, $customerName, 0, 1);
    
        $this->Cell(40, 10, 'Email:', 0);
        $this->Cell(80, 10, $email, 0, 1);
    
        $this->Cell(40, 10, 'Contact Number:', 0);
        $this->Cell(80, 10, $contactNumber, 0, 1);
    
        $this->Cell(40, 10, 'Billing Address:', 0);
        if (!empty($billingAddress2)) {
            $this->Cell(80, 10, $billingAddress1, 0, 1);
            $this->Cell(40, 10, '', 0);
            $this->Cell(80, 10, $billingAddress2, 0, 1);
            if (!empty($billingAddress3)) {
                $this->Cell(40, 10, '', 0);
                $this->Cell(80, 10, $billingAddress3, 0, 1);
            }
        } else {
            $this->Cell(80, 10, $billingAddress1, 0, 1);
        }
    
        $this->Cell(40, 10, 'Postcode:', 0);
        $this->Cell(80, 10, $postcode, 0, 1);
    
        $this->Cell(40, 10, 'State:', 0);
        $this->Cell(80, 10, $state, 0, 1);
    
        $this->Ln(10);
    }


    function addReceiptDetails($refNo, $dateOfPayment)
    {
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'Ref No: ' . $refNo, 0, 1);
        $this->Cell(0, 10, 'Date of Payment: ' . $dateOfPayment, 0, 1);
        $this->Ln(10);
    }

    function addPaymentDetails($paymentDetails)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetWidths(array(10, 20, 120, 40)); // Set column widths
        $this->Row(array('#', 'ID', 'Payment Details', 'Cost (RM)'));

        foreach ($paymentDetails as $index => $payment) {
            $this->Row(array($index + 1, $payment['p_id'], $payment['p_name'], $payment['p_price']));
        }
    }

    function addTotalCosts($subTotal, $tax, $serviceCharge, $total)
    {
        $this->Ln(10);
        $this->Cell(80, 10, 'Sub Total (RM):', 0, 0);
        $this->Cell(30, 10, $subTotal, 0, 1);

        $this->Cell(80, 10, 'Tax (RM):', 0, 0);
        $this->Cell(30, 10, $tax, 0, 1);

        $this->Cell(80, 10, 'Service Charge (RM):', 0, 0);
        $this->Cell(30, 10, $serviceCharge, 0, 1);

        $this->SetFont('Arial', 'B', 12);
        $this->Cell(80, 10, 'TOTAL (RM):', 0, 0);
        $this->Cell(30, 10, $total, 0, 1);
    }

    function SetWidths($widths)
    {
        $this->widths = $widths;
    }

    function Row($data)
    {
        $this->SetFont('Arial', '', 12);
        $nb = 0;
        for ($i = 0; $i < count($data); $i++) {
            $nb = max($nb, $this->GetStringWidth($data[$i]));
        }
        $nb += 2;
        $w = array();
        for ($i = 0; $i < count($data); $i++) {
            $w[$i] = $this->widths[$i];
        }
        $this->CheckPageBreak($nb);
        for ($i = 0; $i < count($data); $i++) {
            $x = $this->GetX();
            $y = $this->GetY();
            $this->Rect($x, $y, $w[$i], 10);
            $this->MultiCell($w[$i], 10, $data[$i], 0, 'C');
            $this->SetXY($x + $w[$i], $y);
        }
        $this->Ln();
    }

    function CheckPageBreak($h)
    {
        if ($this->GetY() + $h > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
        }
    }
}

function getInvoiceDetails($conn, $targetInvoiceId)
{
    $result = array();

    // Fetch details from the 'invoice' table for a specific invoice_id
    $invoiceQuery = "SELECT * FROM invoice WHERE invoice_id = " . $targetInvoiceId;
    $invoiceResult = $conn->query($invoiceQuery);

    while ($invoiceRow = $invoiceResult->fetch_assoc()) {
        $invoice = array(
            'invoice_id' => $invoiceRow['invoice_id'],
            'name' => $invoiceRow['name'],
            'email' => $invoiceRow['email'],
            'contact' => $invoiceRow['contact'],
            'address1' => $invoiceRow['address1'],
            'address2' => $invoiceRow['address2'],
            'address3' => $invoiceRow['address3'],
            'postcode' => $invoiceRow['postcode'],
            'state' => $invoiceRow['state'],
            'subTotal' => $invoiceRow['subTotal'],
            'tax' => $invoiceRow['tax'],
            'serviceCharge' => $invoiceRow['serviceCharge'],
            'total' => $invoiceRow['total'],
            'paymentDate' => $invoiceRow['paymentDate'] // Added paymentDate field
        );

        // Fetch details from the 'invoiceItems' table and join with 'parentpayment'
        $invoiceItemsQuery = "SELECT ii.*, pp.p_name, pp.p_price
                              FROM invoiceItems ii
                              INNER JOIN parentpayment pp ON ii.p_id = pp.p_id
                              WHERE ii.invoice_id = " . $invoiceRow['invoice_id'];
        $invoiceItemsResult = $conn->query($invoiceItemsQuery);

        $items = array();
        while ($invoiceItemsRow = $invoiceItemsResult->fetch_assoc()) {
            $item = array(
                'item_id' => $invoiceItemsRow['item_id'],
                'p_id' => $invoiceItemsRow['p_id'],
                'p_name' => $invoiceItemsRow['p_name'],
                'p_price' => $invoiceItemsRow['p_price']
            );

            $items[] = $item;
        }

        $invoice['items'] = $items;

        $result[] = $invoice;
    }

    return $result;
}

function calculateInvoiceTotals($paymentDetails)
{
    $subTotal = 0.00;

    foreach ($paymentDetails as $payment) {
        $subTotal += floatval($payment['p_price']);
    }

    // Change Tax Here
    $taxRate = 0.0;
    $serviceChargeRate = 0.0;

    $tax = $subTotal * $taxRate;
    $serviceCharge = $subTotal * $serviceChargeRate;
    $total = $subTotal + $tax + $serviceCharge;

    return array(
        'subTotal' => number_format($subTotal, 2),
        'tax' => number_format($tax, 2),
        'serviceCharge' => number_format($serviceCharge, 2),
        'total' => number_format($total, 2),
    );
}

// GENERATE INVOICE/RECEIPT
if (isset($_POST['invoiceID'])) {
    // The POST variable is set
    $targetInvoiceId = $_POST['invoiceID'];
    $invoiceDetails = getInvoiceDetails($conn, $targetInvoiceId);

    if (!empty($invoiceDetails)) {
        $invoice = $invoiceDetails[0]; // Assuming only one invoice is retrieved
    
        // Invoice Details
        $customerName = $invoice['name'];
        $email = $invoice['email'];
        $contactNumber = $invoice['contact'];
        $billingAddress1 = $invoice['address1'];
        $billingAddress2 = $invoice['address2'];
        $billingAddress3 = $invoice['address3'];
        $postcode = $invoice['postcode'];
        $state = $invoice['state'];
        $refNo = $invoice['invoice_id'];
        $dateOfPayment = $invoice['paymentDate']; // Adjust this based on your actual database structure
        $paymentDetails = $invoice['items']; // Assuming the payment details are stored in the 'items' field
    
        // Total Calculations
        // This is just a double calculation if database were to not store values correctly
        $totals = calculateInvoiceTotals($paymentDetails);
    
        $subTotal = $totals['subTotal'];
        $tax = $totals['tax'];
        $serviceCharge = $totals['serviceCharge'];
        $total = $totals['total'];
        
        // Create PDF
        $pdf = new PDFReceipt();
        $pdf->AddPage();
    
        $pdf->addBillingDetails(
            $customerName, 
            $email, 
            $contactNumber, 
            $billingAddress1,
            $billingAddress2,
            $billingAddress3, 
            $postcode, 
            $state);
    
        $pdf->addReceiptDetails($refNo, $dateOfPayment);
        $pdf->addPaymentDetails($paymentDetails);
        $pdf->addTotalCosts($subTotal, $tax, $serviceCharge, $total);
    
        $pdf->Output('receipt.pdf', 'I');
    }else{
        echo "A problem occured!\n";
        echo "Please contact admin @ TASKA UNIMAS for assistance.\n";
    }

} else {
    echo "A problem occured!\n";
    echo "Please contact admin @ TASKA UNIMAS for assistance.\n";
}

?>
