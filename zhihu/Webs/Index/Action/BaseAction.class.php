<?php
class BaseAction extends Action {
	
	public $user; //登录用户
    public $questionCategories; //问题领域

	public function _initialize(){
		$this->assign('site_domain','http://zhihux.sinaapp.com');
		$this->assign('message_style','alert-hidden');
		$this->assign('message_content','');
		$this->assign('login_error_count',isset($_SESSION['login_error_count'])?intval($_SESSION['login_error_count']):0);
		if ($this->isLogin()){
            $user = $this->getCrrentUser();
			$this->assign('user',$user);
            $this->assign('is_login',1);
            $this->assign('login_user_name',$user['userName']);
        }else{
            $this->assign('is_login',0);
            $this->assign('login_user_name','佚名');
        }
        $questionCategories = $this-> getQuestionCategories();
        $this->assign('question_categories',$questionCategories);

	}

    protected function sendSiteMessage($type,$message){
        $this->assign('message_style','alert-'.$type);
        $this->assign('message_content',$message);
    }

    protected function getCrrentUser(){
    	if ($this->isLogin()) {
    		$this->user = M('useraccount')->where(array('userName' => $_COOKIE['zh_user']['username'],'id' => intval($_COOKIE['zh_user']['id'])))->find();
			$this->user['tipsCount'] = 0;
			return $this->user;
    	}else
    		return null;
    }

    protected function isLogin(){
    	if (isset($_COOKIE['zh_user']['id']))
    		return true;
    	else
    		return false;
    }

    protected function getQuestionCategories(){
        F('questionCategories',null);
        if($data=F('questionCategories'))
            return $data;
        else{
            $data = M('CommonCategories')->where("number like 'qc%'")->order('number ASC')->select();
            F('questionCategories',$data);
            return $data;
        }
    }
}

?>