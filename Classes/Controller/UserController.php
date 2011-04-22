<?php
// for now we pretend extending felogin_pi1 to use some hooks
require_once(t3lib_extMgm::extPath('felogin') . 'pi1/class.tx_felogin_pi1.php');
class Tx_Ajaxlogin_Controller_UserController extends tx_felogin_pi1{
	/**
	 * @var Tx_Ajaxlogin_Domain_Model_User
	 */
	protected $user;
	
	/**
	 * @var Tx_Ajaxlogin_Domain_Repository_UserRepository
	 */
	protected $userRepository;
	
	public function __construct() {
		$this->user = t3lib_div::makeInstance('Tx_Ajaxlogin_Domain_Model_User', tslib_eidtools::initFeUser());
		$this->userRepository = t3lib_div::makeInstance('Tx_Ajaxlogin_Domain_Repository_UserRepository');
	}
	
	public function showAction() {
		if($this->user->isLoggedIn()) {
			$setup = Tx_Ajaxlogin_Utility_TypoScript::parse(Tx_Ajaxlogin_Utility_TypoScript::getSetup());
			$content = file_get_contents(t3lib_extMgm::extPath('ajaxlogin') . 'Resources/Private/Templates/User/show.html');
			
			$link = $GLOBALS['TSFE']->tmpl->linkData(
				array('uid' => $setup['profilePid']), // trick the ->linkData() method by sending a simple array with the uid instead of a complete page record
				'',
				false,
				'',
				array(),
				''
			);
			
			$markers = array(
				'###FORMID###' => 'tx-ajaxlogin-' . time(),
				'###STATUS_HEADER###' => Tx_Ajaxlogin_Utility_Localization::translate('ll_status_header'),
				'###STATUS_MESSAGE###' => Tx_Ajaxlogin_Utility_Localization::translate('ll_status_message'),
				'###LOGOUT_LABEL###' => Tx_Ajaxlogin_Utility_Localization::translate('logout'),
				'###USERNAME###' => $this->user->getUsername(),
				'###USER_FULLNAME###' => $this->user->getName(),
				'###PROFILE_URL###' => t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $link['totalURL'],
				'###PROFILE_LABEL###' => 'Manage your account'
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
			$result['statuslabel'] = Tx_Ajaxlogin_Utility_Localization::translate('status-loggedin');
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
			$result['statuslabel'] = Tx_Ajaxlogin_Utility_Localization::translate('status-loggedin');
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
	
	public function newAction() {
		$result = array();
		
		$content = file_get_contents(t3lib_extMgm::extPath('ajaxlogin') . 'Resources/Private/Templates/User/new.html');
			
		$markers = array(
			'###FORMID###' => 'tx-ajaxlogin-' . time(),
			'###HEADER###' => Tx_Ajaxlogin_Utility_Localization::translate('signup'),
			'###MESSAGE###' => Tx_Ajaxlogin_Utility_Localization::translate('ll_status_message'),
			'###SIGNUP_LABEL###' => Tx_Ajaxlogin_Utility_Localization::translate('signup'),
			'###NAME_LABEL###' => 'Name:',
			'###USERNAME_LABEL###' => Tx_Ajaxlogin_Utility_Localization::translate('username'),
			'###EMAIL_LABEL###' => Tx_Ajaxlogin_Utility_Localization::translate('your_email'),
			'###PASSWORD_LABEL1###' => Tx_Ajaxlogin_Utility_Localization::translate('newpassword_label1'),
			'###PASSWORD_LABEL2###' => Tx_Ajaxlogin_Utility_Localization::translate('newpassword_label2'),
			'###RETURNID###' => 'tx-ajaxlogin-return-' . time(),
			'###RETURN_LABEL###' => Tx_Ajaxlogin_Utility_Localization::translate('ll_forgot_header_backToLogin'),
		);
	
		$result['html'] = t3lib_parsehtml::substituteMarkerArray($content, $markers);
		
		$result['formid'] = $markers['###FORMID###'];
		$result['returnid'] = $markers['###RETURNID###'];
		
		return $result;
	}
	
	public function editAction() {
		$content = file_get_contents(t3lib_extMgm::extPath('ajaxlogin') . 'Resources/Private/Templates/User/edit.html');
			
		$markers = array(
			'###FORMID###' => 'tx-ajaxlogin-' . time(),
			'###HEADER###' => Tx_Ajaxlogin_Utility_Localization::translate('signup'),
			'###MESSAGE###' => Tx_Ajaxlogin_Utility_Localization::translate('ll_status_message'),
			'###SIGNUP_LABEL###' => Tx_Ajaxlogin_Utility_Localization::translate('signup'),
			'###NAME_LABEL###' => 'Name:',
			'###USERNAME_LABEL###' => Tx_Ajaxlogin_Utility_Localization::translate('username'),
			'###EMAIL_LABEL###' => Tx_Ajaxlogin_Utility_Localization::translate('your_email'),
			'###PASSWORD_LABEL1###' => Tx_Ajaxlogin_Utility_Localization::translate('newpassword_label1'),
			'###PASSWORD_LABEL2###' => Tx_Ajaxlogin_Utility_Localization::translate('newpassword_label2'),
			'###RETURNID###' => 'tx-ajaxlogin-return-' . time(),
			'###RETURN_LABEL###' => Tx_Ajaxlogin_Utility_Localization::translate('ll_forgot_header_backToLogin'),
		);
	
		return t3lib_parsehtml::substituteMarkerArray($content, $markers);
	}
	
	public function createAction() {
		$setup = Tx_Ajaxlogin_Utility_TypoScript::parse(Tx_Ajaxlogin_Utility_TypoScript::getSetup());
		$data = t3lib_div::_GP('tx_ajaxlogin');
		$result = array();
		
		$result['status'] = false;
		
		// do some validation
		if(empty($data['name'])) {
			$result['message'] = 'No name given';
			return $result;
		}
		if(empty($data['username'])) {
			$result['message'] = 'No username given';
			return $result;
		}
		if(!t3lib_div::validEmail($data['email'])) {
			$result['message'] = 'No valid e-mailaddress given';
			return $result;
		}
		if($data['pass1'] != $data['pass1']) {
			$result['message'] = Tx_Ajaxlogin_Utility_Localization::translate('ll_change_password_notequal_message');
			return $result; // return because we don't need to do anything more
		}		
		if(strlen($data['pass1']) < $setup['minimumPasswordLength']) {
			$result['message'] = Tx_Ajaxlogin_Utility_Localization::translate('ll_change_password_tooshort_message');
			return $result; // return because we don't need to do anything more
		}
		
		// make sure another user with this name doesn't already exists
		$user = $this->userRepository->findOneByUsernameOrEmail($data['username']);
		
		if($user) {
			$result['message'] = 'The chosen username is already in use';
			return $result;
		}
		
		$newPass = $data['pass1'];

		if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['felogin']['password_changed']) {
			$_params = array(
				'user' => $user,
				'newPassword' => $newPass,
			);
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['felogin']['password_changed'] as $_funcRef) {
				if ($_funcRef) {
					t3lib_div::callUserFunction($_funcRef, $_params, $this);
				}
			}
			$newPass = $_params['newPassword'];
		}
		
		$this->userRepository->insert(array(
			'username' => $data['username'],
			'name' => $data['name'],
			'email' => $data['email'],
			'password' => $newPass,
			'pid' => $setup['storagePid']
		));
		
		$result['status'] = true;
		$result['message'] = Tx_Ajaxlogin_Utility_Localization::translate('ll_change_password_done_message');
		
		return $result;
	}
}

?>