<?php
class BaseAction extends Action {
	
	public function _initialize(){
		if (!isset($_SESSION['zh_admin_user_id'])) {
			echo "请先登录";
			die();
		}
	}
}

?>