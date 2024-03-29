<?php
class Tx_Ajaxlogin_Controller_UserController extends Tx_Extbase_MVC_Controller_ActionController {
	
	/**
	 * @var Tx_Ajaxlogin_Domain_Repository_UserRepository
	 */
	protected $userRepository;	
	
	/**
	 * @var Tx_Ajaxlogin_Domain_Repository_UserGroupRepository
	 */
	protected $userGroupRepository;	
	
	public function initializeAction() {
		$this->userRepository = t3lib_div::makeInstance('Tx_Ajaxlogin_Domain_Repository_UserRepository');
		$this->userGroupRepository = t3lib_div::makeInstance('Tx_Ajaxlogin_Domain_Repository_UserGroupRepository');
	}
	
	public function infoAction() {		
		$user = $this->userRepository->findCurrent();
		
		if(!is_null($user)) {
			$this->view->assign('user', $user);
		} else {
			$this->response->setStatus(401);
			$this->forward('login');
		}
	}
	
	public function loginAction() {
		$token = 'tx-ajaxlogin-form' . time();
		$this->view->assign('formToken', $token);
		$this->response->setHeader('X-Ajaxlogin-formToken', $token);
	}
	
	public function authenticateAction() {
		$user = $this->userRepository->findCurrent();
		
		if (!is_null($user)) {
			$message = Tx_Extbase_Utility_Localization::translate('login_successful', 'ajaxlogin');
			$this->flashMessageContainer->add($message, '', t3lib_FlashMessage::OK);

			$referer = t3lib_div::_GP('referer');
			$redirectUrl = t3lib_div::_GP('redirectUrl');
			$redirect_url = Tx_Ajaxlogin_Utility_RedirectUrl::findRedirectUrl($referer, $redirectUrl);
			if (!empty($redirect_url)) {
				$this->response->setHeader('X-Ajaxlogin-redirectUrl', $redirect_url);
			}
			$this->forward('info');
		} else {
			$this->response->setStatus(401);
			$message = Tx_Extbase_Utility_Localization::translate('authentication_failed', 'ajaxlogin');
			$this->flashMessageContainer->add($message, '', t3lib_FlashMessage::ERROR);
			$this->forward('login');
		}
	}
	
	/**
	 * Displays a form for creating a new blog
     *
     * @param Tx_Ajaxlogin_Domain_Model_User $newUser A fresh user object taken as a basis for the rendering
     * @return void
     * @dontvalidate $user
	 */
	public function newAction(Tx_Ajaxlogin_Domain_Model_User $user = null) {		
		if (!is_null($user)) {
			$this->response->setStatus(409);
		}
		
		$token = 'tx-ajaxlogin-form' . time();
		$this->view->assign('formToken', $token);
		$this->response->setHeader('X-Ajaxlogin-formToken', $token);
		
		$this->view->assign('user', $user);
	}
	
	/**
	 * Creates a new user
     *
     * @param Tx_Ajaxlogin_Domain_Model_User $user A fresh User object which has not yet been added to the repository
     * @param string $password_check
     * @return void
	 */
	public function createAction(Tx_Ajaxlogin_Domain_Model_User $user, $password_check) {		
		$check = $this->userRepository->findOneByUsername($user->getUsername());
		
		if (!is_null($check)) {
			$message = Tx_Extbase_Utility_Localization::translate('duplicate_username', 'ajaxlogin');
			$this->flashMessageContainer->add($message, '', t3lib_FlashMessage::ERROR);
			$this->forward('new', null, null, $this->request->getArguments());
		}
		
		if(strcmp($user->getPassword(), $password_check) != 0) {
			$message = Tx_Extbase_Utility_Localization::translate('password_nomatch', 'ajaxlogin');
			$this->flashMessageContainer->add($message, '', t3lib_FlashMessage::ERROR);
			$this->forward('new', null, null, $this->request->getArguments());
		}
		
		$userGroups = $this->userGroupRepository->findByUidArray(t3lib_div::intExplode(',', $this->settings['defaultUserGroups']));
		
		$password = $user->getPassword();
		
		$password = Tx_Ajaxlogin_Utility_Password::salt($password);
		
		foreach ($userGroups as $userGroup) {
			$user->getUsergroup()->attach($userGroup);
		}

		$user->setPassword($password);
		$this->userRepository->add($user);
		$this->userRepository->_persistAll();
		
		Tx_Ajaxlogin_Utility_FrontendUser::signin($user);

		$message = Tx_Extbase_Utility_Localization::translate('signup_successful', 'ajaxlogin');
		$this->flashMessageContainer->add($message, '', t3lib_FlashMessage::OK);

		$referer = t3lib_div::_GP('referer');
		$redirectUrl = t3lib_div::_GP('redirectUrl');
		$redirect_url = Tx_Ajaxlogin_Utility_RedirectUrl::findRedirectUrl($referer, $redirectUrl);
		if (!empty($redirect_url)) {
			$this->response->setHeader('X-Ajaxlogin-redirectUrl', $redirect_url);
		}
		
		$this->forward('info');
	}
	
	public function logoutAction() {
		$message = Tx_Extbase_Utility_Localization::translate('logout_successful', 'ajaxlogin');
		$this->flashMessageContainer->add($message, '', t3lib_FlashMessage::NOTICE);

		$GLOBALS['TSFE']->fe_user->logoff();
		$this->forward('login');
	}
	
