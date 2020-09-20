<?php
namespace Fab\Metadata\Index;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service dealing with metadata extraction of images.
 */
class ImageMetadataExtractor extends AbstractExtractor {

	/**
	 * Allowed file types
	 *
	 * @var array
	 */
	protected $allowedFileTypes = array(AbstractFile::FILETYPE_IMAGE);

    /**
	 * Allowed image types
	 *
	 * @var array
	 */
	protected $allowedImageTypes = array(IMAGETYPE_JPEG, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM);

	/**
	 * Allowed file extensions
	 *
	 * @var array
	 */
	protected $allowedFileExtensions = array(
		'jpeg',
		'jpg',
		'tiff',
		'gif',
		'png',
	);

	/**
	 * @var array
	 */
	protected $iptcAttributesMapping = array(
		'2#005' => 'title',
		'2#120' => 'caption',
		'2#025' => 'keywords',
		'2#115' => 'publisher',
		'2#080' => 'creator',
		'2#110' => 'credit',
		'2#116' => 'copyright_notice',
		'2#100' => 'location_country',
		'2#090' => 'location_city',
		'2#055' => 'content_creation_date',
	);

	/**
	 * @var array
	 */
	protected $colorSpaceToNameMapping = array(
		'0' => 'grey',
		'1' => 'sRGB',
		'2' => 'RGB',
		'3' => 'RGB',
		'4' => 'grey',
		'6' => 'RGB',
	);

	/**
	 * Returns the data priority of the extraction Service.
	 * Defines the precedence of Data if several extractors
	 * extracted the same property.
	 * Should be between 1 and 100, 100 is more important than 1
	 *
	 * @return integer
	 */
	public function getPriority() {
		return 17;
	}

	/**
	 * Returns the execution priority of the extraction Service
	 * Should be between 1 and 100, 100 means runs as first service,
	 * 1 runs at last service
	 *
	 * @return integer
	 */
	public function getExecutionPriority() {
		return 17;
	}

	/**
	 * Checks if the given file can be processed by this Extractor
	 *
	 * @param File $file
	 * @return boolean
	 */
	public function canProcess(File $file) {
		return in_array($file->getExtension(), $this->allowedFileExtensions);
	}

	/**
	 * The actual processing TASK
	 * Should return an array with database properties for sys_file_metadata to write
	 *
	 * @param File $file
	 * @param array $previousExtractedData optional, contains the array of already extracted data
	 *
	 * @return array
	 */
	public function extractMetaData(File $file, array $previousExtractedData = array()) {
		$filename = $file->getForLocalProcessing(false);
		$metadata = array(
			'unit' => 'px'
		);

		// Parse basic metadata from getimagesize, write additional metadata to $info
		$info = array();
		if (@is_file($filename)) {
			$imageSize = getimagesize($filename, $info);
		}

		if (isset($imageSize['channels'])) {
			$metadata['color_space'] = $this->getColorSpace($imageSize['channels']);
		}

		$this->extractExifMetaData($metadata, $filename);
		$this->extractIptcMetaData($metadata, $info);

		return $metadata;
	}

	/**
	 * Extract EXIF meta data
	 *
	 * @param array $metadata
	 * @param string $filename
	 *
	 * @return void
	 */
	protected function extractExifMetaData(&$metadata, $filename) {
		if (!$this->isExifExtensionAvailable()) {
			$this->getLogger()->warning('Function exif_imagetype() and exif_read_data() are not available.');
			return;
		}

		// Only try to read exif data for supported types
		if (!$this->isAllowedImageType($filename)) {
			return;
		}

		$convertEncodingManually = false;
		if (@ini_set('exif.encode_unicode', 'UTF-8') === false) {
			$convertEncodingManually = true;
		}

		$data = array();
		if (@is_file($filename)) {
			$data = @exif_read_data($filename, 0, TRUE);
		}

		if (is_array($data['EXIF'])) {
			$exif = $data['EXIF'];
			if (is_array($data['IFD0'])) {
				$exif = array_merge($data['IFD0'], $exif);
			}
			// adds GPS data from global exif data
			if (is_array($data['GPS']) && !isset($exif['GPS'])) {
				$exif['GPS'] = $data['GPS'];
			}

			$this->processExifData($metadata, $exif);
		}

		if ($convertEncodingManually) {
			$metadata = $this->getUnicodeUtility()->convertValues($metadata);
		}
	}

