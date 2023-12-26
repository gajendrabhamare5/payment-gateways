<html>

<head>

<title>CCAvenue Payment</title>

</head>

<body>

<center>



<?php

include "../../retail-admin/include/conn.php";

include('Crypto.php')?>

<?php 



 error_reporting(0);


	/* $merchant_data='2143779';

	$working_key='E14D97C564DE61E28B600D43C54A68F8';//Shared by CCAVENUES

	$access_code='AVGG28KC23AU36GGUA';//Shared by CCAVENUES */

	
	$merchant_data=$merchant_id;

	$working_key=$working_key;//Shared by CCAVENUES

	$access_code=$access_code;//Shared by CCAVENUES


	foreach ($_POST as $key => $value){

		$merchant_data.=$key.'='.$value.'&';

	}


	$encrypted_data=encrypt($merchant_data,$working_key); // Method for encrypting the data.



?>

<form method="post" name="redirect" action="https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction"> 

<?php

echo "<input type=hidden name=encRequest value=$encrypted_data>";

echo "<input type=hidden name=access_code value=$access_code>";

?>

</form>

</center>

<script anguage='javascript'>document.redirect.submit();</script>
l
</body>

</html>

