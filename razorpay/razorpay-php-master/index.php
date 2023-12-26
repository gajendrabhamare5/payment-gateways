<?php
include('../../../retail-admin/include/conn.php');
session_start();

$order_id = $_REQUEST['oid'];
$uid = $_SESSION['UserID'];

$orderquery = $conn->query("select * from order_master where user_id='$uid' AND order_id='$order_id'");

$orderdata = mysqli_fetch_assoc($orderquery);
$order_total_amount = $orderdata['order_total'];
$payment_method = $orderdata['payment_method'];
if (strtolower($payment_method) == "cash on delivery") {
	$order_total_amount = $order_total_amount * 15 / 100;
}
if ($uid == "173") {
	$order_total_amount = "1";
}
if ($order_total_amount == "") {
	$order_total_amount = "10000";
}
/* echo $order_total_amount; */
$address_id = $orderdata['address_id'];

$fetchquery = $conn->query("select * from order_address_master where address_id='$address_id'");
$fetchcount = mysqli_num_rows($fetchquery);
$fetchdata = mysqli_fetch_assoc($fetchquery);

$billing_type = $fetchdata['type'];
if ($billing_type != 3) {
	$billing_address1 = $fetchdata['address'];
	$billing_address2 = $fetchdata['address2'];
} else {
	$billing_address1 = "";
	$billing_address2 = "";
}
$billing_phone = $fetchdata['phone_no'];

$billing_email = $fetchdata['email_id'];
$billing_first_name = $fetchdata['first_name'];
$billing_last_name = $fetchdata['last_name'];
$billing_city = $fetchdata['city'];
$billing_state = $fetchdata['state'];
$billing_zip = $fetchdata['pincode'];
$billing_country = ucwords(strtolower($fetchdata['country']));
/* exit(); */

?>
<!DOCTYPE html>
<html>

<head lang="en">
    <meta charset="utf-8">
    <link rel="stylesheet" href="tacit.min.css">
</head>

<body>
    <form id="checkout-selection" method="POST" action="pay.php" name="customerData" style="display:none;">
        <input type="hidden" name="checkout" value="manual">
        <input type="hidden" name="hidden_order_total" value="<?php echo $order_total_amount ?>">
        <input type="hidden" name="hidden_order_id" value="<?php echo $order_id ?>">
        <input type="hidden" name="hidden_first_name" value="<?php echo $billing_first_name ?>">
        <input type="hidden" name="hidden_last_name" value="<?php echo $billing_last_name ?>">
        <input type="hidden" name="hidden_email" value="<?php echo $billing_email ?>">
        <input type="hidden" name="hidden_phone" value="<?php echo $billing_phone ?>">
        <input type="hidden" name="hidden_address1" value="<?php echo $billing_address1 ?>">
        <input type="hidden" name="hidden_address2" value="<?php echo $billing_address2 ?>">


        <input type="submit" value="Submit" id="formsub">
    </form>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script>
    jQuery(document).ready(function($) {
        document.customerData.submit();
        /*   var form = $('#checkout-selection');
            var radio = $('input[name="checkout"]');
            var choice = '';
			var order_total_amount = '<?php echo $order_total_amount; ?>';
			var order_id = '<?php echo $order_id; ?>';
			var billing_first_name = '<?php echo $billing_first_name; ?>';
			var billing_last_name = '<?php echo $billing_last_name; ?>';
			var billing_email = '<?php echo $billing_email; ?>';
			var billing_phone = '<?php echo $billing_phone; ?>';
			var billing_address1 = '<?php echo $billing_address1; ?>';
			var billing_address2 = '<?php echo $billing_address2; ?>';

            radio.change(function(e) 
            {
                choice = this.value;
                if (choice === 'orders') 
                {
                    form.attr("action", "pay.php?checkout=manual&hidden_order_total="+order_total_amount+"&hidden_order_id="+order_id+"&hidden_first_name="+billing_first_name+"&hidden_last_name="+billing_last_name+"&hidden_email="+billing_email+"&hidden_phone="+billing_phone+"&hidden_address1="+billing_address1+"&hidden_address2="+billing_address2);
                } 
                else 
                {
                    form.attr("action", "pay.php?checkout=automatic&hidden_order_total="+order_total_amount+"&hidden_order_id="+order_id+"&hidden_first_name="+billing_first_name+"&hidden_last_name="+billing_last_name+"&hidden_email="+billing_email+"&hidden_phone="+billing_phone+"&hidden_address1="+billing_address1+"&hidden_address2="+billing_address2);
                }
            }); */
    });
    </script>

</body>

</html>