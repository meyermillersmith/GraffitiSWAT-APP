<?php
//ini_set('display_errors', 1);
error_reporting(E_ALL);
$using_error_handler = true;
// error handler function
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
	
	global $connection, $user_profile;
    /*if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }*/
    
    $error_details = "";

    switch ($errno) {
    case E_USER_ERROR:
    case E_COMPILE_ERROR :
    case E_ERROR:
        $error_details .= "ERROR [$errno] $errstr";
        $error_details .= "  Fatal error on line $errline in file $errfile";
    	logError($error_details);
        exit(1);
        break;

//     case E_USER_WARNING:
//     case E_USER_NOTICE:
//     case E_NOTICE:
//     case E_WARNING:
//     case E_COMPILE_WARNING:
//     case E_CORE_WARNING:
//         break;

    default:
//         $error_details .= "Unknown error type: [$errno] $errstr";
//         $error_details .= " on line $errline in file $errfile";
//     	logError($error_details);
        break;
    }

    /* Don't execute PHP internal error handler */
    return false;
}
set_error_handler("myErrorHandler");

function myExceptionHandler($exception) {
    logError("EXCEPTION " . $exception->getMessage());
}

set_exception_handler('myExceptionHandler');

function myFatalHandler() {
	global $connection, $user_profile;
	$errfile = "unknown file";
	$errstr  = "shutdown";
	$errno   = E_CORE_ERROR;
	$errline = 0;

	$error = error_get_last();

	if( $error !== NULL) {
		$errno   = $error["type"];
		$errfile = $error["file"];
		$errline = $error["line"];
		$errstr  = $error["message"];
	}
	
	myErrorHandler($errno, $errstr, $errfile, $errline);
// 	$error_details = "FATAL ERROR [$errno] $errstr";
// 	$error_details .= "  Fatal error on line $errline in file $errfile";

// 	logError( $error_details );
}

register_shutdown_function( "myFatalHandler" );

function logError($error_details){
	global $connection, $user_profile;
    if (!isset($connection)) {
    	require dirname(__FILE__).'/../../php/db/dbconnect.php';
    }
    logToFile($user_profile['id'],$error_details);
}

?>