	public function showAction() {
		$user = $this->userRepository->findCurrent();
		
		$this->view->assign('user', $user);
	}
	
	public function editAction() {
		$this->view->assign('user', $this->userRepository->findCurrent());
	}
	
	/**
	 * Updates an existing user
	 * 
	 * @param Tx_Ajaxlogin_Domain_Model_User
	 * @return void
	 */
	public function updateAction(Tx_Ajaxlogin_Domain_Model_User $user) {
		$this->userRepository->update($user);
		$this->flashMessageContainer->add('User updated');
		$this->redirect('show');
	}
	
	public function forgotPasswordAction() {
		$token = 'tx-ajaxlogin-form' . time();
		$this->view->assign('formToken', $token);
		$this->response->setHeader('X-Ajaxlogin-formToken', $token);
	}
	
	/**
	 * 
	 * Enter description here ...
	 * @param string $usernameOrEmail
	 */
	public function resetPasswordAction($usernameOrEmail = '') {
		$user = null;
		
		if(!empty($usernameOrEmail) && t3lib_div::validEmail($usernameOrEmail)) {
			$user = $this->userRepository->findOneByEmail($usernameOrEmail);
		} else if(!empty($usernameOrEmail)) {
			$user = $this->userRepository->findOneByUsername($usernameOrEmail);
		}
		
		if(!is_null($user)) {
			$user->setForgotHash(md5(t3lib_div::generateRandomBytes(64)));
			$user->setForgotHashValid((time() + (24 * 3600)));
			$this->view->assign('user', $user);
			
			$uriBuilder = $this->controllerContext->getUriBuilder();
			$uri = $uriBuilder->reset()->setCreateAbsoluteUri(true)->setTargetPageUid($this->settings['actionPid']['editPassword'])->uriFor('editPassword', array(
				'email' => $user->getEmail(),
				'forgotHash' => $user->getForgotHash()
			));
			
			$subject = Tx_Extbase_Utility_Localization::translate('resetpassword_notification_subject', 'ajaxlogin', array(
				t3lib_div::getIndpEnv('TYPO3_HOST_ONLY')
			));
			
			$message = Tx_Extbase_Utility_Localization::translate('resetpassword_notification_message', 'ajaxlogin', array(
				$user->getName(),
				$uri,
				strftime($this->settings['notificationMail']['strftimeFormat'])
			));
			
			$this->view->assign('uri', $uri);
			
			Tx_Ajaxlogin_Utility_NotifyMail::send($user->getEmail(), $subject, $message);			
		} else {
			$this->response->setStatus(409);
			$message = Tx_Extbase_Utility_Localization::translate('user_notfound', 'ajaxlogin', array($usernameOrEmail));
			$this->flashMessageContainer->add($message, '', t3lib_FlashMessage::NOTICE);
			$this->forward('forgotPassword');
		}
	}
	
	/**
	 * @param string $forgotHash
	 * @param string $email
	 */
	public function editPasswordAction($forgotHash = '', $email = '') {
		if(!empty($forgotHash) && !empty($email)) {
			$user = $this->userRepository->findOneByForgotHashAndEmail($forgotHash, $email);
		} else {
			$user = $this->userRepository->findCurrent();
		}
		
		if(!is_null($user)) {
			$this->view->assign('user', $user);
		} else {
			$this->response->setStatus(401);
		}
	}
	
	public function closeAccountAction() {
		$this->view->assign('user', $this->userRepository->findCurrent());
	}

	/**
	 * Disable currently logged in user and logout afterwards
	 * @param Tx_Ajaxlogin_Domain_Model_User
	 * @return void
	 */
	public function disableAction(Tx_Ajaxlogin_Domain_Model_User $user) {
		$this->userRepository->update($user);
		$GLOBALS['TSFE']->fe_user->logoff();
		$this->redirectToURI('/');
	}

	/**
	 * @param array $password
	 * @param Tx_Ajaxlogin_Domain_Model_User $user
	 */
	public function updatePasswordAction($password, Tx_Ajaxlogin_Domain_Model_User $user) {
		$passwordValidator = t3lib_div::makeInstance('Tx_Ajaxlogin_Domain_Validator_CustomRegularExpressionValidator');
		
		$passwordValidator->setOptions(array(
			'object' => 'User',
			'property' => 'password'
		));
		
		if(!empty($password['new']) && strcmp($password['new'], $password['check']) == 0 && $passwordValidator->isValid($password['new'])) {
			$saltedPW = Tx_Ajaxlogin_Utility_Password::salt($password['new']);
			$user->setPassword($saltedPW);
			$user->setForgotHash('');
			$user->setForgotHashValid(0);
			$message = Tx_Extbase_Utility_Localization::translate('password_updated', 'ajaxlogin');
			$this->flashMessageContainer->add($message, '', t3lib_FlashMessage::OK);
			$this->redirect('show');
		} else {
			$this->response->setStatus(409);
			$message = Tx_Extbase_Utility_Localization::translate('password_invalid', 'ajaxlogin');
			$this->flashMessageContainer->add($message, '', t3lib_FlashMessage::ERROR);
			$this->forward('editPassword');
		}
	}
}

?>