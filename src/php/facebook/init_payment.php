<?php
require 'fbconfig.php';
require '../db/dbconnect.php';

if (!isset($_REQUEST['item_key']) || !isset($_REQUEST['item_type']) || !isset($_REQUEST['fbid'])) {
    die('Item key and type required');
} else {
    $itemKey = $_REQUEST['item_key'];
    $itemType = $_REQUEST['item_type'];
    $fbid = $_REQUEST['fbid'];
}

$requestId = saveNewPaymentRequest($fbid, $itemKey, $itemType);
echo json_encode(array('request_id' => $requestId));