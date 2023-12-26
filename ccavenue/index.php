<?php
include "../../retail-admin/include/conn.php";
session_start();
$uid = $_SESSION['UserID'];
$oid = $_GET['oid'];
$type = $_GET['type'];
if(empty($uid)){
    $uid = session_id();
    $fetch_user_id = $conn->query("SELECT * from user_master_wholesale where guest_id = '$uid'");
    $data_user_id = mysqli_fetch_array($fetch_user_id);
    $uid = $data_user_id['user_id'];
}
$user= $conn->query("select Email from user_master_wholesale where user_id='$uid'");
$user_data=mysqli_fetch_assoc($user);
$email=$user_data['Email'];
$orderquery=$conn->query("select * from order_master where user_id='$uid' AND order_id='$oid'");
	$orderdata=mysqli_fetch_assoc($orderquery);
	//$order_total_amount=1;
	$order_discount_amount=$orderdata['discount_amount'];
	if(empty($order_discount_amount)){
		$order_discount_amount = 0;
	}
	$order_total_amount=$orderdata['order_total'] - $order_discount_amount;
	$address_id=$orderdata['address_id'];
	/*
	$fetchquery=$conn->query("select * from address_master where address_id='$address_id'");
	$fetchdata=mysqli_fetch_assoc($fetchquery);
	$billing_address1 = $fetchdata['address'];
	$billing_address2 = $fetchdata['address2'];
	$billing_phone=$fetchdata['phone_no'];
	$billing_email=$fetchdata['email_id'];
	$billing_first_name=$fetchdata['first_name'];
	$billing_last_name=$fetchdata['last_name'];
	$billing_city=$fetchdata['city'];
	$billing_state=$fetchdata['state'];
	$billing_zip=$fetchdata['pincode'];
	$billing_country=ucwords(strtolower($fetchdata['country'])); */
	$fetchquery=$conn->query("select * from order_address_master where address_id='$address_id'");
	$fetchcount=mysqli_num_rows($fetchquery);
	$fetchdata=mysqli_fetch_assoc($fetchquery);
	$billing_type=$fetchdata['type'];
	if($billing_type != 3){
		$billing_address1 = $fetchdata['address'];
		$billing_address2 = $fetchdata['address2'];
	}
	else{
		$billing_address1 = "";
		$billing_address2 = "";
	}
	$billing_phone=$fetchdata['phone_no'];
	$billing_email=$user_data['Email'];
	$billing_first_name=$fetchdata['first_name'];
	$billing_last_name=$fetchdata['last_name'];
	$billing_city=$fetchdata['city'];
	$billing_state=$fetchdata['state'];
	$billing_zip=$fetchdata['pincode'];
	$billing_country=ucwords(strtolower($fetchdata['country']));
	/* $conn->query("update user_master_wholesale set guest_id='' where `user_id`='$uid'"); */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>CCAvenue Payment</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script>
	window.onload = function() {
		var d = new Date().getTime();
		document.getElementById("tid").value = d;
		document.customerData.submit();
	};
</script>
</head>
<body>
	<div class="container" style="display:none;">
  <h2>CCAvenue Payment Gateway</h2>
	<form method="post" name="customerData" action="ccavRequestHandler.php">
<div class="form-group">
      <input type="text" class="form-control" id="name" placeholder="Enter name" name="name" value="<?php echo $billing_first_name." ".$billing_last_name; ?>" readonly>
    </div>
    <div class="form-group">
      <input type="number" class="form-control" id="phone" name="phone" value="<?php echo $billing_phone; ?>" readonly>
      <input type="hidden" class="form-control" id="billing_name" name="billing_name" value="<?php echo $billing_first_name; ?> <?php echo $billing_last_name; ?>" readonly>
      <input type="hidden" class="form-control" id="billing_address" name="billing_address" value="<?php echo $billing_address1; ?>, <?php echo $billing_address2; ?>" readonly>
      <input type="hidden" class="form-control" id="billing_zip" name="billing_zip" value="<?php echo $billing_zip; ?>" readonly>
      <input type="hidden" class="form-control" id="billing_city" name="billing_city" value="<?php echo $billing_city; ?>" readonly>
      <input type="hidden" class="form-control" id="billing_state" name="billing_state" value="<?php echo $billing_state; ?>" readonly>
      <input type="hidden" class="form-control" id="billing_country" name="billing_country" value="<?php echo $billing_country; ?>" readonly>
      <input type="hidden" class="form-control" id="billing_tel" name="billing_tel" value="<?php echo $billing_phone; ?>" readonly>
      <input type="hidden" class="form-control" id="billing_email" name="billing_email" value="<?php echo $billing_email; ?>" readonly>
    </div>
 <div class="form-group">
      <input type="text" class="form-control" id="amount" placeholder="Enter amount" name="amount" value="<?php echo $order_total_amount; ?>" readonly>
    </div>
				<input type="hidden" name="tid" id="tid" readonly />
				<input type="hidden" name="merchant_id" value="<?php echo $merchant_id; ?>"/>
				<input type="hidden" name="order_id" value="<?php echo $_GET['oid']; ?>"/>
				<input type="hidden" name="currency" value="INR"/>
				<input type="hidden" name="redirect_url" value="<?php echo $base_url; ?>payment/ccavenue/ccavResponseHandler.php"/>
				<input type="hidden" name="cancel_url" value="<?php echo $base_url; ?>payment/ccavenue/ccavResponseHandler.php"/><input type="hidden" name="language" value="EN"/>
				<button type="submit" class="btn btn-default">Pay Now</button>
	      </form>
	  </div>
	</body>
</html>
