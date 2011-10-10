<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}



if (TYPO3_MODE === 'BE') {

	/**
	 * Registers a Backend Module
	 */
	Tx_Extbase_Utility_Extension::registerModule(
		$_EXTKEY,
		'tools',	 // Make module a submodule of 'tools'
		'metadataevaluator',	// Submodule key
		'',						// Position
		array(
			'Test' => 'index',
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_metadataevaluator.xml',
		)
	);

}


t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Media: Metadata extraction');


?>