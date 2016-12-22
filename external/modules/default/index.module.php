<?php

class DefaultModule extends IndexbaseModule
{
    function __construct()
    {
        $this->DefaultModule();
    }
    function DefaultModule()
    {
        parent::__construct();
    }
    function index()
    {
    	$this->__call('index',null);
    }
	function __call($act, $a) {
		$view_file = ROOT_PATH .'/external/modules/'. MODULE .'/views/'.MODULE.'.'.ACT.'.view.php';
		if (file_exists($view_file)){
			require_once($view_file);
				$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : ACT;
				$act = ucfirst($act);
				$MyClass = new $act();
				$MyClass->main();
		}
		else{
			exit('Hello '.MODULE);
		}
	}
}

?>