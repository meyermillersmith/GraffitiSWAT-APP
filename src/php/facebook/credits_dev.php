<?php
require 'fbconfig.php';
require '../db/dbconnect.php';

/**
* Copyright 2004-Present Facebook. All Rights Reserved. 
*
* You should reference http://developers.facebook.com/docs/credits/ as you
* familiarize yourself with callback.php. In particular, read all the steps
* under "Credits Tutorial" and "Credits Callback".
*
* Your application needs the following inputs and outputs
*
* @param int order_id
* @param string status
* @param string method
* @param array order_details (JSON-encoded)
*
* @return array A JSON-encoded array with order_id, next_state
* (optional: error code, comments)
*/

// Prepare the return data array
$data = array('content' => array());

// Parse the signed_request to verify it's from Facebook
$request = parse_signed_request($_REQUEST['signed_request'], $GLOBALS["secret"]);

if ($request == null) {
  die('Nice try, SUCKER!!');
  $json = '{
  		"credits":{
       "signed_request":"PfjakldsFWWs...",
       "order_details":{
          "order_id":9006136047935,
          "buyer":100002572844577,
          "app":128163550571392,
          "receiver":100002572844577,
          "amount":1,
          "time_placed":1320966823,
          "update_time":1320966825,
          "data":"",
          "items":[
             {
                "item_id":"london_sub",
                "title":"A Facebook Hat",
                "description":"The coolest hat youve ever seen.",
                "image_url":"http:\/\/www.facebook.com\/images\/gifts\/740.png",
                "product_url":"http:\/\/www.facebook.com\/images\/gifts\/740.png",
                "price":1,
                "data":""
             }
          ],
          "status":"placed"
       },
       "status":"placed",
       "order_id":"9006136047935",
       "method":"payments_status_update"
    }}';
  $request = json_decode($json, true);
}

// Grab the payload
$payload = $request['credits'];

// Retrieve all params passed in
$func = $_REQUEST['method'];
//$func = $payload['method'];
$order_id = $payload['order_id'];

 //TEST $func = 'payments_status_update';

if ($func == 'payments_status_update') {

  // Grab the order status
  $status = $payload['status'];
  //TEST $status = 'placed';

  // Write your apps logic here for validating and recording a
  // purchase here.
  // 
  // Generally you will want to move states from `placed` -> `settled`
  // here, then grant the purchasing user's in-game item to them.
  if ($status == 'placed') {
  	$orderDetails = json_decode($payload['order_details'], true);
    $fbid = $orderDetails['buyer'];
  	$items = $orderDetails['items'];
  	$item_info = $items[0]['item_id'];
  	$item_type = $items[0]['data'];
    $data['content']['status'] = saveOrder($fbid,$item_info,$item_type,'ia898dhjna00n','fb');
  }

  // Compose returning data array_change_key_case
  $data['content']['order_id'] = $order_id;

} else if ($func == 'payments_get_items') {
  // remove escape characters
  $order_info = stripcslashes($payload['order_info']);
  $item_info = json_decode($order_info, true);
  //TEST $item_info = 'lesstrain';
 $item_data = getItem($item_info['item_id'], $item_info['item_type']);
  if ($item_data != -1) {

    // Per the credits api documentation, you should pass in an item 
    // reference and then query your internal DB for the proper 
    // information. Then set the item information here to be 
    // returned to facebook then shown to the user for confirmation.
    $item['item_id'] = $item_info['item_id'];
    $item['data'] = $item_info['item_type'];
    $item['title'] = stripslashes($item_info['item_name']);
    $item['price'] = $item_data['price'];
    $item['description'] = $item['title'];
    $itemUrl = $item['image_url'] = 'http://projects.lessrain.net/public/graffiti/images/' . $item_info['item_type'] . 's/' . $item['item_id'];
    if ($item_info['item_type'] == 'surface'){
    	 $itemUrl.= '/thumbnail_90.png';
    } else {
    	$itemUrl.= '.png';
    }
    $item['image_url'] = $itemUrl;
    $item['product_url'] = $itemUrl;
  } else {
  
 	die('item does not exist');
    // For the sake of the sample, we will default to this item if 
    // the `order_info` reference passed from your JS call is not matched 
    // above.
    $item['title'] = 'A Facebook Hat';
    $item['price'] = 1;
    $item['description'] = 'The coolest hat you\'ve ever seen.';
    $item['image_url'] = 'http://www.facebook.com/images/gifts/740.png';
    $item['product_url'] = 'http://www.facebook.com/images/gifts/740.png';
  }

  // Put the associate array of item details in an array, and return in the
  // 'content' portion of the callback payload.
  $data['content'] = array($item);
}

// Required by api_fetch_response()
$data['method'] = $func;

// Send data back
echo json_encode($data);

// You can find the following functions and more details
// on http://developers.facebook.com/docs/authentication/canvas.
function parse_signed_request($signed_request, $app_secret) {
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

function base64_url_decode($input) {
  return base64_decode(strtr($input, '-_', '+/'));
}
?>