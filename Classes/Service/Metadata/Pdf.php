<?php
namespace TYPO3\CMS\Metadata\Service\Metadata;

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

/**
 * Add auto-loader for Zend PDF library
 */
use TYPO3\CMS\Core\Service\AbstractService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Metadata\Utility\Unicode;

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('metadata') . '/Resources/Private/ZendPdf/vendor/autoload.php');

/**
 * @package metadata
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 */
class Pdf extends AbstractService {

	/**
	 * Same as class name
	 *
	 * @var string
	 */
	protected $prefixId = 'tx_metadata_service_metadata_pdf';

	/**
	 * Performs the service processing
	 *
	 * @return boolean
	 */
	public function process() {

		$this->out = array();

		if ($inputFile = $this->getInputFile()) {

			try {

				$pdf = \ZendPdf\PdfDocument::load($inputFile);

				if (is_object($pdf)) {

					$this->out['title'] = $pdf->properties['Title'];
					$this->out['creator'] = $pdf->properties['Author'];
					$this->out['description'] = $pdf->properties['Subject'];
					$this->out['keywords'] = $pdf->properties['Keywords'];
					$this->out['creator_tool'] = $pdf->properties['Creator'];
					$this->out['creation_date'] = $this->parsePdfDate($pdf->properties['CreationDate']);
					$this->out['modification_date'] = $this->parsePdfDate($pdf->properties['ModDate']);

					$this->out = Unicode::convert($this->out);
				}
			} catch (\Exception $e) {

				/** @var $loggerManager \TYPO3\CMS\Core\Log\LogManager */
				$loggerManager = GeneralUtility::makeInstance('TYPO3\CMS\Core\Log\LogManager');

				/** @var $logger \TYPO3\CMS\Core\Log\Logger */
				$message = sprintf('Metadata: PDF indexation raised an exception %s.', $e->getMessage());
				$loggerManager->getLogger(get_class($this))->warning($message);
			}

		} else {
			$this->errorPush(T3_ERR_SV_NO_INPUT, 'No or empty input.');
		}

		return $this->getLastError();
	}

	/**
	 * Convert a PDF date string into a timestamp
	 * PDF date: D:YYYYMMDDHHmmSSOHH'mm'
	 *
	 * @param string $pdfDate
	 * @return int
	 */
	protected function parsePdfDate($pdfDate) {

		// Remove starting D: if exists
		$pdfDate = preg_replace("/D:/", "", $pdfDate);

		// Split the PDF Date into two parts if a timezone indication exists (Z = time is indicated in UTC)
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

?>