<?php

class Tx_Ajaxlogin_Bootstrap {
	/**
	 * @var array
	 */
	public static $settings;
	
	/**
	 * @var tslib_cObj
	 */
	public static $contentObject;
	
	public function run($content, $conf) {
		$requestParameters = t3lib_div::_GP('tx_ajaxlogin');
		
		self::$settings = $conf['settings.'];
		self::$contentObject = t3lib_div::makeInstance('tslib_cObj');
		
		if(!empty($requestParameters['forgot'])) {
			$passwordController = t3lib_div::makeInstance('Tx_Ajaxlogin_Controller_PasswordController');			
			$content = $passwordController->changeAction($requestParameters['forgot']);
		} else if(!empty($GLOBALS['TSFE']->fe_user->user)) {
			$userController = t3lib_div::makeInstance('Tx_Ajaxlogin_Controller_UserController');
			$content = $userController->editAction();
		} else {
			$content = 'can\'t help you';
		}
		
		return $content;
	}
}

?>