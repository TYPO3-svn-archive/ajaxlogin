<?php

class Tx_Ajaxlogin_Domain_Model_User {
	
	/**
	 * @var tslib_feUserAuth
	 */
	protected $userObject;
	
	/**
	 * @var array
	 */
	protected $sessionData;
	
	public function __construct(tslib_feUserAuth $userObject) {
		$this->userObject = $userObject;
		
		$this->sessionData = $this->userObject->fetchUserSession();
	}
	
	/**
	 * @return tslib_feUserAuth
	 */
	public function getUserObject() {
		return $this->userObject;
	}
	
	public function isLoggedIn() {
		if($this->sessionData) {
			return true;
		}
		
		return false;
	}
	
	public function getUsername() {
		if($this->sessionData && array_key_exists('username', $this->sessionData)) {
			return $this->sessionData['username'];
		}
		
		return null;
	}
	
	public function getName() {
		if($this->sessionData && array_key_exists('name', $this->sessionData)) {
			return $this->sessionData['name'];
		}
		
		return null;
	}
	
	public function getFirstName() {
		if($this->sessionData && array_key_exists('first_name', $this->sessionData)) {
			return $this->sessionData['first_name'];
		}
		
		return null;
	}
	
	public function getMiddleName() {
		if($this->sessionData && array_key_exists('middle_name', $this->sessionData)) {
			return $this->sessionData['middle_name'];
		}
		
		return null;
	}
	
	public function getLastName() {
		if($this->sessionData && array_key_exists('last_name', $this->sessionData)) {
			return $this->sessionData['last_name'];
		}
		
		return null;
	}
	
	public function getImageHash() {
		if($this->sessionData && array_key_exists('tx_t3ouserimage_img_hash', $this->sessionData)) {
			return $this->sessionData['tx_t3ouserimage_img_hash'];
		}
		
		return null;
	}
}

?>