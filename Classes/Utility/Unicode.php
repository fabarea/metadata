<?php

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
 * Utiliy method for charset conversion 
 * 
 * @package metadata
 */
class Tx_Metadata_Utility_Unicode {

	/**
	 * Ouptut a UTF-8 array of metadata
	 *
	 * @param	array		the metadata array taken as input
	 * @return	array		the metadata converted into UTF-8
	 */
	static public function convert($metadata) {
		/* @var $charsetConversionObject t3lib_cs */
		$charsetConversionObject = t3lib_div::makeInstance('t3lib_cs');
		
		// iso-8859-1 is assumed to be the standard encoding for file metadata
		$inputEncoding = 'iso-8859-1';
		
		foreach ($metadata as $metadataKey => $metadataValue) {
			// @todo mb_detect_encoding seems to be pretty buggy. Check whether it should be replaced by something else...
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