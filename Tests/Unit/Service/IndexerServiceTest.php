<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Media development team <typo3-project-media@lists.typo3.org>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class \Fab\Metadata\Service\IndexerService.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @subpackage metadata
 */
class IndexerServiceTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \Fab\Metadata\Service\IndexerService
	 */
	private $fixture;

	public function setUp() {
		$this->fixture = new \Fab\Metadata\Service\IndexerService();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 * @dataProvider fileNameProvider
	 */
	public function guessTitleReturnsExpectedTitleFromProvider($fileName, $expected) {
		$this->assertEquals($expected, $this->fixture->guessTitle($fileName));

	}

	/**
	 * Provider
	 */
	public function fileNameProvider() {
		return array(
			array('foo.jpg', 'foo'),
			array('foo-bar.jpg', 'foo bar'),
			array('foo_bar.jpg', 'foo bar'),
			array('FooBar.jpg', 'Foo Bar'),
		);
	}
}
?>