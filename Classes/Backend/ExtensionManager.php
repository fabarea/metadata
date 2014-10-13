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
	 * @param object $tsObj t3lib_tsStyleConfig
	 * @return string the HTML message
	 */
	public function renderMessage(&$params, &$tsObj) {
		$out = '';

		$out .= '
		<div style="">
			<div class="typo3-message message-' . $this->getExifClassName() . '">
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
			<div class="typo3-message message-' . $this->getIptcClassName() . '">
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
	 * Check if needed exif method are available in PHP
	 *
	 * @return bool
	 */
	protected function isExifExtensionAvailable() {
		return (function_exists('exif_imagetype') && function_exists('exif_read_data'));
	}

	/**
	 * Check if needed exif method are available in PHP
	 *
	 * @return string
	 */
	protected function getExifClassName() {
		return $this->isExifExtensionAvailable() ? 'ok' : 'warning';
	}

	/**
	 * Check if needed exif method are available in PHP
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
	 * Check if needed iptc method are available in PHP
	 *
	 * @return bool
	 */
	protected function isIptcExtensionAvailable() {
		return function_exists('iptcparse');
	}

	/**
	 * Check if needed exif method are available in PHP
	 *
	 * @return string
	 */
	protected function getIptcClassName() {
		return $this->isExifExtensionAvailable() ? 'ok' : 'warning';
	}

	/**
	 * Check if needed exif method are available in PHP
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
