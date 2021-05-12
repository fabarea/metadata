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

use Fab\Metadata\Utility\UnicodeUtility;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Index\ExtractorInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Abstract service dealing with metadata extraction
 */
abstract class AbstractExtractor implements ExtractorInterface {

	/**
	 * Allowed file types
	 *
	 * @var array
	 */
	protected $allowedFileTypes = [];

	/**
	 * Allowed file extensions
	 *
	 * @var array
	 */
	protected $allowedFileExtensions = [];

	/**
	 * Returns an array of supported file types;
	 * An empty array indicates all filetypes
	 *
	 * @return array
	 */
	public function getFileTypeRestrictions(): array {
		return $this->allowedFileTypes;
	}

	/**
	 * Get all supported DriverClasses
	 * Since some extractors may only work for local files, and other extractors
	 * are especially made for grabbing data from remote.
	 * Returns array of string with driver names of Drivers which are supported,
	 * If the driver did not register a name, it's the classname.
	 * empty array indicates no restrictions
	 *
	 * @return array
	 */
	public function getDriverRestrictions(): array {
		return [];
	}

	/**
	 * Checks if the given file can be processed by this Extractor
	 *
	 * @param File $file
	 * @return boolean
	 */
	public function canProcess(File $file): bool {
		return TRUE;
	}

	/**
	 * Returns a logger instance
	 *
	 * @return Logger
	 */
	protected function getLogger(): Logger {
		$loggerManager = GeneralUtility::makeInstance(LogManager::class);

		return $loggerManager->getLogger(__CLASS__);
	}

	/**
	 * @return \Fab\Metadata\Utility\UnicodeUtility
	 */
	protected function getUnicodeUtility(): UnicodeUtility {
		return GeneralUtility::makeInstance(UnicodeUtility::class);
	}
}
