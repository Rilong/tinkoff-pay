<?php
session_start();

require 'TinkoffMerchantAPI.php';


define('TINKOFF_TERMINAL', '1563959455912');
define('TINKOFF_SECRET', 'ahl5wmikwhnoj0sj');

if ($_POST['name'] != '' || $_POST['phone'] != '' || $_POST['amount'] != '') {
    $api = new TinkoffMerchantAPI(TINKOFF_TERMINAL, TINKOFF_SECRET);
    $amount = floatval(trim(str_replace(' ', '', $_POST['amount'])));
    $args = array(
        'OrderId'         => uniqid(),
        'Amount'          => $amount * 100,
        'Description'     => 'Оплата товара',
        'SuccessURL'      => 'https://linen-life.ru/tinkoffpay/success.php',
        'FailURL'         => 'https://linen-life.ru/ru/content14-oshibka-oplaty',
        'DATA'            => array(
            'Name' => $_POST['name'],
            'Phone' => $_POST['phone'],
            'Amount' => $_POST['amount']
        )
    );
    $info = json_decode($api->init($args));
    $_SESSION['tinkoff']['order_id'] = $info->OrderId;
    $_SESSION['tinkoff']['name'] = $_POST['name'];
    $_SESSION['tinkoff']['phone'] = $_POST['phone'];
    $_SESSION['tinkoff']['amount'] = $_POST['amount'];

    if ($info->Success === true) {
        header('Location: ' . $info->PaymentURL);
    } else {
        header('Location: ' . $args['FailURL']);
    }

} else {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}