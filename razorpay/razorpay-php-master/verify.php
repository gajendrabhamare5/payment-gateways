<?php
include('../../../retail-admin/include/conn.php');
include "../../../retail-admin/mail_function.php";
require('config.php.sample');

/* $order_id = $_REQUEST['order_id']; */
$order_id = $_SESSION['order_id'];
$uid = $_SESSION['UserID'];
require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$success = true;

$error = "Payment Failed";
$orderquery=$conn->query("select * from order_master where user_id='$uid' AND order_id='$order_id'");
	$orderdata=mysqli_fetch_assoc($orderquery);
	$order_total_amount=$orderdata['order_total'];

	$fetchquery=$conn->query("select * from address_master where user_id='$uid' AND type='1'");
	$fetchcount=mysqli_num_rows($fetchquery);
	$fetchdata=mysqli_fetch_assoc($fetchquery);

	$billing_phone=$fetchdata['phone_no'];

	$billing_email=$fetchdata['email_id'];
	$billing_first_name=$fetchdata['first_name'];
	$billing_last_name=$fetchdata['last_name'];
if (empty($_POST['razorpay_payment_id']) === false)
{
	  $success = false;
    $api = new Api($keyId, $keySecret);

    try
    {
        // Please note that the razorpay order ID must
        // come from a trusted source (session here, but
        // could be database or something else)
        $attributes = array(
            'razorpay_order_id' => $_SESSION['razorpay_order_id'],
            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
            'razorpay_signature' => $_POST['razorpay_signature']
        );

        $api->utility->verifyPaymentSignature($attributes);
		$payment = $api->payment->fetch($_POST['razorpay_payment_id']);
		/* if($uid == "525")
		{		
			echo "<pre>";
			print_r($payment);
			echo "</pre>";
			echo "<pre>";
			print_r($_SESSION);
			echo "</pre>";
			echo $_SESSION['razorpay_order_id'];
		} */
		$razor_pay_amount=$payment->amount;
		$txn_id=$payment->id;
		
		if($payment->status=="captured")
		{
			$success = true;
		}
		else
		{
			$success = false;
		}
    }
    catch(SignatureVerificationError $e)
    {
        $success = false;
        $error = 'Razorpay Error : ' . $e->getMessage();  
    }
}

if ($success === true)
{
    $html = "<p>Your payment was successful</p>
             <p>Payment ID: $txn_id</p>";
			 
			 $conn->query("update order_master set order_status='6',txn_id='$txn_id' where order_id='$order_id'");
			
			
			$q = $conn->query("delete from `cart_master` where `UserID`=$uid");
			send_mail($order_id);
}
else
{
    $html = "<p>Your payment failed</p>
             <p>{$error}</p>";
			 $conn->query("update order_master set order_status='4' where order_id='$order_id'");
			   send_mail($order_id);
}
/* if($uid != "525")
	{ */
header('Location:'.$base_url.'order_details/'.$order_id.'');
/* 	} */
//echo $html;
?>