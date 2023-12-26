<button id="rzp-button1" style="display:none;">Pay</button>
<?php
include('../../../retail-admin/include/conn.php');

require('config.php.sample');
require('razorpay-php/Razorpay.php');

/* $que2 = mysqli_query($conn,"SELECT * FROM slc_header");
$ro2 = mysqli_fetch_array($que2); */

$hidden_order_id = $_REQUEST['hidden_order_id'];
$hidden_order_total = $_REQUEST['hidden_order_total'];

$hidden_first_name = $_REQUEST['hidden_first_name'];
$hidden_last_name = $_REQUEST['hidden_last_name'];
$customer_name = $hidden_first_name." ".$hidden_last_name;
$hidden_email = $_REQUEST['hidden_email'];
$hidden_phone = $_REQUEST['hidden_phone'];
$hidden_address1 = $_REQUEST['hidden_address1'];
$hidden_address2 = $_REQUEST['hidden_address2'];
// Create the Razorpay Order

use Razorpay\Api\Api;

$api = new Api($keyId, $keySecret);

//
// We create an razorpay order using orders api
// Docs: https://docs.razorpay.com/docs/orders
//
$orderData = [
    'receipt'         => $hidden_order_id,
    'amount'          => $hidden_order_total * 100, // 2000 rupees in paise
    'currency'        => 'INR',
    'payment_capture' => 1 // auto capture
];

$razorpayOrder = $api->order->create($orderData);

$razorpayOrderId = $razorpayOrder['id'];

$_SESSION['razorpay_order_id'] = $razorpayOrderId;
$_SESSION['order_id'] = $hidden_order_id;
$order_id = $hidden_order_id;
$displayAmount = $amount = $orderData['amount'];
/* echo $displayAmount; */

if ($displayCurrency !== 'INR')
{
    $url = "https://api.fixer.io/latest?symbols=$displayCurrency&base=INR"; 
    $exchange = json_decode(file_get_contents($url), true);

    $displayAmount = $exchange['rates'][$displayCurrency] * $amount / 100;
}

$checkout = 'manual';

if (isset($_GET['checkout']) and in_array($_GET['checkout'], ['automatic', 'manual'], true))
{
    $checkout = $_GET['checkout'];
}


$data = [
    "key"               => $keyId,
    "amount"            => $amount,
    "name"              => $customer_name,
    "description"       => "Razorpay",
    "image"             => $website_logo,
    "prefill"           => [
    "name"              => $customer_name,
    "email"             => $hidden_email,
    "contact"           => $hidden_phone,
    ],
    "notes"             => [
    "address"           => $hidden_address1,
    "merchant_order_id" => $hidden_order_id,
    ],
    "theme"             => [
    "color"             => "#F37254"
    ],
    "order_id"          => $razorpayOrderId,
];

if ($displayCurrency !== 'INR')
{
    $data['display_currency']  = $displayCurrency;
    $data['display_amount']    = $displayAmount;
}

$json = json_encode($data);

require("checkout/{$checkout}.php");
