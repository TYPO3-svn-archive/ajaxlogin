<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$tempColumns = array (
	'tx_ajaxlogin_forgotHash' => array (
		'config' => array (
			'type' => 'passthrough',
		)
	),
	'tx_ajaxlogin_forgotHashValid' => array (
		'config' => array (
			'type' => 'passthrough',
		)
	),
);

t3lib_div::loadTCA("fe_users");
t3lib_extMgm::addTCAcolumns("fe_users", $tempColumns, 1);

t3lib_extMgm::addPlugin(array(
	'Userprofile',
	'ajaxlogin'
));

t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'AJAX login');
?>