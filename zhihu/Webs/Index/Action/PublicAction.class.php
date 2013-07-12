<?php
class PublicAction extends Action {
	public function verify(){
		import('ORG.Util.Image');
        Image::buildImageVerify(1,1);
	}

	public function checkverifycode(){
		if (!IS_AJAX) {
            halt('Undefined');
        }
        //$_SESSION['verify']==md5(trim($_POST['verifyCode'])
        if ($_SESSION['verify']==md5(I('verifyCode'))) 
        	$this->ajaxReturn(true,'JSON');
        else
        	$this->ajaxReturn(false,'JSON');
	}
}

?>