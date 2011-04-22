<?php

class Tx_Ajaxlogin_Domain_Repository_UserRepository implements t3lib_Singleton {
	
	public function findOneByUsernameOrEmail($arg) {
		$arg = $GLOBALS['TYPO3_DB']->fullQuoteStr($arg, 'fe_users');
		
		$where = array(
			'fe_users.disable = 0',
			'fe_users.deleted = 0',
    		'fe_users.starttime <= ' . $GLOBALS['SIM_ACCESS_TIME'],
    		'(fe_users.endtime = 0 OR fe_users.endtime > ' . $GLOBALS['SIM_ACCESS_TIME'] . ')',
			'(fe_users.username = ' . $arg . ' OR fe_users.email = ' . $arg . ')'
		);
		
		return $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
			'*',
			'fe_users',
			implode(' AND ', $where)
		);
	}
	
	public function findOneByForgotHash($hash) {
		$hash = $GLOBALS['TYPO3_DB']->fullQuoteStr($hash, 'fe_users');
		
		$where = array(
			'fe_users.disable = 0',
			'fe_users.deleted = 0',
    		'fe_users.starttime <= ' . $GLOBALS['SIM_ACCESS_TIME'],
    		'(fe_users.endtime = 0 OR fe_users.endtime > ' . $GLOBALS['SIM_ACCESS_TIME'] . ')',
			'fe_users.tx_ajaxlogin_forgotHash = ' . $hash,
			'fe_users.tx_ajaxlogin_forgotHashValid > ' . time()
		);
		
		return $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
			'*',
			'fe_users',
			implode(' AND ', $where)
		);
	}
	
	public function update($uid, $fields) {
		unset($fields['uid']);
		unset($fields['username']); // make sure the username isn't changed
		return $GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users', 'uid = ' . intval($uid), $fields);
	}
	
	public function insert($fields) {
		unset($fields['uid']);
		return $GLOBALS['TYPO3_DB']->exec_INSERTquery('fe_users', $fields);
	}

}

?>