<?php
include('Crypto.php');
	include "../../retail-admin/include/conn.php";
	/* include "../../session.php"; */
	/* include "../../retail-admin/mail_function.php"; */
	error_reporting(0);


		/* $uid = $_SESSION['UserID']; */

	$workingKey=$working_key;		//Working Key should be provided here.
	$encResponse=$_POST["encResp"];			//This is the response sent by the CCAvenue Server
	$rcvdString=decrypt($encResponse,$workingKey);		//Crypto Decryption used as per the specified working key.
	$order_status="";
	$decryptValues=explode('&', $rcvdString);
	$dataSize=sizeof($decryptValues);
	echo "<center>";

	for($i = 0; $i < $dataSize; $i++)
	{
		$information=explode('=',$decryptValues[$i]);
		if($i==0){
				$order_id =$information[1];
		}
		if($i==1){
				$transaction_id =$information[1];
		}
		if($i==3)	$order_status=$information[1];
	}

	/* $orderquery=$conn->query("select * from order_master where user_id='$uid' AND order_id='$order_id'"); */
	$orderquery=$conn->query("select * from order_master where order_id='$order_id'");
	$orderdata=mysqli_fetch_assoc($orderquery);
	$order_total_amount=$orderdata['order_total'];
	$uid=$orderdata['user_id'];
	session_start();
	$_SESSION['Order_id'] = $order_id;
	$fetchquery=$conn->query("select * from address_master where user_id='$uid' AND type='1'");
	$fetchcount=mysqli_num_rows($fetchquery);
	$fetchdata=mysqli_fetch_assoc($fetchquery);

	$billing_phone=$fetchdata['phone_no'];

	$billing_email=$fetchdata['email_id'];
	$billing_first_name=$fetchdata['first_name'];
	$billing_last_name=$fetchdata['last_name'];

	$datee=date("Y-m-d");
	$conn->query("INSERT INTO ccavenu_response(`order_id`,`user_id`,`remark`,`Date`) values('$order_id','$uid','$rcvdString','$datee')");
	/* $conn->query("update user_master_wholesale set guest_id='' where `user_id`='$uid'");
	 */
	if($order_status==="Success")
	{
		echo "<h3>Thank You. Your order status is ".$order_status."</h3><br>
			<p>Your Transaction ID for this transaction is ".$transaction_id.".</p><br>
			<p>We have received a payment of Rs. ".$order_total_amount." Your order will soon be shipped.</p><br>
			<p style='text-align: center;'><a  href='".$base_url."order_details/".$order_id."/thank_you' style='color:blue;'> Back to Website</a></p>";

		 $conn->query("update order_master set order_status='6',txn_id='$transaction_id' where order_id='$order_id'");


			$q = $conn->query("delete from `cart_master` where `UserID`=$uid");
			send_mail($order_id);



	}
	else if($order_status==="Aborted")
	{
		echo "<h3>Your order status is ".$order_status."</h3><br>
			<p>Your transaction id for this transaction is ".$transaction_id.".</p><br>
			<p style='text-align: center;'><a href='".$base_url."order_details/".$order_id."/thank_you' style='color:blue;'> Back to Website</a></p>";

		 $conn->query("update order_master set order_status='4',txn_id='$transaction_id' where order_id='$order_id'");
			   send_mail($order_id);

	}
	else if($order_status==="Failure")
	{
		echo "<h3>Your order status is ".$order_status."</h3><br>
			<p>Your transaction id for this transaction is ".$transaction_id.".</p><br>
			<p style='text-align: center;'><a href='".$base_url."order_details/".$order_id."/thank_you' style='color:blue;'> Back to Website</a></p>";

		 $conn->query("update order_master set order_status='4',txn_id='$transaction_id' where order_id='$order_id'");
			   send_mail($order_id);
	}
	else
	{
		echo "<h3>Your order status is ".$order_status."</h3><br>
			<p>Your transaction id for this transaction is ".$transaction_id.".</p><br>
			<p style='text-align: center;'><a href='".$base_url."order_details/".$order_id."/thank_you' style='color:blue;'> Back to Website</a></p>";

		 $conn->query("update order_master set order_status='4',txn_id='$transaction_id' where order_id='$order_id'");
			   send_mail($order_id);
	}

	//echo "<br><br>";

	//echo "<table cellspacing=4 cellpadding=4>";
	//for($i = 0; $i < $dataSize; $i++)
	//{
	//	$information=explode('=',$decryptValues[$i]);
	//    	echo '<tr><td>'.$information[0].'</td><td>'.$information[1].'</td></tr>';
	//}

	//echo "</table><br>";
	echo "</center>";

?>
