<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService($_EXTKEY, 'metaExtract', 'Metadata\PdfService', array(
	'title'       => 'PDF meta data extraction',
	'description' => 'Uses Zend PDF to extract meta data',

	'subtype'     => 'application/pdf',

	'available'   => TRUE,
	'priority'    => 50,
	'quality'     => 50,

	'os'          => '',
	'exec'        => '',

	'classFile'   => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Classes/Service/Metadata/Pdf.php',
	'className'   => 'Fab\Metadata\Service\Metadata\Pdf',
));

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addService($_EXTKEY, 'metaExtract', 'Metadata\ImageService', array(
	'title'       => 'Image meta data extraction',
	'description' => 'Uses PHP EXIF/IPTC functions to extract meta data',

	'subtype'     => 'image/jpeg,image/tiff,image/png,image/gif',

	'available'   => TRUE,
	'priority'    => 50,
	'quality'     => 50,

	'os'          => '',
	'exec'        => '',

	'classFile'   => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Classes/Service/Metadata/Image.php',
	'className'   => 'Fab\Metadata\Service\Metadata\Image',
));


?>