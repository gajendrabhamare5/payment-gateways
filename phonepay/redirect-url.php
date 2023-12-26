<?php
include('../../retail-admin/include/conn.php');


$referenceId =  $_POST['providerReferenceId'];
$paymentCode =  $_POST['code'];
$merchantOrderId =  $_POST['merchantOrderId'];
$oid = trim($merchantOrderId,$order_prefix);
$where = '';
if($paymentCode == 'PAYMENT_SUCCESS'){
    $where = ", order_status = 12 ";
}
$conn->query("update order_master SET txn_id='$referenceId' $where , paymentStatus = '$paymentCode' where order_id = '$oid'  ");

?>
<html>
    <style>
        .aa{
            display: grid;
            text-align: center;
            width: 63%;
            margin: 0 auto;
            background-color: #d3d3d387;
            padding: 20px;;
        }
        img{
            width: 20%;
            margin: 0 auto;
        }
    </style>
    <div class="aa">
        <img src="<?php echo $logo; ?>" alt="">
        <h2>Order ID - <?php echo '#'.$merchantOrderId; ?></h2>
        <h2>Payment Status - <?php echo $paymentCode; ?></h2>
        <h2>Reference ID - <?php echo $referenceId; ?></h2>
        <a href="<?php echo $base_url; ?>">Back to website</a>
    </div>
</html>