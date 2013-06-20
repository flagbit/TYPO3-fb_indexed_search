<?php
namespace Flagbit\FbIndexedSearch\Xclass;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Michael Bentz (michael.bentz@flagbit.de)
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
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * XCLASS which overwrites the browsebox-links generated by indexed_search
 * 
 * @package TYPO3
 * @subpackage fb_indexed_search
 * @author	Michael Bentz <michael.bentz@flagbit.de>
 */
class SearchFormController extends \TYPO3\CMS\IndexedSearch\Controller\SearchFormController {

	/**
	 * @var $pointer_cObj \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
	 */
	protected $pointer_cObj;

	/**
	 * Generates the link for the result-browserbox.
	 * 
	 * Checks for given configuration for the browsebox-links in TypoScript.
	 * If no configuration is present, the output will be handled by the parent class
	 *
	 * @param	string		String to wrap in <a> tag
	 * @param	integer		Pointer value
	 * @param	string		List of integers pointing to free indexing configurations to search. -1 represents no filtering, 0 represents TYPO3 pages only, any number above zero is a uid of an indexing configuration!
	 * @return	string		Input string wrapped in <a> tag.
	 */
	function makePointerSelector_link($str,$p,$freeIndexUid) {

		$linkObject = $this->conf['search.']['pageBrowser.']['linkConfig'];
		$linkConfig = $this->conf['search.']['pageBrowser.']['linkConfig.'];

		if (!is_array($linkConfig)) {
			// no config present, redirect to parent class
			return parent::makePointerSelector_link($str, $p, $freeIndexUid);
		}

		// create a cObject for link-settings
		if (!$this->pointer_cObj) {
			$this->init_pointer_cObj();
		}

		// Set navigation variables
		$this->pointer_cObj->data['pageBrowser_pointer'] = $p;
		$this->pointer_cObj->data['pageBrowser_linkText'] = $str;
		$this->pointer_cObj->data['pageBrowser_freeIndexUid'] = $freeIndexUid;

		// Either generate the link as cObject (linkConfig = TEXT) or by directly calling typoLink(...)
		if (is_string($linkObject)) {
			return $this->pointer_cObj->cObjGetSingle($linkObject, $linkConfig);
		} else {
			return $this->pointer_cObj->typoLink($str, $linkConfig);
		}
	}

	/*
	 * Initializes a cObject which is used for generating the pagebrowser links
	 *
	 * @return void
	 */
	protected function init_pointer_cObj() {
		$this->pointer_cObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\ContentObject\\ContentObjectRenderer');

		// integrate form values if available
		foreach ($this->piVars as $key => $value) {
			$this->pointer_cObj->data['piVars_'.$key] = $value;
		}
		foreach ($this->internal as $key => $value) {
			$this->pointer_cObj->data['internal_'.$key] = $value;
		}
		$this->pointer_cObj->data['prefixId'] = $this->prefixId;
	}
}
?>