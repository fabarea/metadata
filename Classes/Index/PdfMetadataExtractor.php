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

use TYPO3\CMS\Core\Resource\File;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use Fab\Metadata\Utility\Unicode;

// Add auto-loader for Zend PDF library
// TODO Use composer install on extension installation?
require_once(ExtensionManagementUtility::extPath('metadata') . '/Resources/Private/ZendPdf/vendor/autoload.php');

/**
 * Service dealing with metadata extraction of images.
 */
class PdfMetadataExtractor extends AbstractExtractor {

	/**
	 * Allowed file extensions
	 *
	 * @var array
	 */
	protected $allowedFileExtensions = array(
		'pdf',
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
		return 16;
	}

	/**
	 * Returns the execution priority of the extraction Service
	 * Should be between 1 and 100, 100 means runs as first service, 1 runs at last service
	 *
	 * @return integer
	 */
	public function getExecutionPriority() {
		return 16;
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
		$metadata = array();

		$this->extractPdfMetaData($metadata, $file->getForLocalProcessing());

		return Unicode::convert($metadata);
	}

	/**
	 * Extract PDF meta data
	 *
	 * @param array $metadata
	 * @param string $filename
	 *
	 * @return void
	 */
	public function extractPdfMetaData(&$metadata, $filename) {
		try {
			$pdf = new \ZendPdf\PdfDocument($filename, NULL, TRUE);

			$metadata['pages'] = count($pdf->pages);

			foreach ($pdf->properties as $detail => $value) {

				switch ($detail) {
					case 'Title':
						$metadata['title'] = $value;
						break;

					case 'Author':
						$metadata['creator'] = $value;
						break;

					case 'Subject':
						$metadata['description'] = $value;
						break;

					case 'Keywords':
						$metadata['keywords'] = $value;
						break;

					case 'Pages':
						$metadata['pages'] = (int) $value;
						break;

					case 'Producer':
					case 'Creator':
						$metadata['creator_tool'] = $value;
						break;

					case 'CreationDate':
						$metadata['creation_date'] = $this->parsePdfDate($value);
						break;

					case 'ModDate':
						$metadata['modification_date'] = $this->parsePdfDate($value);
						break;

					default:
				}
			}
		} catch (\Exception $e) {
			$message = sprintf('Metadata: PDF indexation raised an exception %s.', $e->getMessage());
			$this->getLogger()->warning($message);
		}
	}

	/**
	 * Convert a PDF date string into a timestamp
	 * PDF date: D:YYYYMMDDHHmmSSOHH'mm'
	 *
	 * @param string $pdfDate
	 * @return int
	 */
	protected function parsePdfDate($pdfDate) {

		// Remove starting D: if
		// TODO: what is this? A hack for windows?
		$pdfDate = preg_replace('/D:/', '', $pdfDate);

		// Split the PDF Date into two parts if a timezone indication exists
		// (Z = time is indicated in UTC)
		$pdfDateArray = preg_split("/(?=[-+Z]\d{2}'\d{2}')/", $pdfDate, -1);

		// Check if timezone indication exists
		if (isset($pdfDateArray[1])) {

			$timeOffset = preg_replace('[\D]', '', $pdfDateArray[1]);

			switch (substr($pdfDateArray[1], 0, 1)) {
				case '-':
					$timeOffset = '-' . $timeOffset;
					break;
				case '+':
					$timeOffset = '+' . $timeOffset;
					break;
				default:
			}
		}

		// Build an interpretable datetime
		if (isset($timeOffset)) {
			$pdfDate = $pdfDateArray[0] . $timeOffset;
			$pdfDateTimeFormat = \DateTime::createFromFormat('YmdGisO', $pdfDate);
		} else {
			$pdfDateTimeFormat = \DateTime::createFromFormat('YmdGis', $pdfDateArray[0]);
		}

		$pdfDateTime = NULL;
		if (is_object($pdfDateTimeFormat)) {
			// Form it to a UNIX timestamp
			$pdfDateTime = $pdfDateTimeFormat->format('U');
		}

		return $pdfDateTime;
	}
}
