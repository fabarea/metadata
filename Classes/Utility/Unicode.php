<?php
namespace Fab\Metadata\Utility;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Utility method for charset conversion
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
		$charsetConversionObject = GeneralUtility::makeInstance('TYPO3\CMS\Core\Charset\CharsetConverter');

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
