<?php
defined('TYPO3_MODE') || die('Access denied.');

$tca = [
	'palettes' => [
		'22' => [
			'showitem' => 'iso_speed_ratings, aperture_value, shutter_speed_value, focal_length, --linebreak--, camera_model, flash, metering_mode',
			'canNotCollapse' => '1'
		],
		'51' => ['showitem' => 'horizontal_resolution, vertical_resolution', 'canNotCollapse' => '1'],
	],
	'columns' => [
		'credit' => [
			'exclude' => 1,
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.credit',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'max' => '32',
				'eval' => 'trim'
			],
		],
		'aperture_value' => [
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.aperture_value',
			'config' => [
				'type' => 'input',
				'size' => '7',
				'eval' => 'float',
				'default' => '0',
				'readOnly' => true,
			],
		],
		'shutter_speed_value' => [
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.shutter_speed_value',
			'config' => [
				'type' => 'input',
				'size' => '7',
				'max' => '24',
				'eval' => 'trim',
				'default' => '0',
				'readOnly' => true,
			],
		],
		'iso_speed_ratings' => [
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.iso_speed_ratings',
			'config' => [
				'type' => 'input',
				'size' => '7',
				'max' => '24',
				'eval' => 'trim',
				'default' => '0',
				'readOnly' => true,
			],
		],
		'camera_model' => [
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.camera_model',
			'config' => [
				'type' => 'input',
				'size' => '12',
				'max' => '255',
				'eval' => 'trim',
				'default' => '',
				'readOnly' => true,
			],
		],
		'focal_length' => [
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.focal_length',
			'config' => [
				'type' => 'input',
				'size' => '7',
				'max' => '24',
				'eval' => 'int',
				'default' => '0',
				'readOnly' => true,
			],
		],
		'flash' => [
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.flash',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'default' => '-1',
				'items' => [
					['', '0'],
					['Flash', '1'],
					['Flash, strobe return light not detected', '5'],
					['Flash, strobe return light detected', '7'],
					['Compulsory Flash', '9'],
					['Compulsory Flash, Return light not detected', '13'],
					['Compulsory Flash, Return light detected', '15'],
					['No Flash', '16'],
					['No Flash', '24'],
					['Flash, Auto-Mode', '25'],
					['Flash, Auto-Mode, Return light not detected', '29'],
					['Flash, Auto-Mode, Return light detected', '31'],
					['No Flash', '32'],
					['Red Eye', '65'],
					['Red Eye, Return light not detected', '69'],
					['Red Eye, Return light detected', '71'],
					['Red Eye, Compulsory Flash', '73'],
					['Red Eye, Compulsory Flash, Return light not detected', '77'],
					['Red Eye, Compulsory Flash, Return light detected', '79'],
					['Red Eye, Auto-Mode', '89'],
					['Red Eye, Auto-Mode, Return light not detected', '93'],
					['Red Eye, Auto-Mode, Return light detected', '95'],
				],
				'readOnly' => true,
			],
		],
		'metering_mode' => [
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.metering_mode',
			'config' => [
				'type' => 'select',
				'default' => '-1',
				'renderType' => 'selectSingle',
				'itemListStyle' => 'width:200px;',
				'items' => [
					['', '0'],
					['Average', '1'],
					['Center Weighted Average', '2'],
					['Spot', '3'],
					['Multi-Spot', '4'],
					['Pattern', '5'],
					['Partial', '6'],
					['Other', '255'],
				],
				'readOnly' => true,
			],
		],
		'horizontal_resolution' => [
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.horizontal_resolution',
			'config' => [
				'type' => 'input',
				'size' => '10',
				'max' => '8',
				'eval' => 'int',
				'default' => '0',
				'readOnly' => true,
			],
		],
		'vertical_resolution' => [
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'l10n_display' => 'defaultAsReadonly',
			'label' => 'LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:sys_file_metadata.vertical_resolution',
			'config' => [
				'type' => 'input',
				'size' => '10',
				'max' => '8',
				'eval' => 'int',
				'default' => '0',
				'readOnly' => true,
			],
		],
		// existing fields
		'color_space' => [
			'config' => [
				'items' => [
					['sRGB', 'sRGB'],
				],
			]
		],
	]
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	'sys_file_metadata',
	'credit',
	TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE,
	'after:copyright'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	'sys_file_metadata',
	'--palette--;LLL:EXT:metadata/Resources/Private/Language/locallang.xlf:palette.exif;22;;',
	TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE,
	'after:color_space'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
	'sys_file_metadata',
	'--palette--;;51',
	TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE,
	'after:unit'
);

\TYPO3\CMS\Core\Utility\ArrayUtility::mergeRecursiveWithOverrule($GLOBALS['TCA']['sys_file_metadata'], $tca);
