<?php

class Tx_Ajaxlogin_Domain_Validator_CustomRegularExpressionValidator extends Tx_Extbase_Validation_Validator_AbstractValidator {
	
	public function isValid($value) {
		$setup = Tx_Ajaxlogin_Utility_TypoScript::getSetup();
		$setup = Tx_Extbase_Utility_TypoScript::convertTypoScriptArrayToPlainArray($setup);
		
		$object = trim($this->options['object']);
		$property = trim($this->options['property']);
		
		$pattern = trim($setup['settings']['validation'][$object][$property]);
		
		if(!preg_match($pattern, $value)) {
			$this->addError('The given subject did not match the pattern "' . $pattern . '"', 1307626687);
			return false;
		}
		
		return true;
	}
}

?>