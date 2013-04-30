<?php
/**
 * ACL Controller Plug-in.
 * @author Adi Sayoga
 */
class Acc_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		// TODO hard code, seharusnya role disimpan di-database
		
		// Set up acl
		$acl = new Zend_Acl();
		
		// Tambahkan resources
		$resources = array(
			"index", "error", "settings", "user", "account", "journal", "journal-penyesuaian", "laporan");
		
		foreach ($resources as $resourceId => $resource) {
			$acl->addResource(new Zend_Acl_Resource($resource));
		}
		
		// Tambahkan role
		$roles = array(
			array("roleId" => "guest", "parent" => null), 
			array("roleId" => "user", "parent" => "guest"), 
			array("roleId" => "admin", "parent" => "user"));
		
		foreach ($roles as $role) {
			$acl->addRole($role["roleId"], $role["parent"]);
		}
		
		$acl->allow(null, "error");
		
		// Guest hanya bisa mengakses login
		$acl->allow("guest", "user", array("login", "logout"));
		
		// User hanya bisa mengakses account, journal, dan laporan
		$acl->allow("user", array("index", "account", "journal", "journal-penyesuaian", "laporan"));
		
		// Administrator bisa melakukan apa saja
		$acl->allow("admin", null);
		
		// Fetch user saat ini
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			$role = strtolower($identity->role);
		} else {
			$role = "guest";
		}
		
		$controller = $request->controller;
		$action = $request->action;
		
		if (!$acl->isAllowed($role, $controller, $action)) {
			if ($role == "guest") {
				$request->setControllerName("user");
				$request->setActionName("login");
				
				// tambahkan referensi request pada login sehingga jika login berhasil maka akan 
				// kembali ke halaman sebelumnya yang direquest
				if ($controller != "user") $request->setParam("ref", $controller . "/" . $action);
			} else {
				$request->setControllerName("error");
				$request->setActionName("noauth");
			}
		}
	}
}