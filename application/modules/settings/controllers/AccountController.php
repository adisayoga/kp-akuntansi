<?php
class Settings_AccountController extends Zend_Controller_Action
{
	public function indexAction()
	{
		
	}
	
	public function rebuildTreeAction()
	{
		$accountModel = new Model_Account();
		$accountModel->rebuildTree();
	}
}