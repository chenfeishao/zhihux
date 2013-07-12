<?php 
class AdminAction extends Action{

	public function login(){

		if (IS_POST && isset($_POST['adminName']) && isset($_POST['adminPass'])) {
			if (I('adminName')=='admin' && I('adminPass')=='greedisgood') {
				$_SESSION['zh_admin_user_id'] = 0;
				$this->redirect('Admin/Index/index');
			}
		}else if (isset($_GET['mem']) && I('mem')=='xi4oz3ro') {
			$this -> display();
		}else{
			halt('Undefined');
		}
	}
	
}
 ?>