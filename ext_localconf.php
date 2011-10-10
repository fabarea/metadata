<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::addService($_EXTKEY, 'metaExtract', 'Tx_Media_PdfService', array(
	'title'       => 'PDF meta data extraction',
	'description' => 'Uses Zend PDF to extract meta data',

	'subtype'     => 'application/pdf',

	'available'   => TRUE,
	'priority'    => 50,
	'quality'     => 50,

	'os'          => '',
	'exec'        => '',

	'classFile'   => t3lib_extMgm::extPath($_EXTKEY) . 'Classes/Service/Metadata/Pdf.php',
	'className'   => 'Tx_Metadata_Service_Metadata_Pdf',
));

t3lib_extMgm::addService($_EXTKEY, 'metaExtract', 'Tx_Media_ImageService', array(
	'title'       => 'Image meta data extraction',
	'description' => 'Uses PHP EXIF/IPTC functions to extract meta data',

	'subtype'     => 'image/jpeg,image/tiff,image/png,image/gif',

	'available'   => TRUE,
	'priority'    => 50,
	'quality'     => 50,

	'os'          => '',
	'exec'        => '',

	'classFile'   => t3lib_extMgm::extPath($_EXTKEY) . 'Classes/Service/Metadata/Image.php',
	'className'   => 'Tx_Metadata_Service_Metadata_Image',
));

?>