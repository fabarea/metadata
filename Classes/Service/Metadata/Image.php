<?php
namespace Fab\Metadata\Service\Metadata;

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
use TYPO3\CMS\Core\Service\AbstractService;

/**
 * @package metadata
 */
class Image extends AbstractService {

	/**
	 * Same as class name
	 *
	 * @var string
	 */
	protected $prefixId = 'tx_metadata_service_metadata_image';

	/**
	 * Path to this script relative to the extension dir.
	 *
	 * @var string
	 */
	protected $scriptRelPath = 'Classes/Service/Metadata/Image.php';

	/**
	 * The extension key.
	 *
	 * @var string
	 */
	protected $extKey = 'metadata';

	/**
	 * Same as class name
	 *
	 * @var string
	 */
	protected $allowedImageTypes = array(IMAGETYPE_JPEG, IMAGETYPE_TIFF_II, IMAGETYPE_TIFF_MM);

	/**
	 * Performs the service processing
	 *
	 * @return    boolean
	 */
	public function process() {

		$this->out = array();

		if ($inputFile = $this->getInputFile()) {

			// Read basic metadata from file, write additional metadata to $info
			$imageSize = getimagesize($inputFile, $info);

			// Parse metadata from getimagesize
			$this->out['unit'] = 'px';

			if (isset($imageSize['channels'])) {
				$this->out['color_space'] = $this->getColorSpace($imageSize['channels']);
			}

			// Makes sure the function exists otherwise generates a log entry
			if (function_exists('exif_imagetype') && function_exists('exif_read_data')) {

				// Determine image type
				$imageType = exif_imagetype($inputFile);

				// Only try to read exif data for supported types
				if (in_array($imageType, $this->allowedImageTypes)) {

					$exif = @exif_read_data($inputFile, 0, TRUE);

					// Parse metadata from EXIF GPS block
					if (is_array($exif['GPS'])) {
						$this->out['latitude'] = $this->parseGPSCoordinate($exif['GPS']['GPSLatitude'], $exif['GPS']['GPSLatitudeRef']);;
						$this->out['longitude'] = $this->parseGPSCoordinate($exif['GPS']['GPSLongitude'], $exif['GPS']['GPSLongitudeRef']);;
					}

					// Parse metadata from EXIF EXIF block
					if (is_array($exif['EXIF'])) {
						$this->out['creation_date'] = strtotime($exif['EXIF']['DateTimeOriginal']);
					}

					// Parse metadata from EXIF IFD0 block
					if (is_array($exif['IFD0'])) {

						foreach ($exif['IFD0'] as $exifAttribute => $value) {

							switch ($exifAttribute) {

								case 'XResolution' :
									$this->out['horizontal_resolution'] = $this->fractionToInt($value);
									break;
								case 'YResolution' :
									$this->out['vertical_resolution'] = $this->fractionToInt($value);
									break;
								case 'Subject' :
									$this->out['description'] = $value;
									break;
								case 'DateTime' :
									$this->out['modification_date'] = strtotime($value);
									break;
								case 'Software' :
									$this->out['creator_tool'] = $value;
									break;
							}
						}
					}
				}
			} else {
				\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Function exif_imagetype() and exif_read_data() are not available. Make sure Mbstring and Exif module are loaded.', 2);
			}

			// Check if IPTC metadata exists
			if (isset($info['APP13'])) {
				$iptc = iptcparse($info['APP13']);
			}

			// Parse metadata from IPTC APP13
			if (is_array($iptc)) {

				$iptcAttributes = array(
					'2#005' => 'title',
					'2#120' => 'caption',
					'2#025' => 'keywords',
					'2#085' => 'author',
					'2#115' => 'publisher',
					'2#080' => 'creator',
					'2#116' => 'copyright_notice',
					'2#100' => 'location_country',
					'2#090' => 'location_city',
					'2#055' => 'creation_date',
				);

				foreach ($iptcAttributes as $iptcAttribute => $mediaField) {
					if (isset($iptc[$iptcAttribute])) {
						$this->out[$mediaField] = $iptc[$iptcAttribute][0];
					}
				}
			}

			// Convert each metadata value from its encoding to utf-8
			$this->out = \Fab\Metadata\Utility\Unicode::convert($this->out);

		} else {
			$this->errorPush(T3_ERR_SV_NO_INPUT, 'No or empty input.');
		}

		return $this->getLastError();
	}

	/**
	 * Converting GPS
	 *
	 * @param array $value
	 * @param string $ref
	 * @return string
	 */
	protected function parseGPSCoordinate($value, $ref) {

		if (is_array($value)) {

			$neutralValue = $value[0] + ((($value[1] * 60) + ($value[2])) / 3600);
			$value = ($ref === 'N' || $ref === 'E') ? $neutralValue : '-' . $neutralValue;

		}

		return (string) $value;
	}

	/**
	 * Calculates a fraction
	 */
	protected function fractionToInt($fraction) {

		$fractionParts = explode('/', $fraction);
		return intval($fractionParts[0] / $fractionParts[1]);

	}

	/**
	 * Converts the color space id to the value in Media Management
	 *
	 * @param int $value
	 * @return int
	 */
	protected function getColorSpace($value) {

		$colorSpaceToName = array(
			'0' => 'grey',
			'2' => 'RGB',
			'3' => 'RGB',
			'4' => 'grey',
			'6' => 'RGB',
		);

		return $colorSpaceToName[$value];

	}
}

?>