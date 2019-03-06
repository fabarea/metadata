<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

$extractMetadataImage = $extractMetadataPdf = true;

// Get extension configuration
if (class_exists(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)) {
    $extensionConfiguration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class
    );
    $configuration = $extensionConfiguration->get('metadata');
} else {
    // @extensionScannerIgnoreLine Fallback to access extConf for TYPO3 CMS version below 9.5
    if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['metadata'])
        && !empty($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['metadata'])
    ) {
        $configuration = $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['metadata'];
        if (!is_array($configuration)) {
            $configuration = unserialize($configuration);
        }
    }
}

// Register metadata extractor for images if configured so.
if (isset($configuration['extract_image_metadata']) && (bool)$configuration['extract_image_metadata']) {
    \TYPO3\CMS\Core\Resource\Index\ExtractorRegistry::getInstance()
        ->registerExtractionService('Fab\Metadata\Index\ImageMetadataExtractor');
}

// Register metadata extractor for pdf if configured so.
if (isset($configuration['extract_pdf_metadata']) && (bool)$configuration['extract_pdf_metadata']) {
    \TYPO3\CMS\Core\Resource\Index\ExtractorRegistry::getInstance()
        ->registerExtractionService('Fab\Metadata\Index\PdfMetadataExtractor');
}
