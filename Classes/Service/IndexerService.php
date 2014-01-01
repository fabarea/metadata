<?php
namespace TYPO3\CMS\Metadata\Service;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Fabien Udriot <fabien.udriot@typo3.org>
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
 * @package metadata
 */
class IndexerService {

	/**
	 * Performs the service processing
	 *
	 * @param \TYPO3\CMS\Core\Resource\File $fileObject
	 * @param array $fileInfo
	 * @return void
	 */
	public function postFileIndex(\TYPO3\CMS\Core\Resource\File $fileObject, $fileInfo = array()) {

		if ($fileObject->isIndexed()) {

			/** @var $objectManager \TYPO3\CMS\Extbase\Object\ObjectManager */
			$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

			/** @var $assetRepository \TYPO3\CMS\Media\Domain\Repository\AssetRepository */
			$assetRepository = $objectManager->get('TYPO3\CMS\Media\Domain\Repository\AssetRepository');

			/** @var $serviceObject \TYPO3\CMS\Metadata\Service\Metadata\Pdf */
			$serviceObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstanceService('metaExtract', $fileObject->getMimeType());

			if (is_object($serviceObject) && $this->isMemorySufficient($fileObject)) {

				$inputFilePath = $fileObject->getForLocalProcessing($writable = FALSE);
				$inputFilePath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($inputFilePath);

				// Notice: get the asset to have more metadata from method getProperties()
				// This can probably be removed when FAL will have a more advance handling of properties.
				$assetObject = $assetRepository->findByUid($fileObject->getUid());
				if (is_object($assetObject)) {

					$serviceObject->setInputFile($inputFilePath, $assetObject->getMimeType());
					$serviceObject->process();
					$properties = $assetObject->getProperties();

					$values = array();

					$metadata = $serviceObject->getOutput();

					foreach ($metadata as $key => $value) {
						// there are some conditions to have metadata filling the asset
						// 1. the property name must exist in Asset
						// 2. the property value must be empty
						// 3. $value must have a value
						if (isset($properties[$key]) && empty($properties[$key]) && $value) {
							$values[$key] = $value;
						}
					}

					$assetObject->updateProperties($values);
					$assetRepository->update($assetObject);
				}
			}

			// In any case update title if remains empty. Do it even if no metadata service was found.
			if (!$fileObject->getProperty('title')) {

				$values = array();

				// Guess a title according to the file name.
				$values['title'] = $this->guessTitle($fileObject->getName());

				$fileObject->updateProperties($values);
				$assetRepository->update($fileObject);
			}
		}
	}

	/**
	 * Tell if the memory is sufficient to proceed of metadata extraction.
	 * It has been seen the PDF parser consuming all resources, prevent this!
	 *
	 * @param \TYPO3\CMS\Core\Resource\File $fileObject
	 * @return bool
	 */
	protected function isMemorySufficient($fileObject) {
		$memoryLimit = ini_get('memory_limit');
		$memorySufficient = $this->transformToBytes($memoryLimit) * 0.75 > memory_get_usage();

		// If the memory is not sufficient but the file is not a PDF, metadata extraction is still allowed.
		if (! $memorySufficient && $fileObject->getMimeType() !== 'application/pdf') {
			$memorySufficient = TRUE;
		}
		return $memorySufficient;
	}

	/**
	 * Transform a human size into bytes
	 *
	 * @param $val
	 * @return int|string
	 */
	protected function transformToBytes($val) {
		$val = trim($val);
		$last = strtolower($val[strlen($val) - 1]);
		switch ($last) {
			// The 'G' modifier is available since PHP 5.1.0
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}

		return $val;
	}

	/**
	 * Guess a title given a file name.
	 *
	 * @param string $fileName
	 * @return string
	 */
	public function guessTitle($fileName){
		$fileNameWithoutExtension = $this->removeExtension($fileName);

		$title = $fileNameWithoutExtension;
		// first case: the name is separated by _ or -
		// second case: this is an upper camel case name
		if (preg_match('/-|_/is', $fileNameWithoutExtension)) {
			$title = preg_replace('/-|_/is', ' ', $fileNameWithoutExtension);
		} elseif (preg_match('/[A-Z]/', $fileNameWithoutExtension)) {
			$parts = preg_split('/(?=[A-Z])/', $fileNameWithoutExtension, -1, PREG_SPLIT_NO_EMPTY);
			$title = implode(' ', $parts);
		}

		// remove double space
		return preg_replace('/\s+/', ' ', $title);
	}

	/**
	 * Remove extension of a file.
	 *
	 * @param string $fileName
	 * @return string
	 */
	protected function removeExtension($fileName){
		$parts = explode('.', $fileName);
		if (!empty($parts)) {
			array_pop($parts);
		}
		return implode('.', $parts);
	}
}

?>