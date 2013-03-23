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

			$inputFilePath = $fileObject->getForLocalProcessing($writable = FALSE);
			$inputFilePath = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($inputFilePath);

			// find a service for that file type
			/** @var $serviceObject \TYPO3\CMS\Metadata\Service\Metadata\Pdf */
			$serviceObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstanceService('metaExtract', $fileObject->getMimeType());

			if (is_object($serviceObject)) {

				// Notice: get the asset to have more metadata from method getProperties()
				// This can probably be removed when FAL will have a more advance handling of properties.

				/** @var $assetRepository \TYPO3\CMS\Media\Domain\Repository\AssetRepository */
				$assetRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Domain\Repository\AssetRepository');
				$assetObject = $assetRepository->findByUid($fileObject->getUid());
				if (is_object($assetObject)) {

					$serviceObject->setInputFile($inputFilePath, $assetObject->getMimeType());
					$serviceObject->process();

					$properties = $assetObject->getProperties();
					$updatedProperties = array();

					$metadata = $serviceObject->getOutput();

					// try to guess a title according to the file name
					if (empty($metadata['title'])) {
						$metadata['title'] = $this->guessTitle($fileObject->getName());
					}

					foreach ($metadata as $key => $value) {
						// there are some conditions to have metadata filling the asset
						// 1. the property name must exist in Asset
						// 2. the property value must be empty
						// 3. $value must have a value
						if (isset($properties[$key]) && empty($properties[$key]) && $value) {
							$updatedProperties[$key] = $value;
						}
					}

					$assetObject->updateProperties($updatedProperties);
					$assetRepository->update($assetObject);
				}
			}
		}
	}

	/**
	 * Guess a title given a file name.
	 *
	 * @param string $fileName
	 * @return string
	 */
	public function guessTitle($fileName){
		$info = pathinfo($fileName);
		$fileNameWithoutExtension = basename($fileName, '.' . $info['extension']);
		$titleProvisional = preg_replace('/-|_/is', ' ', $fileNameWithoutExtension);
		return trim(preg_replace("([A-Z])", " $0", $titleProvisional));
	}
}

?>