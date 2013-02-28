<?php
namespace TYPO3\CMS\Metadata\Utility;
/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2011
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
 * ************************************************************* */

/**
 * Utility method for charset conversion
 *
 * @package metadata
 */
class Unicode {

	/**
	 * Output a UTF-8 array of metadata
	 *
	 * @param array $metadata the metadata array taken as input
	 * @return array
	 */
	static public function convert($metadata) {

		/* @var $charsetConversionObject \TYPO3\CMS\Core\Charset\CharsetConverter */
		$charsetConversionObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Charset\CharsetConverter');

		// iso-8859-1 is assumed to be the standard encoding for file metadata
		$inputEncoding = 'iso-8859-1';

		foreach ($metadata as $metadataKey => $metadataValue) {
			// check out comment at http://php.net/manual/en/function.mb-detect-encoding.php
			if (mb_detect_encoding($metadataValue, 'UTF-8', true)) {
				$inputEncoding = 'utf-8';
			}

			$metadata[$metadataKey] = $charsetConversionObject->conv($metadata[$metadataKey], $inputEncoding, 'utf-8');
		}

		return $metadata;
	}
}
?>