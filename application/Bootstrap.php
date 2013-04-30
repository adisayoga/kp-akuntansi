<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initView()
    {
    	// Initialize view
    	$view = new Zend_View();
    	$view->doctype("XHTML1_STRICT");
    	$view->headTitle("Sistem Informasi Akuntansi");
    	$view->headTitle()->setSeparator(" - ");
    	$view->skin = "default";
    	
    	$view->addHelperPath(APPLICATION_PATH . "/views/helpers", "Zend_View_Helper_");
    	
    	// JQuery
    	$view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
    	$view->jQuery()->setLocalPath("/js/jquery-1.4.2.min.js")
    	               ->setUiLocalPath("/js/jquery-ui-1.8.custom.min.js")
    	               ->addJavascriptFile("/js/jquery.layout.min.js")
    	               ->addJavascriptFile("/js/jquery.zend.jsonrpc.js")
    	               ->addJavascriptFile("/js/json2.js")
    	               ->addJavascriptFile("/js/common.js")
    	               ->enable()->uiEnable();
    	               
    	$view->addHelperPath("Acc/View/Helper", "Acc_View_Helper");
    	               
    	// Tambahkan ke ViewRenderer
    	$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper("viewRenderer");
    	$viewRenderer->setView($view);
    	
    	Zend_Paginator::setDefaultItemCountPerPage(14);
    	
    	// Kembalikan nilainya, sehingga bisa disimpan oleh bootstrap
    	return $view;
    }
    
    protected function _initAutoLoad()
    {
    	// Tambahkan autoloader
		$autoLoader = Zend_Loader_Autoloader::getInstance();
		$autoLoader->registerNamespace("Acc_");
    	
    	$resourceLoader = new Zend_Loader_Autoloader_Resource(array(
		    "basePath"      => APPLICATION_PATH,
		    "namespace"     => "",
		    "resourceTypes" => array(
		        "form"      => array("path" => "forms/", "namespace" => "Form_"),
		        "model"     => array("path" => "models/", "namespace" => "Model_")
		    ),
		));
		
		return $autoLoader;
    }
    
}

