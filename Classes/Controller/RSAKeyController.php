<?php

require_once(t3lib_extMgm::extPath('rsaauth') . 'sv1/backends/class.tx_rsaauth_backendfactory.php');
require_once(t3lib_extMgm::extPath('rsaauth') . 'sv1/storage/class.tx_rsaauth_storagefactory.php');

class Tx_Ajaxlogin_Controller_RSAKeyController {
	public function createAction() {
		$result = null;
		
		if ($GLOBALS['TYPO3_CONF_VARS']['FE']['loginSecurityLevel'] == 'rsa') {
			$backend = tx_rsaauth_backendfactory::getBackend();
			if ($backend) {
				$result = array();
				
				// Generate a new key pair
				$keyPair = $backend->createNewKeyPair();

				// Save private key
				$storage = tx_rsaauth_storagefactory::getStorage();
				/* @var $storage tx_rsaauth_abstract_storage */
				$storage->put($keyPair->getPrivateKey());

				// Add RSA hidden fields
				$result['n'] = $keyPair->getPublicKeyModulus();
				$result['e'] = sprintf('%x', $keyPair->getExponent());
			}
		}
		return $result;
	}
}

?>