<?php

class Tx_Ajaxlogin_Controller_LoginFormController {
	public function showAction() {
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$result = array();
		
		$content = file_get_contents(t3lib_extMgm::extPath('ajaxlogin') . 'Resources/Private/Templates/LoginForm/show.html');
		
		$markers = array(
			'###FORMID###' => 'tx-ajaxlogin-' . time(),
			'###HIDDENFIELDS###' => '',
			'###USERNAME_LABEL###' => Tx_Ajaxlogin_Utility_Localization::translate('username'),
			'###PASSWORD_LABEL###' => Tx_Ajaxlogin_Utility_Localization::translate('password'),
			'###LOGIN_LABEL###' => Tx_Ajaxlogin_Utility_Localization::translate('login'),
			'###FORGOT_LABEL###' => Tx_Ajaxlogin_Utility_Localization::translate('ll_forgot_header'),
			'###FORGOTID###' => 'tx-ajaxlogin-forgot-' . time(),
			'###SIGNUP_LABEL###' => Tx_Ajaxlogin_Utility_Localization::translate('signup'),
			'###SIGNUPID###' => 'tx-ajaxlogin-signup-' . time()
		);
		
		$result['formid'] = $markers['###FORMID###'];
		$result['forgotid'] = $markers['###FORGOTID###'];
		$result['signupid'] = $markers['###SIGNUPID###'];
		
		if ($GLOBALS['TYPO3_CONF_VARS']['FE']['loginSecurityLevel'] == 'rsa') {
			$result['RSA'] = true;
			
			$RSAKeyController = t3lib_div::makeInstance('Tx_Ajaxlogin_Controller_RSAKeyController');
			$result['RSAKey'] = $RSAKeyController->createAction();
			
			$javascriptPath = t3lib_extMgm::siteRelPath('rsaauth') . 'resources/';
				$files = array(
					'jsbn/jsbn.js',
					'jsbn/prng4.js',
					'jsbn/rng.js',
					'jsbn/rsa.js',
					'jsbn/base64.js'
				);

			foreach ($files as $file) {
				$markers['###HIDDENFIELDS###'] .= '<script type="text/javascript" src="' .
					t3lib_div::getIndpEnv('TYPO3_SITE_URL') .
					$javascriptPath . $file . '"></script>';
			}
		}
		
		$result['html'] = t3lib_parsehtml::substituteMarkerArray($content, $markers);
		
		return $result;
	}
}

?>