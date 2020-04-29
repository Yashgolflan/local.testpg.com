<?php

require('config.php');
require('razorpay-php/Razorpay.php');
session_start();

// Create the Razorpay Order

use Razorpay\Api\Api;

$api = new Api($keyId, $keySecret);

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
    'offer_id' => 'offer_EkISDxfRW18gtY',
];
}
else{
  $orderData = [
      'receipt'         => 3456,
      'amount'          => 2000 * 100, // 2000 rupees in paise
      'currency'        => 'INR',
      'payment_capture' => 1, // auto capture
      'offer_id' => 'offer_EkIX8NbX923Yf6',

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

require("checkout/{$checkout}.php");
