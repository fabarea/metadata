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
 * Extension Configuration integration
 */
class ExtensionManager {

	/**
	 * Display a message in the Extension Configuration whether the PHP extraction availability for IPTC/EXIF is OK or not.
	 *
	 * @return string the HTML message
	 */
	public function renderMessage(): string {
		return '
		<div>
			<div class="alert alert-' . $this->getExifClassName() . '">
				' . $this->getExifMessage() . '
			</div>
			<div class="alert alert-' . $this->getIptcClassName() . '">
				' . $this->getIptcMessage() . '
			</div>
		</div>
		';
	}

	/**
	 * Check if functions "exif" are available in PHP.
	 *
	 * @return bool
	 */
	protected function isExifExtensionAvailable(): bool {
		return function_exists('exif_imagetype') && function_exists('exif_read_data');
	}

	/**
	 * Return the according class name whether extension "exif" is available.
	 *
	 * @return string
	 */
	protected function getExifClassName(): string {
		return $this->isExifExtensionAvailable() ? 'success' : 'warning';
	}

	/**
	 * Return the according message whether extension "exif" is available.
	 *
	 * @return string
	 */
	protected function getExifMessage(): string {
		return 'PHP EXIF extension is ' . ($this->isExifExtensionAvailable() ? 'well' : 'not') . ' installed.';
	}

	/**
	 * Check if functions "iptc" are available in PHP.
	 *
	 * @return bool
	 */
	protected function isIptcExtensionAvailable(): bool {
		return function_exists('iptcparse');
	}

	/**
	 * Return the according class name whether extension "iptc" is available.
	 *
	 * @return string
	 */
	protected function getIptcClassName(): string {
		return $this->isIptcExtensionAvailable() ? 'success' : 'warning';
	}

	/**
	 * Return the according message whether extension "iptc" is available.
	 *
	 * @return string
	 */
	protected function getIptcMessage(): string {
		return 'PHP IPTC extension is ' . ($this->isIptcExtensionAvailable() ? 'well' : 'not') . ' installed.';
	}

}
