<?php
/*******************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Kai Vogel <kai.vogel@speedprogs.de>, Speedprogs.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as
 *  published by the Free Software Foundation; either version 2 of
 *  the License, or (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ******************************************************************/

/**
 * Utilities to manage and convert Typoscript Code
 * 
 * This class was originally created by Kai Vogel, I adapted it to fit Ajaxlogin
 *
 * @version $Id$
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 * @author Arno Schoon <arno@maxserv.nl>
 */
class Tx_Ajaxlogin_Utility_TypoScript {

	/**
	 * @var tslib_cObj
	 */
	static protected $contentObject;


	/**
	 * Initialize configuration manager and content object
	 *
	 * @return void
	 * @author Kai Vogel <kai.vogel@speedprogs.de>
 	 * @author Arno Schoon <arno@maxserv.nl>
	 */
	static private function initialize() {
		// Simulate Frontend
		$GLOBALS['TSFE'] = new stdClass();
		$GLOBALS['TSFE']->cObjectDepthCounter = 100;
		$GLOBALS['TSFE']->cObj = t3lib_div::makeInstance('tslib_cObj');
		$GLOBALS['TSFE']->csConvObj = t3lib_div::makeInstance('t3lib_cs');
		$GLOBALS['TSFE']->tmpl = t3lib_div::makeInstance('t3lib_TStemplate');
		
		if (empty($GLOBALS['TSFE']->sys_page)) {
			$GLOBALS['TSFE']->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
		}
		if (empty($GLOBALS['TT'])) {
			$GLOBALS['TT'] = t3lib_div::makeInstance('t3lib_TimeTrackNull');
		}

		// Get content object
		self::$contentObject = $GLOBALS['TSFE']->cObj;
		if (empty(self::$contentObject)) {
			self::$contentObject = t3lib_div::makeInstance('tslib_cObj');
		}
	}


	/**
	 * Returns unparsed TypoScript setup
	 *
	 * @return array TypoScript setup
	 * @author Kai Vogel <kai.vogel@speedprogs.de>
	 */
	static public function getSetup() {
		if (empty(self::$configurationManager)) {
			self::initialize();
		}

		$configurationManager = t3lib_div::makeInstance('Tx_Extbase_Configuration_BackendConfigurationManager');
		$setup = $configurationManager->getTypoScriptSetup();

		if (!empty($setup['plugin.']['tx_ajaxlogin.'])) {
			return $setup['plugin.']['tx_ajaxlogin.'];
		}

		return array();
	}


	/**
	 * Parse given TypoScript configuration
	 *
	 * @param array $configuration TypoScript configuration
	 * @return array Parsed configuration
	 * @author Kai Vogel <kai.vogel@speedprogs.de>
	 */
	static public function parse(array $configuration) {
		if (empty(self::$contentObject)) {
			self::initialize();
		}

		// Parse configuration
		$configuration = self::parseTypoScriptArray($configuration);
		$configuration = t3lib_div::removeDotsFromTS($configuration);

		return $configuration;
	}


	/**
	 * Parse TypoScript configuration
	 *
	 * @param array $configuration TypoScript configuration
	 * @return array Parsed configuration
	 * @author Kai Vogel <kai.vogel@speedprogs.de>
	 */
	static protected function parseTypoScriptArray(array $configuration) {
		$typoScriptArray = array();

		foreach ($configuration as $key => $value) {
			$ident = rtrim($key, '.');
			if (is_array($value)) {
				if (!empty($configuration[$ident])) {
					$typoScriptArray[$ident] = self::$contentObject->cObjGetSingle($configuration[$ident], $value);
					unset($configuration[$key]);
				} else {
					$typoScriptArray[$key] = self::parseTypoScriptArray($value);
				}
			} else if (is_string($value) && $key == $ident) {
				$typoScriptArray[$key] = $value;
			}
		}

		return $typoScriptArray;
	}

}
?>