<?php
function getItem($item_key, $item_type)
{
    $selectItem = "SELECT * from `" . $item_type . "` WHERE `key` ='" . $item_key . "'";
    $itemQuery = mysql_query($selectItem) or die("Could not process this query " . $selectItem);
    $num_rows = mysql_num_rows($itemQuery);

    if ($num_rows > 0) {
        while ($item = mysql_fetch_array($itemQuery)) {
            if ($item_type == 'surface') {
                return array(
                    'name' => $item['name'],
                    'price' => $item['price']
                );
            } else {
                return array(
                    'key' => $item['key'],
                    'name' => ucfirst($item['key']),
                    'price' => $item['price']
                );
            }
        }

    } else {
        return -1;
    }
}

function saveOrder($fbid, $item_key, $item_type, $acquisition, $origin = '')
{
    $ori = $acquisition;
    $acquisition = $acquisition == 'bought' ? null : ($acquisition == 'ia898dhjna00n' && $origin == 'fb' ? 'bought' : $acquisition);
    if (!$acquisition) {
        logToFile($fbid, 'saveOrder::Acquisition was ' . $ori . ', set to NULL. CHEAT RISK!!');
        return 'canceled';
    }
    $getId = "SELECT `id` from " . $item_type . " WHERE `key` = '" . $item_key . "'";
    $getIdQuery = mysql_query($getId);
    if ($getIdQuery === false) {
        logToFile($fbid, 'saveOrder::"Could not process this query ' . $getId);
        return 'canceled';
    }
    $item_id = -1;

    while ($item = mysql_fetch_array($getIdQuery)) {
        $item_id = $item['id'];
    }

    if ($item_id == -1) {
        logToFile($fbid, 'saveOrder::"could not get the ID ' . $item_id . ' query ' . $getId);
        return 'canceled';
    }

    $itemBought = checkItemBought($fbid, $item_id, $item_type);
    $updateItem = false;
    if ($itemBought) {
        if ($itemBought['acquisition'] == $acquisition) {
            if ($acquisition == 'granted') {
                return 'settled';
            } else {
                return 'canceled';
            }
        } else if ($acquisition == 'tested') {
            return 'settled';
        } else if ($acquisition == 'granted') {
            if ($itemBought['acquisition'] == 'tested') {
                $updateItem = true;
            } else {
                return 'settled';
            }
        } else if ($acquisition == 'bought') {
            $updateItem = true;
        }
    }
    if ($updateItem) {
        $insertOrder = "UPDATE `user_" . $item_type . "` SET `acquisition`='" . $acquisition . "' where `fbid`='.$fbid.';";
    } else {
        $insertOrder = "INSERT INTO `user_" . $item_type . "` (`fbid`, `sid`, `acquisition`) VALUES (" . $fbid . ", " . $item_id . ", '" . $acquisition . "');";
    }
    $insertOrderQuery = mysql_query($insertOrder);
    if ($insertOrderQuery === false) {
        logToFile($fbid, 'saveOrder::"Could not process this query ' . $insertOrder);
        return 'canceled';
    }
    return 'settled';
}

function saveOrderBrandConnect($fbid, $item_key, $item_type, $event_id)
{

    return $event_id . ':OK';
}

/*function saveOrderBrandConnect($fbid,$item_key,$event_id){
    $getId = "SELECT `id` from ".$item_type." WHERE `key` = '".$item_key."'";
    $getIdQuery = mysql_query($getId);
    $item_id = -1;
    $successResponse = $event_id.':OK';

    while ($item = mysql_fetch_array($getIdQuery)){
            $item_id = $item['id'];
    }
    if ($item_id == -1) {
        logToFile($fbid,'saveOrderBrandConnect::$item_key='.$item_key.':Can\'t find item_id. item_id='.$item_id);
        return $successResponse.':failed:saveOrderBrandConnect::$item_key '.$item_key.':Can\'t find item_id. item_id='.$item_id;
    }

    if (checkItemBought($fbid,$item_id,$item_type) > 0){
        return $successResponse.':canceled:checkItemBought true ';
    }

    $insertOrder = "INSERT INTO `user_".$item_type."` (`fbid`, `sid`, `acquisition`, `event_id`, `timestamp`) VALUES (".$fbid.", ".$item_id.", 'brandConnect', '".$event_id."', NOW());";
    $insertOrderQuery = mysql_query($insertOrder);
    if ($insertOrderQuery === false) {
        return $successResponse.':canceled: insert failed';
    }
    return $successResponse.':settled';
} */

function checkBrandConnectProcessed($eventId)
{
    /*ADAPT FOR TYPE!!! //TODO */
    $checkEventId = "SELECT `id` from user_surface WHERE `event_id` = '" . $eventId . "'";
    return singleResultAsArray($checkEventId) != null;
}

function checkItemBought($fbid, $item_id, $item_type)
{
    $checkBought = "SELECT * from user_" . $item_type . " WHERE `sid` ='" . $item_id . "' AND `fbid` ='" . $fbid . "'";
    return singleResultAsArray($checkBought);
}

function checkLastBoughtItem($item_type, $timestamp)
{
// 		$checkBought = "SELECT * from user_".$item_type." where `acquisition`='bought' and `timestamp` > '".$timestamp."'";
    $checkBought = "SELECT * from user_" . $item_type . " where `timestamp` > '" . $timestamp . "'";
    return resultsToArray($checkBought);
}

function saveNewPaymentRequest($fbid, $itemKey, $itemType)
{
    //generates unique request id (checks db for existing one)
    do {
        $requestId = sha1($itemKey . $itemType . time());

        $selectItem = "SELECT `request_id` from `payment_request` WHERE `request_id` = '" . $requestId . "'";
        $query = mysql_query($selectItem) or die("Could not process this query " . $selectItem);
        $num_rows = mysql_num_rows($query);
    } while ($num_rows > 0);

    $item = getItem($itemKey, $itemType);

    $insertOrder = "INSERT INTO `payment_request` (`request_id`, `fbid`, `item_key`, `item_type`, `price`)"
        . "VALUES ('" . $requestId . "', '" . $fbid . "', '" . $itemKey . "', '" . $itemType . "', '" . $item['price'] . "');";
    $insertOrderQuery = mysql_query($insertOrder);
    if ($insertOrderQuery === false) {
        logToFile($fbid, 'saveOrder::"Could not process this query ' . $insertOrder);
        return null;
    }

    return $requestId;
}

function getPaymentRequest($requestId) {
    $select = "SELECT * from `payment_request` WHERE `request_id` ='" . $requestId . "'";
    $query = mysql_query($select) or die("Could not process this query " . $select);
    $num_rows = mysql_num_rows($query);

    if ($num_rows > 0) {
         return mysql_fetch_array($query);
    } else {
        return -1;
    }
}

function deletePaymentRequest($requestId) {
    $delete = "DELETE from `payment_request` WHERE `request_id` ='" . $requestId . "'";
    $query = mysql_query($delete) or die("Could not process this query " . $delete);
}