<?php 
$requestParameters = t3lib_div::_GP('tx_ajaxlogin');
$response = null;

tslib_eidtools::connectDB();

switch($requestParameters['controller']) {
	case 'LoginForm':
		$controller = t3lib_div::makeInstance('Tx_Ajaxlogin_Controller_LoginFormController');
	break;
	case 'User':
		$controller = t3lib_div::makeInstance('Tx_Ajaxlogin_Controller_UserController');
	break;
	default:
		$controller = null;
	break;
}

if(!empty($requestParameters['action']) && is_callable(array($controller, $requestParameters['action'] . 'Action'))) {
	$response = call_user_func(array($controller, $requestParameters['action'] . 'Action'));
}


header('Content-type: application/json; charset=utf-8');
echo json_encode(array(
	'Ajaxlogin' => $response
));
?>