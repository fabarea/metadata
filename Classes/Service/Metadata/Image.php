<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Fabien Udriot <fabien.udriot@typo3.org>
 *  Lorenz Ulrich <lorenz.ulrich@visol.ch>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 *
 *
 * @package metadata
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Tx_Metadata_Service_Metadata_Image extends t3lib_svbase {
	protected $prefixId = 'tx_metadata_service_metadata_image';		// Same as class name
	protected $scriptRelPath = 'Classes/Service/Metadata/Image.php';	// Path to this script relative to the extension dir.
	protected $extKey = 'metadata';	// The extension key.

		// iso-8859-1 is assumed to be the standard encoding for file metadata
	protected $inputEncoding = 'iso-8859-1';

	/**
	 * Performs the service processing
	 *
	 * @return	boolean
	 */
	public function process()	{

		$this->out = array();

		if($inputFile = $this->getInputFile()) {

			$charsetConversionObject = t3lib_div::makeInstance('t3lib_cs');

				// Read basic metadata from file, write additional metadata to $info
			$imagesize = getimagesize($inputFile, $info);

				// Parse metadata from getimagesize
			$this->out = array(
				'width' => $imagesize['0'],
				'height' => $imagesize['1'],
					// Unit px is hardcoded for images
				'unit' => 'px',
			);

			if (isset($imagesize['channels'])) {
				$this->out['color_space'] = $this->getColorSpace($imagesize['channels']);
			}

				// Makes sure the function exists otherwise generates a log entry
			if (function_exists('exif_read_data')) {

				$exif = exif_read_data($inputFile, 0, TRUE);

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
			else {
				t3lib_div::devLog('Function exif_read_data() is not available. Make sure Mbstring and Exif module are loaded.', 2);
			}

				// Check if IPTC metadata exists
			if (isset($info['APP13'])) {

				$iptc = iptcparse($info['APP13']);

			}

				// Parse metadata from IPTC APP13
			if (is_array($iptc)) {

				$iptcAttributes = array(
					'2#005'	=> 'title',
					'2#120'	=> 'caption',
					'2#025'	=> 'keywords',
					'2#085'	=> 'author',
					'2#115'	=> 'publisher',
					'2#080'	=> 'creator',
					'2#116'	=> 'copyright_notice',
					'2#100'	=> 'location_country',
					'2#090'	=> 'location_city',
					'2#055'	=> 'creation_date',
				);

				foreach($iptcAttributes as $iptcAttribute => $mediaField) {

					if (isset($iptc[$iptcAttribute])) {

						$this->out[$mediaField] = $iptc[$iptcAttribute][0];

					}

				}

			}

				// Convert each metadata value from its encoding to utf-8
			foreach ($this->out as $metadataKey => $metadataValue) {
				if (mb_detect_encoding($metadataValue === 'UTF-8')) {
					$this->inputEncoding = 'utf-8';
				}
				$charsetConversionObject->conv($this->out[$metadataKey], $this->inputEncoding, 'utf-8');
			}


		} else {
			$this->errorPush(T3_ERR_SV_NO_INPUT, 'No or empty input.');
		}

		return $this->getLastError();
	}

	/**
	 * Converting GPS
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

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/media/Classes/Service/Pdf.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/media/Classes/Service/Pdf.php']);
}

?>