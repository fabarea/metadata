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

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Utility method for charset conversion
 */
class UnicodeUtility implements SingletonInterface {

	/**
	 * @var \TYPO3\CMS\Core\Charset\CharsetConverter
	 */
	protected $charsetConverter;

	/**
	 * @param array $metadata
	 * @return array
	 */
	public function convertValues(array $metadata) {

		foreach ($metadata as $key => $value) {
			$metadata[$key] = $this->convert($value);
		}

		return $metadata;
	}

	/**
	 * @param string $value
	 * @return string
	 */
	public function convert($value) {

		// iso-8859-15 is assumed to be the standard encoding for file metadata
		$inputEncoding = 'iso-8859-15';

		// This function would also do the job, in case: mb_convert_encoding($value, 'UTF-8', 'auto')
		return $this->getCharsetConverter()->conv($value, $inputEncoding, 'utf-8');
	}

	/**
	 * @return \TYPO3\CMS\Core\Charset\CharsetConverter
	 */
	protected function getCharsetConverter() {
		if (is_null($this->charsetConverter)) {
			$this->charsetConverter = GeneralUtility::makeInstance('TYPO3\CMS\Core\Charset\CharsetConverter');
		}
		return $this->charsetConverter;
	}
}
