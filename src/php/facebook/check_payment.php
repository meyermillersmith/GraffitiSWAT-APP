<?php
require 'fbconfig.php';
require '../db/dbconnect.php';

if (!isset($_REQUEST['signed_request'])) {
    die('Signed request required');
} else {
    $request = parse_signed_request($_REQUEST['signed_request'], $GLOBALS["secret"]);
    if ($request == null) {
        die('Nice try, SUCKER!!');
    }

    $status = $request['status'];

    if ($status == 'completed') {
        $requestFromDb = getPaymentRequest($request['request_id']);
        if ($request['amount'] == $requestFromDb['price']) {
            saveOrder($requestFromDb['fbid'], $requestFromDb['item_key'], $requestFromDb['item_type'], 'ia898dhjna00n', 'fb');
            deletePaymentRequest($request['request_id']);
        } else {
            $status = 'failed';
        }
    }

    echo json_encode(array('status' => $status));
}

function parse_signed_request($signed_request, $app_secret)
{
    list($encoded_sig, $payload) = explode('.', $signed_request, 2);

    // Decode the data
    $sig = base64_url_decode($encoded_sig);
    $data = json_decode(base64_url_decode($payload), true);

    if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
        error_log('Unknown algorithm. Expected HMAC-SHA256');
        return null;
    }

    // Check signature
    $expected_sig = hash_hmac('sha256', $payload, $app_secret, $raw = true);
    if ($sig !== $expected_sig) {
        error_log('Bad Signed JSON signature!');
        return null;
    }

    return $data;
}

function base64_url_decode($input)
{
    return base64_decode(strtr($input, '-_', '+/'));
}