<?php
if (!defined('TYPO3_MODE')) die ('Access denied.');

$tca = array(
	'types' => array(
		TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => array('showitem' => '
			fileinfo, title, description, alternative, keywords, caption, download_name,

			--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
				--palette--;LLL:EXT:filemetadata/Resources/Private/Language/locallang_tca.xlf:palette.visibility;10;;,
				fe_groups,

			--div--;LLL:EXT:filemetadata/Resources/Private/Language/locallang_tca.xlf:tabs.metadata,
				creator,
				--palette--;;20;;,
				--palette--;;21;;,
				--palette--;LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:palette.exif;22;;,
				--palette--;;23;;,
				--palette--;LLL:EXT:filemetadata/Resources/Private/Language/locallang_tca.xlf:palette.geo_location;40;;,
				--palette--;;30;;,
				--palette--;LLL:EXT:filemetadata/Resources/Private/Language/locallang_tca.xlf:palette.metrics;50;;,
				--palette--;;51;;'
		),
	),
	'palettes' => array(
		'20' => array('showitem' => 'publisher, source', 'canNotCollapse' => '1'),
		'21' => array('showitem' => 'creator_tool, copyright_notice', 'canNotCollapse' => '1'),
		'22' => array(
			'showitem' => 'iso_speed_ratings, aperture_value, shutter_speed_value, focal_length',
			'canNotCollapse' => '1'
		),
		'23' => array('showitem' => 'camera_model, flash, metering_mode', 'canNotCollapse' => '1'),
		'51' => array('showitem' => 'horizontal_resolution, vertical_resolution', 'canNotCollapse' => '1'),
	),
	'columns' => array(
		'copyright_notice' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.copyright_notice',
			'config' => array(
				'type' => 'input',
				'size' => 40,
				'max' => '255',
				'eval' => 'trim'
			),
		),
		'aperture_value' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.aperture_value',
			'config' => array(
				'type' => 'input',
				'size' => '7',
				'eval' => 'float',
				'default' => '0',
				'readOnly' => TRUE,
			),
		),
		'shutter_speed_value' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.shutter_speed_value',
			'config' => array(
				'type' => 'input',
				'size' => '7',
				'max' => '24',
				'eval' => 'trim',
				'default' => '0',
				'readOnly' => TRUE,
			),
		),
		'iso_speed_ratings' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.iso_speed_ratings',
			'config' => array(
				'type' => 'input',
				'size' => '7',
				'max' => '24',
				'eval' => 'trim',
				'default' => '0',
				'readOnly' => TRUE,
			),
		),
		'camera_model' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.camera_model',
			'config' => array(
				'type' => 'input',
				'size' => '12',
				'max' => '255',
				'eval' => 'trim',
				'default' => '',
				'readOnly' => TRUE,
			),
		),
		'focal_length' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.focal_length',
			'config' => array(
				'type' => 'input',
				'size' => '7',
				'max' => '24',
				'eval' => 'int',
				'default' => '0',
				'readOnly' => TRUE,
			),
		),
		'flash' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.flash',
			'config' => array(
				'type' => 'select',
				'default' => '-1',
				'items' => array(
					array('', '0'),
					array('Flash', '1'),
					array('Flash, strobe return light not detected', '5'),
					array('Flash, strobe return light detected', '7'),
					array('Compulsory Flash', '9'),
					array('Compulsory Flash, Return light not detected', '13'),
					array('Compulsory Flash, Return light detected', '15'),
					array('No Flash', '16'),
					array('No Flash', '24'),
					array('Flash, Auto-Mode', '25'),
					array('Flash, Auto-Mode, Return light not detected', '29'),
					array('Flash, Auto-Mode, Return light detected', '31'),
					array('No Flash', '32'),
					array('Red Eye','65'),
					array('Red Eye, Return light not detected','69'),
					array('Red Eye, Return light detected', '71'),
					array('Red Eye, Compulsory Flash', '73'),
					array('Red Eye, Compulsory Flash, Return light not detected', '77'),
					array('Red Eye, Compulsory Flash, Return light detected', '79'),
					array('Red Eye, Auto-Mode', '89'),
					array('Red Eye, Auto-Mode, Return light not detected', '93'),
					array('Red Eye, Auto-Mode, Return light detected', '95'),
				),
				'readOnly' => TRUE,
			),
		),
		'metering_mode' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.metering_mode',
			'config' => array(
				'type' => 'select',
				'default' => '-1',
				'itemListStyle' => 'width:200px;',
				'items' => array(
					array('', '0'),
					array('Average', '1'),
					array('Center Weighted Average', '2'),
					array('Spot', '3'),
					array('Multi-Spot', '4'),
					array('Pattern', '5'),
					array('Partial', '6'),
					array('Other', '255'),
				),
				'readOnly' => TRUE,
			),
		),
		'horizontal_resolution' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.horizontal_resolution',
			'config' => array(
				'type' => 'input',
				'size' => '10',
				'max' => '8',
				'eval' => 'int',
				'default' => '0',
				'readOnly' => TRUE,
			),
		),
		'vertical_resolution' => array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.vertical_resolution',
			'config' => array(
				'type' => 'input',
				'size' => '10',
				'max' => '8',
				'eval' => 'int',
				'default' => '0',
				'readOnly' => TRUE,
			),
		),
		// existing fields
		'color_space' => array(
			'config' => array(
				'items' => array(
					array('sRGB', 'sRGB'),
				),
			)
		),
	),
);
\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['sys_file_metadata'], $tca);