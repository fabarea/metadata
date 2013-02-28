<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Let see if a BE module is required
// It could make sense if one has to define how metadata extraction should behave
if (FALSE) { # if (TYPO3_MODE === 'BE') {

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


if (FALSE) {
	t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Media: Metadata extraction');
}

// Connect "postFileIndex" signal slot with the metadata service.
/** @var $signalSlotDispatcher \TYPO3\CMS\Extbase\SignalSlot\Dispatcher */
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager')
	->get('TYPO3\CMS\Extbase\SignalSlot\Dispatcher');

$signalSlotDispatcher->connect('TYPO3\CMS\Core\Resource\Service\IndexerService', 'postFileIndex', 'TYPO3\CMS\Metadata\Service\IndexerService', 'postFileIndex', FALSE);
?>