<?php

header('Content-type: text/html; charset=utf-8');

require 'functions.php';

$enableSandbox = false;

$paypalConfig = [
    'email' => 'linenlifell@gmail.com', // Электронная почта от Paypal
    'return_url' => 'https://linen-life.ru/ru/content15-uspeshnaya-oplata', // Страница с успешной транзакцией
    'cancel_url' => 'https://linen-life.ru/ru/content14-oshibka-oplaty', // Страница с не успешной транзакцией
    'notify_url' => 'https://linen-life.ru/orderform/paypal/payment.php' // Скрипт для обработки транзакции
];

$paypalUrl = $enableSandbox ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

$itemName = 'Оплата товара'; // Название товара

if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])) {

    $data = [];
    foreach ($_POST as $key => $value) {
        $data[$key] = stripslashes($value);
    }

    $data['business'] = $paypalConfig['email'];

    $data['return'] = stripslashes($paypalConfig['return_url']);
    $data['cancel_return'] = stripslashes($paypalConfig['cancel_url']);
    $data['notify_url'] = stripslashes($paypalConfig['notify_url']);

    $data['item_name'] = $itemName;
    $data['amount'] = (float) str_replace(',', '.', $data['amount']);

    $data['custom'] = json_encode(['phone' => $data['phone'], 'name' => $data['first_name']], JSON_UNESCAPED_UNICODE);


    $queryString = http_build_query($data);

    header('location:' . $paypalUrl . '?' . $queryString);
    exit();
} else {
    $user_data = json_decode($_POST['custom'], true);

    try {
        if (verifyTransaction($_POST)) {
            $to = 'alafabs@yandex.ru'; // Электронная почта на которую будет выслано уведомления
            $subject = 'Заявка на оплату с Формы'; //Загаловок сообщения
            $message = '
                    <html>
                        <head>
                            <title>' . $subject . '</title>
                        </head>
                        <body>
                            <p>Имя: ' . $user_data['name'] . '</p>
                            <p>Телефон: ' . $user_data['phone'] . '</p>                        
                            <p>Сумма оплаты: ' . $_POST['mc_gross'] . ' ' . $_POST['mc_currency'] . '</p>
                        </body>
                    </html>'; //Текст нащего сообщения можно использовать HTML теги
            $headers = "Content-type: text/html; charset=utf-8 \r\n"; //Кодировка письма
            $headers .= "From: Linen Life <ashamayshop@gmail.com>\r\n"; //Наименование и почта отправителя
            mail($to, $subject, $message, $headers);
        }
    } catch (Exception $e) {
        echo 'error' . $e->getMessage();
    }
}