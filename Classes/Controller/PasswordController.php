<?php
// for now we pretend extending felogin_pi1 to use some hooks
require_once(t3lib_extMgm::extPath('felogin') . 'pi1/class.tx_felogin_pi1.php');
class Tx_Ajaxlogin_Controller_PasswordController extends tx_felogin_pi1 {
	
	/**
	 * @var Tx_Ajaxlogin_Domain_Repository_UserRepository
	 */
	protected $userRepository;
	
	public function __construct() {
		$this->userRepository = t3lib_div::makeInstance('Tx_Ajaxlogin_Domain_Repository_UserRepository');
	}
	
	public function forgotAction() {
		$result = array();
		
		$content = file_get_contents(t3lib_extMgm::extPath('ajaxlogin') . 'Resources/Private/Templates/Password/forgot.html');
		
		$markers = array(
			'###FORMID###' => 'tx-ajaxlogin-' . time(),
			'###HEADER###' => Tx_Ajaxlogin_Utility_Localization::translate('ll_forgot_header'),
			'###MESSAGE###' => Tx_Ajaxlogin_Utility_Localization::translate('ll_forgot_reset_message'),
			'###RETURNID###' => 'tx-ajaxlogin-return-' . time(),
			'###RETURN_LABEL###' => Tx_Ajaxlogin_Utility_Localization::translate('ll_forgot_header_backToLogin'),
			'###DATA_LABEL###' => Tx_Ajaxlogin_Utility_Localization::translate('ll_enter_your_data'),
			'###SEND_PASSWORD###' => Tx_Ajaxlogin_Utility_Localization::translate('reset_password')
		);
	
		$result['html'] = t3lib_parsehtml::substituteMarkerArray($content, $markers);
		
		$result['formid'] = $markers['###FORMID###'];
		$result['returnid'] = $markers['###RETURNID###'];
		
		return $result;
	}
	
	public function resetAction() {
		$data = t3lib_div::_GP('tx_ajaxlogin');
		$result = array();
		
		$user = $this->userRepository->findOneByUsernameOrEmail($data['email']);
			
		if($user) {
			$setup = Tx_Ajaxlogin_Utility_TypoScript::parse(Tx_Ajaxlogin_Utility_TypoScript::getSetup());
			
			$hash = md5(t3lib_div::generateRandomBytes(64));
			$validEnd = time() + 3600 * intval($setup['resetLinkValidHours']);
			
			$link = $GLOBALS['TSFE']->tmpl->linkData(
				array('uid' => $setup['profilePid']), // trick the ->linkData() method by sending a simple array with the uid instead of a complete page record
				'',
				false,
				'',
				array(),
				t3lib_div::implodeArrayForUrl('tx_ajaxlogin', array(
					'forgot' => $hash
				))
			);
			
			Tx_Ajaxlogin_Utility_NotifyMail::send(
				$user['email'], 
				Tx_Ajaxlogin_Utility_Localization::translate('ll_forgot_validate_reset_password_subject'), 
				Tx_Ajaxlogin_Utility_Localization::translate('ll_forgot_validate_reset_password', 'ajaxlogin', array(
					$user['name'],
					t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $link['totalURL'],
					strftime($setup['resetLinkValidStrftimeFormat'], $validEnd)
				))
			);
			
			$this->userRepository->update($user['uid'], array(
				'tx_ajaxlogin_forgotHash' => $hash,
				'tx_ajaxlogin_forgotHashValid' => $validEnd
			));

			$result['status'] = true;
			$result['message'] = Tx_Ajaxlogin_Utility_Localization::translate('ll_forgot_reset_message_emailSent', 'ajaxlogin', array($data['email']));
		} else {
			$result['status'] = false;
			$result['message'] = Tx_Ajaxlogin_Utility_Localization::translate('ll_forgot_email_nopassword');
			
			$result['debug'] = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['felogin']['password_changed'];
		}
		
		
		return $result;
	}
	
	public function saveAction() {
		$setup = Tx_Ajaxlogin_Utility_TypoScript::parse(Tx_Ajaxlogin_Utility_TypoScript::getSetup());
		$data = t3lib_div::_GP('tx_ajaxlogin');
		$result = array();
		
		if($data['np1'] != $data['np2']) {
			$result['status'] = false;
			$result['message'] = Tx_Ajaxlogin_Utility_Localization::translate('ll_change_password_notequal_message');
			return $result; // return because we don't need to do anything more
		}
		
		if(strlen($data['np1']) < $setup['minimumPasswordLength']) {
			$result['status'] = false;
			$result['message'] = Tx_Ajaxlogin_Utility_Localization::translate('ll_change_password_tooshort_message');
			return $result; // return because we don't need to do anything more
		}
		
		$user = $this->userRepository->findOneByForgotHash($data['forgot']);
			
		if($user) {
			$newPass = $data['np1'];

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
			
			$this->userRepository->update($user['uid'], array(
				'password' => $newPass,
				'tx_ajaxlogin_forgotHash' => '',
				'tx_ajaxlogin_forgotHashValid' => 0
			));
			
			$result['status'] = true;
			$result['message'] = Tx_Ajaxlogin_Utility_Localization::translate('ll_change_password_done_message');
		} else {
			$result['status'] = false;
			$result['message'] = Tx_Ajaxlogin_Utility_Localization::translate('ll_change_password_notvalid_message');
		}
		
		
		return $result;
	}
	
	public function changeAction($forgotHash) {		
		$content = file_get_contents(t3lib_extMgm::extPath('ajaxlogin') . 'Resources/Private/Templates/Password/change.html');
		
		$user = $this->userRepository->findOneByForgotHash($forgotHash);
		if(!$user) {
			return Tx_Ajaxlogin_Utility_Localization::translate('ll_change_password_notvalid_message');
		}
		
		$markers = array(
			'###FORMID###' => 'tx-ajaxlogin-password-change' . time(),
			'###HEADER###' => Tx_Ajaxlogin_Utility_Localization::translate('ll_change_password_header'),
			'###MESSAGE###' => Tx_Ajaxlogin_Utility_Localization::translate('ll_change_password_message', 'ajaxlogin', array(
				Tx_Ajaxlogin_Bootstrap::$contentObject->cObjGetSingle(Tx_Ajaxlogin_Bootstrap::$settings['minimumPasswordLength'], Tx_Ajaxlogin_Bootstrap::$settings['minimumPasswordLength.'])
			)),
			'###NEWPASS_LABEL1###' => Tx_Ajaxlogin_Utility_Localization::translate('newpassword_label1'),
			'###NEWPASS_LABEL2###' => Tx_Ajaxlogin_Utility_Localization::translate('newpassword_label2'),
			'###SEND_PASSWORD###' => Tx_Ajaxlogin_Utility_Localization::translate('change_password'),
			'###FORGOT_HASH###' => $forgotHash
		);
	
		return t3lib_parsehtml::substituteMarkerArray($content, $markers);
	}
}

?>