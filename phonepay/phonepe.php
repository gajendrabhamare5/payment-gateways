<?php

include('../../retail-admin/include/conn.php');

$oid = $_REQUEST['oid'];
$fetch_order_data = $conn->query("select order_total from order_master where order_id = '$oid' ");
$data_order = mysqli_fetch_array($fetch_order_data);
$order_total = $data_order['order_total'];
$redirectURL = $base_url.'payment/phonepay/redirect-url.php';
$callBackURL = $base_url;
$order_id = $order_prefix.''.$oid;

$apiKey = "6c48b179-c696-4930-9b04-f0d603ee22ea"; // sandbox or test APIKEY

$data = array(
    "merchantId" => "M22NMNW2XFA5Z",
    "merchantTransactionId" => $order_id,
    "merchantUserId" => "MUID12345",
    "amount" => $order_total * 100,
    "redirectUrl" => $redirectURL,
    "redirectMode" => "POST",
    "callbackUrl" => $redirectURL,
    "mobileNumber" => "9327230940",
    "paymentInstrument" => array(
        "type" => "PAY_PAGE"
    )
);
// Convert the Payload to JSON and encode as Base64
$payloadMain = base64_encode(json_encode($data));
$salt_index = 1; //key index 1
$payload = $payloadMain . "/pg/v1/pay" . $apiKey;
$sha256 = hash("sha256", $payload);
$final_x_header = $sha256 . '###' . $salt_index;


//X-VERIFY  -	SHA256(base64 encoded payload + "/pg/v1/pay" + salt key) + ### + salt index

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.phonepe.com/apis/hermes/pg/v1/pay",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode([
        'request' => $payloadMain
    ]),
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "X-VERIFY: " . $final_x_header,
        "accept: application/json"
    ],
]);

$response = curl_exec($curl);

$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
    /*   header('Location: paymentfailed.php?cURLError='.$err); */
} else {
    $responseData = json_decode($response, true);
    $url = $responseData['data']['instrumentResponse']['redirectInfo']['url'];
    /* echo $url;
        header('Location: '.$url); */
    echo '<script>window.location.href="' . $url . '"</script>';
}