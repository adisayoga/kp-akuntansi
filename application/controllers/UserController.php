<?php

class UserController extends Zend_Controller_Action
{

    public function init() 
    {
    	//$this->view->headTitle("Data User");
    }
    
    /**
     * Index
     */
    public function indexAction()
    {
    	// Tambahkan link javascript
    	$this->view->JQuery()->addJavascriptFile("/js/jquery.pagination.js");
    	
        $userModel = new Model_User();
        $adapter = $userModel->fetchPaginatorAdapter();
    	$paginator = new Zend_Paginator($adapter);
    	$paginator->setPageRange(5);
    	
    	$page = $this->getRequest()->getParam("page", 1);
    	$paginator->setCurrentPageNumber($page);
    	$this->view->paginator = $paginator;
    }

    /**
     * Halaman untuk menambah user data baru.
     */
    public function createAction()
    {
    	// Tambahkan link javascript
    	$this->view->JQuery()->addJavascriptFile("/js/jquery.formValidation.js");
    	
    	$userForm = new Form_User();
    	$userForm->setAction("/user/create");
    	$userForm->setMethod("post");
    	
    	if ($this->getRequest()->isPost() && $userForm->isValid($_POST)) {
    		$userModel = new Model_User();
    		$result = $userModel->createNew(array(
    			"username"    => $userForm->getValue("username"), 
    			"password"    => $userForm->getValue("password"), 
    			"displayName" => $userForm->getValue("displayName"), 
    			"role"        => $userForm->getValue("role")
    		));
    		
    		if ($result) {
	            // Setelah disimpan, kosongkan kembali formnya
	            $userForm->username->setValue("");
	            $userForm->password->setValue("");
	            $userForm->displayName->setValue("");
	            $userForm->role->setValue("");
	            
	            $this->view->message = "Data user berhasil ditambahkan.";
    		} else {
    			$this->view->message = "Data user gagal ditambahkan, cek kembali inputan!";
    		}
    	}
    	$this->view->form = $userForm;
    }
    
	/**
	 * Halaman untuk mengubah data user.
	 */
    public function updateAction()
    {
    	// Tambahkan link javascript
    	$this->view->JQuery()->addJavascriptFile("/js/jquery.formValidation.js");
    	
    	$id = $this->getRequest()->getParam("id");
    	$userModel = new Model_User();
    	$user = $userModel->find($id)->current();
    	if (!$user) throw new Zend_Exception("User tidak ditemukan.");
    	
    	$userForm = new Form_User();
    	$userForm->setAction("/user/update");
    	$userForm->setMethod("post");
    	$userForm->removeElement("password");
    	$userForm->setExclude($user->username);
    	
    	if ($this->getRequest()->isPost()) {
    		if ($userForm->isValid($_POST)) {
	    		$result = $userModel->updateData($userForm->getValue("id"), array(
	    			"username"    => $userForm->getValue("username"), 
	    			"password"    => $userForm->getValue("password"), 
	    			"displayName" => $userForm->getValue("displayName"), 
	    			"role"        => $userForm->getValue("role")
	    		));
	    		return $this->_forward("index");
    		}
    	} else {
	    	$userForm->populate($user->toArray());
    	}
    	
    	$this->view->form = $userForm;
	    $this->view->lastUsername = $user->username;
    }
    
    /**
     * Halaman untuk mengubah password.
     */
    public function updatePasswordAction()
    {
    	// Tambahkan link javascript
    	$this->view->JQuery()->addJavascriptFile("/js/jquery.formValidation.js");
    	
    	$userModel = new Model_User();
    	$userForm = new Form_User();
    	$userForm->setAction("/user/update-password");
    	
    	// Hapus field yang tidak diperlukan
    	$userForm->removeElement("username");
    	$userForm->removeElement("displayName");
    	$userForm->removeElement("role");
    	
    	if ($this->getRequest()->isPost() && $userForm->isValid($_POST)) {
    		$result = $userModel->updatePassword(
    			$userForm->getvalue("id"),
    			$userForm->getValue("password")
    		);
    		return $this->_forward("index");
    	} else {
    		$id = $this->getRequest()->getParam("id");
    		$user = $userModel->find($id)->current();
    		if (!$user) throw new Zend_Exception("User tidak ditemukan.");
    			
    		$userForm->populate($user->toArray());
    		$this->view->user = $user;
    	}
    	$this->view->form = $userForm;
    	
    }
    
    /**
     * Halaman untuk menghapus data user.
     */
    public function deleteAction()
    {
    	$userModel = new Model_User();
    	$id = $this->getRequest()->getParam("id");
    	$userModel->deleteData($id);
    	return $this->_forward("index");
    }
    
    /**
     * Keadaan user, apakah sudah login atau tidak.
     */
    public function stateAction()
    {
    	$auth = Zend_Auth::getInstance();
    	if ($auth->hasIdentity()) {
    		$this->view->identity = $auth->getIdentity();
    	}
    }

    /**
     * Login
     */
    public function loginAction()
    {
    	// Halaman yang akan diarahkan jika login berhasil
    	$ref = $this->getRequest()->getParam("ref");
    	if (!$ref) $ref = "index";
    	$ref = "/$ref";
    	
    	$loginForm = new Form_Login();
    	$loginForm->setAction("/user/login");
    	
    	if ($this->getRequest()->isPost() && $loginForm->isValid($_POST)) {
    		$data = $loginForm->getValues();
    		$userModel = new Model_User();
    		if ($userModel->login($data["username"], $data["password"])) {
    			return $this->_redirect($ref);
    		} else {
    			$this->view->message = "Maaf, username dan/atau password anda salah!";
    		}
    	}
    	
    	$this->view->ref = $ref;
    	$this->view->form = $loginForm;
    }
    
    /**
     * Logout
     */
    public function logoutAction()
    {
    	$userModel = new Model_User();
    	$userModel->logout();
    }
}

