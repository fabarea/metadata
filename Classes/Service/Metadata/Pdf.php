<?php
namespace TYPO3\CMS\Metadata\Service\Metadata;
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

// Add auto-loader for Zend PDF library
require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('metadata') . '/Resources/Private/ZendPdf/vendor/autoload.php');

/**
 *
 *
 * @package metadata
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License, version 3 or later
 *
 */
class Pdf extends \TYPO3\CMS\Core\Service\AbstractService {

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
	public function process()	{

		$this->out = array();

		if($inputFile = $this->getInputFile()) {

			$pdf = \ZendPdf\PdfDocument::load($inputFile);

			if (is_object($pdf)) {

				$this->out['title'] = $pdf->properties['Title'];
				$this->out['creator'] = $pdf->properties['Author'];
				$this->out['description'] = $pdf->properties['Subject'];
				$this->out['keywords'] = $pdf->properties['Keywords'];
				$this->out['creator_tool'] = $pdf->properties['Creator'];
				$this->out['creation_date'] = $this->parsePdfDate($pdf->properties['CreationDate']);
				$this->out['modification_date'] = $this->parsePdfDate($pdf->properties['ModDate']);

				$this->out = \TYPO3\CMS\Metadata\Utility\Unicode::convert($this->out);
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
	protected function parsePdfDate($pdfDate)	{

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