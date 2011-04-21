<?php

class Tx_Ajaxlogin_Controller_UserController {
	/**
	 * @var Tx_Ajaxlogin_Domain_Model_User
	 */
	protected $user;
	
	public function __construct() {
		$this->user = t3lib_div::makeInstance('Tx_Ajaxlogin_Domain_Model_User', tslib_eidtools::initFeUser());
	}
	
	public function showAction() {
		if($this->user->isLoggedIn()) {
			$content = file_get_contents(t3lib_extMgm::extPath('ajaxlogin') . 'Resources/Private/Templates/User/show.html');
			
			$markers = array(
				'###FORMID###' => 'tx-ajaxlogin-' . time(),
				'###STATUS_HEADER###' => Tx_Ajaxlogin_Utility_Localization::translate('ll_status_header'),
				'###STATUS_MESSAGE###' => Tx_Ajaxlogin_Utility_Localization::translate('ll_status_message'),
				'###LOGOUT_LABEL###' => Tx_Ajaxlogin_Utility_Localization::translate('logout')
			);
		
			$result = array(
				'html' => t3lib_parsehtml::substituteMarkerArray($content, $markers)
			);
			
			$result['formid'] = $markers['###FORMID###'];
		} else {
			$result = null;
		}
		
		
		return $result;
	}
	
	public function checkStatusAction() {
		$result = array();
			
		if($this->user->isLoggedIn()) {
			$result['status'] = true;
			$result['statuslabel'] = Tx_Ajaxlogin_Utility_Localization::translate('logout');
		} else {
			$result['status'] = false;
			$result['statuslabel'] = Tx_Ajaxlogin_Utility_Localization::translate('login');
		}
		
		
		return $result;
	}
	
	public function loginAction() {
		$result = array();
			
		if($this->user->isLoggedIn()) {
			$result['status'] = true;
			$result['statuslabel'] = Tx_Ajaxlogin_Utility_Localization::translate('logout');
		} else {
			$result['status'] = false;
			$result['message'] = Tx_Ajaxlogin_Utility_Localization::translate('ll_error_message');
			$result['statuslabel'] = Tx_Ajaxlogin_Utility_Localization::translate('login');
		}
		
		
		return $result;
	}
	
	public function logoutAction() {
		$this->user->getUserObject()->logoff();
		
		return array(
			'status' => true,
			'statuslabel' => Tx_Ajaxlogin_Utility_Localization::translate('login')
		);
	}
}

?>