<?php

header('Content-Type: text/html; charset=UTF-8');

/**
 * API integration
 *
 * @link https://help.unitpay.ru/payments/create-payment
 */

require_once('./orderInfo.php');
require_once('../UnitPay.php');

$unitpay = new UnitPay($domain, $secretKey);

/**
 * Base params: account, desc, sum, currency, projectId, paymentType
 * Additional params:
 *  Qiwi, Mc:
 *      phone
 * alfaClick:
 *      clientId
 *
 * @link https://help.unitpay.ru/payments/create-payment
 * @link https://help.unitpay.ru/book-of-reference/payment-system-codes
 */
$response = $unitpay->api('initPayment', [
    'account' => $orderId,
    'desc' => $orderDesc,
    'sum' => $orderSum,
    'paymentType' => 'yandex',
    'currency' => $orderCurrency,
    'projectId' => $projectId,
]);

// If need user redirect on Payment Gate
if (isset($response->result->type)
    && $response->result->type === 'redirect') {
    // Url on PaymentGate
    $redirectUrl = $response->result->redirectUrl;
    // Payment ID in Unitpay (you can save it)
    $paymentId = $response->result->paymentId;
    // User redirect
    header("Location: " . $redirectUrl);

// If without redirect (invoice)
} elseif (isset($response->result->type)
    && $response->result->type === 'invoice') {
    // Url on receipt page in Unitpay
    $receiptUrl = $response->result->receiptUrl;
    // Payment ID in Unitpay (you can save it)
    $paymentId = $response->result->paymentId;
    // Invoice Id in Payment Gate (you can save it)
    $invoiceId = $response->result->invoiceId;
    // User redirect
    header("Location: " . $receiptUrl);

// If error during api request
} elseif (isset($response->error->message)) {
    $error = $response->error->message;
    print 'Error: '.$error;
}
