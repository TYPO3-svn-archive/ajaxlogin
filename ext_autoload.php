<?php
/* 
 * Register necessary class names with autoloader
 *
 * $Id: $
 */
return array(
	'tx_ajaxlogin_bootstrap' => t3lib_extMgm::extPath('ajaxlogin', 'Classes/Bootstrap.php'),
	'tx_ajaxlogin_controller_loginformcontroller' => t3lib_extMgm::extPath('ajaxlogin', 'Classes/Controller/LoginFormController.php'),
	'tx_ajaxlogin_controller_rsakeycontroller' => t3lib_extMgm::extPath('ajaxlogin', 'Classes/Controller/RSAKeyController.php'),
	'tx_ajaxlogin_controller_passwordcontroller' => t3lib_extMgm::extPath('ajaxlogin', 'Classes/Controller/PasswordController.php'),
	'tx_ajaxlogin_controller_usercontroller' => t3lib_extMgm::extPath('ajaxlogin', 'Classes/Controller/UserController.php'),
	'tx_ajaxlogin_domain_model_user' => t3lib_extMgm::extPath('ajaxlogin', 'Classes/Domain/Model/User.php'),
	'tx_ajaxlogin_domain_repository_userrepository' => t3lib_extMgm::extPath('ajaxlogin', 'Classes/Domain/Repository/UserRepository.php'),
	'tx_ajaxlogin_utility_localization' => t3lib_extMgm::extPath('ajaxlogin', 'Classes/Utility/Localization.php'),
	'tx_ajaxlogin_utility_notifymail' => t3lib_extMgm::extPath('ajaxlogin', 'Classes/Utility/NotifyMail.php'),
	'tx_ajaxlogin_utility_typoscript' => t3lib_extMgm::extPath('ajaxlogin', 'Classes/Utility/TypoScript.php'),
	'tx_ajaxlogin_utility_userimage' => t3lib_extMgm::extPath('ajaxlogin', 'Classes/Utility/UserImage.php')
);
?>
