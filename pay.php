<?php

require('config.php');
require('razorpay-php/Razorpay.php');
session_start();

// Create the Razorpay Order

use Razorpay\Api\Api;

$api = new Api($keyId, $keySecret);
//$payment = $api->payment->fetch('pay_EkJgBUkJfXnxAw');//fetch payment through id;
//echo(serialize($payment['order_id']));//fetch orderid from above array
// echo(serialize($api->card->fetch($payment['card_id'])));//last 4 digits of card /details
//echo(serialize($api->order->fetch($payment['order_id'])));
//
// We create an razorpay order using orders api
// Docs: https://docs.razorpay.com/docs/orders
//
if($_GET['checkout']=='automatic'){
$orderData = [
    'receipt'         => 3456,
    'amount'          => 2 * 100, // 2000 rupees in paise
    'currency'        => 'INR',
    'payment_capture' => 1, // auto capture
  //  'offer_id' => 'offer_EkISDxfRW18gtY',
];
}
else{
  $orderData = [
      'receipt'         => 3456,
      'amount'          => 2000 * 100, // 2000 rupees in paise
      'currency'        => 'INR',
      'payment_capture' => 1, // auto capture
    //  'offer_id' => 'offer_EkIX8NbX923Yf6',

  ];
}

$razorpayOrder = $api->order->create($orderData);

$razorpayOrderId = $razorpayOrder['id'];

$_SESSION['razorpay_order_id'] = $razorpayOrderId;

$displayAmount = $amount = $orderData['amount'];

if ($displayCurrency !== 'INR')
{
    $url = "https://api.fixer.io/latest?symbols=$displayCurrency&base=INR";
    $exchange = json_decode(file_get_contents($url), true);

    $displayAmount = $exchange['rates'][$displayCurrency] * $amount / 100;
}

$checkout = 'automatic';

if (isset($_GET['checkout']) and in_array($_GET['checkout'], ['automatic', 'manual'], true))
{
    $checkout = $_GET['checkout'];
}
if($checkout=="automatic"){
$data = [
    "key"               => $keyId,
    "amount"            => $amount,
    "name"              => "Golflan",
    "description"       => "Testing",
    "image"             => "https://s29.postimg.org/r6dj1g85z/daft_punk.jpg",
    "prefill"           => [
    "name"              => "Test Person",
    "email"             => "customer@merchant.com",
    "contact"           => "9999999999",
    ],
    "notes"             => [
    "address"           => "Hello World",
    "merchant_order_id" => "12312321",
    ],
    "theme"             => [
    "color"             => "#F37254"
    ],
    "order_id"          => $razorpayOrderId,
    "method" => [
    "netbanking" => "false",
    "card"=> "true",
    "wallet" => "false",
    "upi" => "false",
    "emi" => "false"
  ]


];
}
else{
  $data = [
      "key"               => $keyId,
      "amount"            => $amount,
      "name"              => "Golflan",
      "description"       => "Testing",
      "image"             => "https://s29.postimg.org/r6dj1g85z/daft_punk.jpg",
      "prefill"           => [
      "name"              => "Test Person",
      "email"             => "customer@merchant.com",
      "contact"           => "9999999999",
      ],
      "notes"             => [
      "address"           => "Hello World",
      "merchant_order_id" => "12312321",
      ],
      "theme"             => [
      "color"             => "#F37254"
      ],

      "order_id"          => $razorpayOrderId,

      "method" => [
      "netbanking" => "false",
      "card"=> "true",
      "wallet" => "false",
      "upi" => "false",
      "emi" => "false"
    ]

  ];
}

if ($displayCurrency !== 'INR')
{
    $data['display_currency']  = $displayCurrency;
    $data['display_amount']    = $displayAmount;
}

$json = json_encode($data);
$client = new http\Client;
$request = new http\Client\Request;
$request->setRequestUrl('https://api.razorpay.com/v1/payments/create/redirect');
$request->setRequestMethod('POST');
$body = new http\Message\Body;
$body->append('{
"amount": 100,
"currency": "INR",
"contact": 8888888888,
"email": "gaurav@gmail.com",
"order_id": "order_4xbQrmEoA5WJ0G",
"method": "card",
"card":{
"number": "4111111111111111",
"name": "Gaurav",
"expiry_month": 11,
"expiry_year": 23,
"cvv": 100
}
}');
$request->setBody($body);
$request->setOptions(array());
$request->setHeaders(array(
'Authorization' => 'Basic cnpwX3Rlc3RfRFNmQWFaQW5YYVdwMGo6Ukd1WjEzamZOQzk4cm83eWgwTXBuaWxq',
'Content-Type' => 'application/json'
));
$client->enqueue($request)->send();
$response = $client->getResponse();
echo $response->getBody();

//require("checkout/{$checkout}.php");
?>