	/**
	 * Parse metadata from EXIF incl. IFD0 block
	 *
	 * @param $metadata
	 * @param $exif
	 *
	 * @return void
	 */
	protected function processExifData(&$metadata, $exif) {
		foreach ($exif as $exifAttribute => $value) {

			switch ($exifAttribute) {
				case 'Headline':
				case 'Title':
				case 'XPTitle':
					$metadata['title'] = $value;
					break;

				case 'Subject':
				case 'ImageDescription':
				case 'Description':
					$metadata['description'] = $value;
					break;

				case 'CaptionAbstract':
					$metadata['caption'] = $value;
					break;

				case 'Keywords':
				case 'XPKeywords':
					$metadata['keywords'] = $value;
					break;

				case 'ImageCreated':
				case 'CreateDate':
				case 'DateTimeOriginal':
				case 'DateTimeDigitized':
					$metadata['content_creation_date'] = strtotime($value);
					break;

				case 'ModifyDate':
				case 'DateTime':
					$metadata['content_modification_date'] = strtotime($value);
					break;

				case 'Copyright':
				case 'CopyrightNotice':
				case 'Rights':
					$metadata['copyright_notice'] = $value;
					break;

				case 'Credit':
					$metadata['credit'] = $value;
					break;

				case 'Artist':
				case 'Creator':
					$metadata['creator'] = $value;
					break;

				case 'ApertureValue':
				case 'MaxApertureValue':
					$parts = explode('/', $value);
					$metadata['aperture_value'] = round(exp(($parts[0] / $parts[1]) * 0.51 * log(2)), 1);
					break;

				case 'ShutterSpeedValue':
					$metadata['shutter_speed_value'] = $this->formatShutterSpeedValue($value);
					break;

				case 'ISOSpeedRatings':
					$metadata['iso_speed_ratings'] = $value;
					break;

				case 'FocalLength':
					$parts = explode('/', $value);
					$metadata['focal_length'] = $parts[0] / $parts[1];
					break;

				case 'CameraModel':
				case 'Model':
					$metadata['camera_model'] = $value;
					break;

				case 'Flash':
					$metadata['flash'] = (int)$value;
					break;

				case 'MeteringMode':
					$metadata['metering_mode'] = (int)$value;
					break;

				case 'ColorSpace':
					$metadata['color_space'] = $this->getColorSpace($value);
					break;

				case 'HorizontalResolution':
				case 'XResolution':
					$metadata['horizontal_resolution'] = $this->fractionToInteger($value);
					break;
				case 'VerticalResolution':
				case 'YResolution':
					$metadata['vertical_resolution'] = $this->fractionToInteger($value);
					break;

				case 'GPS':
					if (is_array($value)) {
						$metadata['latitude'] = $this->parseGpsCoordinate($value['GPSLatitude'], $value['GPSLatitudeRef']);
						$metadata['longitude'] = $this->parseGpsCoordinate($value['GPSLongitude'], $value['GPSLongitudeRef']);
					}
					break;

				case 'City':
					$metadata['location_city'] = $value;
					break;

				case 'Country':
					$metadata['location_country'] = $value;
					break;

				case 'CreatorTool':
				case 'Software':
					$metadata['creator_tool'] = $value;
					break;

				default:
			}
		}
	}

