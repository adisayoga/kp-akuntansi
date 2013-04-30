<?php
/**
 * Class untuk me-load skin
 */
class Zend_View_Helper_LoadSkin extends Zend_View_Helper_Abstract
{
	/**
	 * Konstruktor
	 * @param $skin
	 */
	public function loadSkin($skin)
	{
		// Load skin dari file configurasi
		$skinData = new Zend_Config_Xml("./skins/$skin/skin.xml");
		$stylesheets = $skinData->stylesheets->stylesheet->toArray();
		
		// Menambahkan masing-masing stylesheet
		if (is_array($stylesheets)) {
			foreach ($stylesheets as $stylesheet) {
				$this->view->headLink()->appendStylesheet("/skins/$skin/css/$stylesheet");
			}
		}
	}
}