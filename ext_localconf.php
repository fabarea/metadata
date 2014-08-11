<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

// Register metadata extractors.
\TYPO3\CMS\Core\Resource\Index\ExtractorRegistry::getInstance()->registerExtractionService('Fab\Metadata\Index\ImageMetadataExtractor');
\TYPO3\CMS\Core\Resource\Index\ExtractorRegistry::getInstance()->registerExtractionService('Fab\Metadata\Index\PdfMetadataExtractor');

