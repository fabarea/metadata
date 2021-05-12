<?php
defined('TYPO3_MODE') || die('Access denied.');

// Get extension configuration
if (class_exists(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)) {
	$extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
		\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
	);
	$configuration = $extensionConfiguration->get('metadata');
} else {
	// @extensionScannerIgnoreLine Fallback to access extConf for TYPO3 CMS version below 9.5
	if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['metadata'])) {
		$configuration = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['metadata'];
		if (!is_array($configuration)) {
			$configuration = unserialize($configuration);
		}
	}
}

// Register metadata extractor for images if configured so.
if (!empty($configuration['extract_image_metadata'])) {
	\TYPO3\CMS\Core\Resource\Index\ExtractorRegistry::getInstance()
		->registerExtractionService(\Fab\Metadata\Index\ImageMetadataExtractor::class);
}

// Register metadata extractor for pdf if configured so.
if (!empty($configuration['extract_pdf_metadata'])) {
	\TYPO3\CMS\Core\Resource\Index\ExtractorRegistry::getInstance()
		->registerExtractionService(\Fab\Metadata\Index\PdfMetadataExtractor::class);
}
