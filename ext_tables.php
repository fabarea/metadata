<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

/** @var \TYPO3\CMS\Extbase\Object\ObjectManager $objectManager */
$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

// Connect "postFileIndex" signal slot with the metadata service.
/** @var $signalSlotDispatcher \TYPO3\CMS\Extbase\SignalSlot\Dispatcher */
$signalSlotDispatcher = $objectManager->get('TYPO3\CMS\Extbase\SignalSlot\Dispatcher');
$signalSlotDispatcher->connect('TYPO3\CMS\Core\Resource\Service\IndexerService', 'postFileIndex', 'Fab\Metadata\Service\IndexerService', 'postFileIndex', FALSE);