<?php
require 'fbconfig.php';
require '../db/dbconnect.php';

if (!isset($_REQUEST['item_key']) || !isset($_REQUEST['item_type'])) {
    die('Item key and type required');
} else {
    $itemKey = $_REQUEST['item_key'];
    $itemType = $_REQUEST['item_type'];
}

$item_data = getItem($itemKey, $itemType);
if ($item_data != -1) {
    $itemTitle = stripslashes($item_data['name']);
    $itemPrice = $item_data['price'];
    $itemUrl = 'http://graffiti.mee-mail.com/images/' . $itemType . 's/' . $itemKey;
    if ($itemType == 'surface') {
        $itemUrl .= '/thumbnail_90.png';
    } else {
        $itemUrl .= '.png';
    }

    $itemObject =
'<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#">
    <meta property="og:type" content="og:product" />
    <meta property="og:title" content="'.$itemTitle.'" />
    <meta property="og:image" content="'.$itemUrl.'" />
    <meta property="og:description" content="'.$itemTitle.'" />
    <meta property="product:price:amount" content="'.$itemPrice.'"/>
    <meta property="product:price:currency" content="USD"/>
</head>';
    echo $itemObject;
} else {
    die('Item does not exist');
}