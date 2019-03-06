<?php
namespace Fab\Metadata\Backend;

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
 * Extension Manager integration
 */
class ExtensionManager {

	/**
	 * Display a message to the Extension Manager whether the configuration is OK or KO.
	 *
	 * @param array $params
	 * @param \TYPO3\CMS\Core\TypoScript\ConfigurationForm $tsObj
	 * @return string the HTML message
	 */
	public function renderMessage(&$params, &$tsObj) {
		$out = '';

		$out .= '
		<div style="">
			<div class="typo3-message message-' . $this->getExifClassName() . ' alert alert-' . $this->getExifClassName() . '">
				<div class="message-header">
					PHP EXIF extension
				</div>
				<div class="message-body">
					' . $this->getExifMessage() . '
				</div>
			</div>
		</div>
		';

		$out .= '
		<div style="">
			<div class="typo3-message message-' . $this->getIptcClassName() . ' alert alert-' . $this->getIptcClassName() . '">
				<div class="message-header">
					PHP IPTC extension
				</div>
				<div class="message-body">
					' . $this->getIptcMessage() . '
				</div>
			</div>
		</div>
		';


		return $out;
	}

	/**
	 * Check if functions "exif" are available in PHP.
	 *
	 * @return bool
	 */
	protected function isExifExtensionAvailable() {
		return (function_exists('exif_imagetype') && function_exists('exif_read_data'));
	}

	/**
	 * Return the according class name whether extension "exif" is available.
	 *
	 * @return string
	 */
	protected function getExifClassName() {
		if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 7000000) {
			return $this->isExifExtensionAvailable() ? 'success' : 'warning';
		} else {
			return $this->isExifExtensionAvailable() ? 'ok' : 'warning';
		}
	}

	/**
	 * Return the according message whether extension "exif" is available.
	 *
	 * @return string
	 */
	protected function getExifMessage() {
		$message = "EXIF extension is well installed.";
		if (!$this->isExifExtensionAvailable()) {
			$message = "EXIF extension is not installed.";
		}
		return $message;
	}

	/**
	 * Check if functions "iptc" are available in PHP.
	 *
	 * @return bool
	 */
	protected function isIptcExtensionAvailable() {
		return function_exists('iptcparse');
	}

	/**
	 * Return the according class name whether extension "iptc" is available.
	 *
	 * @return string
	 */
	protected function getIptcClassName() {
		if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) >= 7000000) {
			return $this->isExifExtensionAvailable() ? 'success' : 'warning';
		} else {
			return $this->isExifExtensionAvailable() ? 'ok' : 'warning';
		}
	}

	/**
	 * Return the according message whether extension "iptc" is available.
	 *
	 * @return string
	 */
	protected function getIptcMessage() {
		$message = "IPTC extension is well installed.";
		if (!$this->isExifExtensionAvailable()) {
			$message = "IPTC extension is not installed.";
		}
		return $message;
	}

}
