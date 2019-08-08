<?php
session_start();

$to = 'alafabs@yandex.ru';// Электронная почта на которую будет выслано уведомления
$subject = 'Заявка на оплату с Формы'; //Загаловок сообщения
$message = '
                    <html>
                        <head>
                            <title>' . $subject . '</title>
                        </head>
                        <body>
                            <p>Номер заказа: ' . $_SESSION['tinkoff']['order_id'] . '</p>
                            <p>Имя: ' . $_SESSION['tinkoff']['name'] . '</p>
                            <p>Телефон: ' . $_SESSION['tinkoff']['phone'] . '</p>                        
                            <p>Сумма оплаты: ' . $_SESSION['tinkoff']['amount'] . ' руб.</p>
                        </body>
                    </html>'; //Текст нащего сообщения можно использовать HTML теги
$headers = "Content-type: text/html; charset=utf-8 \r\n"; //Кодировка письма
$headers .= "From: Linen Life <ashamayshop@gmail.com>\r\n"; //Наименование и почта отправителя
mail($to, $subject, $message, $headers);

unset($_SESSION['tinkoff']);

header('Location: https://linen-life.ru/ru/content15-uspeshnaya-oplata');