	/**
	 * Extract Iptc meta data
	 *
	 * @param $metadata array
	 * @param $info array
	 *
	 * @return void
	 */
	protected function extractIptcMetaData(&$metadata, $info) {
		if (!$this->isIptcExtensionAvailable()) {
			$this->getLogger()->warning('Function iptcparse() is not available.');
			return;
		}

		// Check if IPTC metadata exists
		if (isset($info['APP13'])) {
			$iptc = iptcparse($info['APP13']);

			// Parse metadata from IPTC APP13
			if (is_array($iptc)) {
				foreach ($this->iptcAttributesMapping as $attribute => $mediaField) {
					if (isset($iptc[$attribute])) {
						// store categories as comma separated values in DB
						if ($mediaField === 'keywords') {
							$metadata[$mediaField] = implode(',', $iptc[$attribute]);
						} else {
							$metadata[$mediaField] = $iptc[$attribute][0];
						}
					}
				}

				// check if data is already encoded as UTF-8
				if (empty($iptc['1#090'][0]) || $iptc['1#090'][0] != "\x1b\x25\x47") { // this is ESC%G
					$metadata = $this->getUnicodeUtility()->convertValues($metadata);
				}
			}
		}
	}

	/**
	 * @param $filename
	 * @return bool
	 */
	protected function isAllowedImageType($filename) {
		$imageType = null;

		if (@is_file($filename)) {
			$imageType = exif_imagetype($filename);
		}

		return in_array($imageType, $this->allowedImageTypes);
	}

	/**
	 * Check if needed exif method are available in PHP
	 *
	 * @return bool
	 */
	protected function isExifExtensionAvailable() {
		return (function_exists('exif_imagetype') && function_exists('exif_read_data'));
	}

	/**
	 * Check if needed iptc method are available in PHP
	 *
	 * @return bool
	 */
	protected function isIptcExtensionAvailable() {
		return function_exists('iptcparse');
	}

	/**
	 * Converting GPS
	 *
	 * @param array $value
	 * @param string $ref
	 *
	 * @return string|null
	 */
	protected function parseGpsCoordinate($value, $ref) {
		if (is_array($value)) {
			$processedValue = array();
			foreach ($value as $key => $item) {
				if (strpos($item, '/') !== FALSE) {
					$parts = GeneralUtility::trimExplode('/', $item);
					if (intval($parts[1])) {
						$processedValue[$key] = (int)($parts[0] / $parts[1]);
					} else {
						$processedValue[$key] = (int)$parts[0];
					}
				}
			}
			$neutralValue = $processedValue[0] + ((($processedValue[1] * 60) + ($processedValue[2])) / 3600);
			$value = ($ref === 'N' || $ref === 'E') ? $neutralValue : '-' . $neutralValue;
		}

		return ($value === null || $value === '' || $value == 0) ? '0.00000000000000' : (string)$value;
	}

	/**
	 * Calculates a fraction.
	 *
	 * @param string $fraction
	 * @return int
	 */
	protected function fractionToInteger($fraction) {
		if (strpos($fraction, '/') !== FALSE) {
			$fractionParts = explode('/', $fraction);
			$integer = intval($fractionParts[0] / $fractionParts[1]);
		} else {
			$integer = intval($fraction);
		}
		return $integer;
	}

	/**
	 * Format shutter speed value.
	 * To convert this value to ordinary 'Shutter Speed'; calculate this value's power of 2, then reciprocal.
	 * For example, if value is '4', shutter speed is 1/(2^4)=1/16 second.
	 *
	 * @param string $shutterSpeedValue
	 * @return string
	 */
	protected function formatShutterSpeedValue($shutterSpeedValue) {
		if (preg_match('/^1\//', $shutterSpeedValue) !== 1) {
			if (strpos($shutterSpeedValue, '/') !== FALSE) {
				$parts = explode('/', $shutterSpeedValue);
				if (intval($parts[1])) {
					$shutterSpeedValue = '1/' . (int)pow(2, $parts[0] / $parts[1]);
				}
			}
		}
		return $shutterSpeedValue;
	}

	/**
	 * Converts the color space id to the value in Media Management
	 *
	 * @param int $value
	 *
	 * @return string
	 */
	protected function getColorSpace($value) {
		if (array_key_exists($value, $this->colorSpaceToNameMapping)) {
			$value = $this->colorSpaceToNameMapping[$value];
		} else {
            $value = '';
		}

		return $value;
	}
}
