<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

$extractMetadataImage = $extractMetadataPdf = TRUE;
if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['metadata'])) {
	$configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['metadata']);
	$extractMetadataImage = (bool)$configuration['extract_metadata_image'];
	$extractMetadataPdf = (bool)$configuration['extract_metadata_pdf'];
}

// Register metadata extractor for images if configured so.
if ( $extractMetadataImage) {
	\TYPO3\CMS\Core\Resource\Index\ExtractorRegistry::getInstance()->registerExtractionService('Fab\Metadata\Index\ImageMetadataExtractor');
}

// Register metadata extractor for pdf if configured so.
if ( $extractMetadataPdf) {
	\TYPO3\CMS\Core\Resource\Index\ExtractorRegistry::getInstance()->registerExtractionService('Fab\Metadata\Index\PdfMetadataExtractor');
}
