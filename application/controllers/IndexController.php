<?php
class IndexController extends Zend_Controller_Action
{

    public function init() {}

    /**
     * Action index
     */
    public function indexAction() { }
    
    /**
     * Untuk mengetes
     */
    public function testAction()
    {
    	// Tambahkan link javascript untuk tree table
    	$this->view->JQuery()->addJavascriptFile("/js/jquery.jsonrpc.js");
    	
    	// Custom Decorator
    	$form = new Form_Registration();
    	if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
    		
    	}
    	$this->view->form = $form;
    	
    	var_dump($this->_getAllParams());
    }
    
    /**
     * Untuk mengetes tanggal
     */
    public function testDateAction() {}
}
