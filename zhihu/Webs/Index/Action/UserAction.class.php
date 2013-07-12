<?php
// 本类由系统自动生成，仅供测试用途
class UserAction extends BaseAction {
    public function index(){
    	$this->assign('site_title','首页');
		$this->display();
    }
    
    public function newQuestion(){
    	$this->assign('site_title','发布问题');
    	$this->display();
    }
    
    public function questionList(){
    	$categoryTitle = isset($_GET['number']) && trim($_GET['number']) ? trim($_GET['number']) :'所有';
    	$this->assign('site_title',$categoryTitle.'问题列表');
    	$this->display();
    }
    
    public function register(){
    	// if (!IS_POST && !IS_AJAX) {
     //        $this->assign('site_title','用户注册');
     //        $this-> display();
     //    }else{
     //        $useraccount = D('UserAccount');
     //        if ($useraccount->create()) {
     //            $useraccount -> useremail = I('userEmail');
     //            $useraccount -> username = I('userName');
     //            $useraccount -> userpass = md5(md5(md5(I('userPass'))));
     //            if (condition) {
     //                $user_id = $useraccount->add();
     //                if($user_id){
     //                    $_SESSION['zh_user_id'] = $user_id;
     //                    $_SESSION['zh_user_name'] = $useraccount -> username;
     //                    return redirect(U('Index/Index/index'));
     //                }
     //                else 
     //                    $this->ajaxReturn('注册失败，请重试','JSON');
     //            }else{
     //                $this->ajaxReturn('验证码错误','JSON');
     //            }
     //        }else{
     //            $this->ajaxReturn($useraccount->getError(),'JSON');
     //        }
     //    }
        $this->assign('site_title','用户注册');
        if (IS_POST) {
            $useraccount = D('UserAccount');
            if ($data =$useraccount->create()) {
                $data['userEmail'] = I('userEmail');
                $data['userName'] = I('userName');
                $data['userPass'] = md5(md5(md5(I('userPass'))));
                $verify = I('verifyCode');
                if ($verify != md5($_SESSION['Verify'])) {
                    $user_id = $useraccount->add($data);
                    if($user_id){
                        $last_time=time();
                        cookie('zh_user[id]',$user_id,3600*24*7);
                        cookie('zh_user[username]',$data['userName'],3600*24*7);
                        cookie('zh_user[key]',md5($user_id.$data['userName'].$last_time,3600*24*7));
                        return redirect(U('Index/Index/index'));
                    }
                    else{
                        $this -> sendSiteMessage('error','注册失败，请重试');
                    }
                }else{
                    $this -> sendSiteMessage('error','验证码错误');
                }
            }else
               $this -> sendSiteMessage('error',$useraccount -> getError());
        }
        $this-> display();
    }

    public function checkusername(){
        if (!IS_AJAX) {
            halt('Undefined');
        }
        $username = I('userName');
        $user = M('useraccount')->where(array('userName' => $username ))->find();
        //DP($user);
        //die();
        if($user)
            $this->ajaxReturn(false,'JSON');
        else
            $this->ajaxReturn(true,'JSON');

    }

    public function checkuseremail(){
         if (!IS_AJAX) {
            halt('Undefined');
        }
        $useremail = I('userEmail');
        $user = M('useraccount')->where(array('userEmail' => $useremail))->find();
        if($user)
            $this->ajaxReturn(false,'JSON');
        else
            $this->ajaxReturn(true,'JSON');
    }

    public function ajaxlogin(){
        if (!IS_AJAX)
            halt('Undefined');
        if(isset($_SESSION['login_error_count']) && intval($_SESSION['login_error_count'])>=4)
            $this->ajaxReturn(array('status'=> 2,'message' => '登录错误次数过多，请稍后重试'),'JSON');
        $u = trim(I('post.u'));
        $p = trim(I('post.p'));
        if ($u && $p) {
            $p = md5(md5(md5($p)));
            $user = M('useraccount')->where(array('userEmail' => $u,'userPass' => $p))->find();
            if (!$user) 
                $user = M('useraccount')->where(array('userName' => $u,'userPass' => $p))->find();
            if($user){
                $last_time=time();
                cookie('zh_user[id]',$user['id'],3600*24*7);
                cookie('zh_user[username]',$user['userName'],3600*24*7);
                cookie('zh_user[key]',md5($user['id'].$user['userName'].$last_time,3600*24*7));
                $this->ajaxReturn(array('status'=> 1,'message' => '登录成功'),'JSON');
            }
            else{
                session('login_error_count',intval($_SESSION['login_error_count'])+1);
                $this->ajaxReturn(array('status'=> 4,'message' => '账号或密码错误'),'JSON');
            }
        }else
            $this->ajaxReturn(array('status'=> 3,'message' => '请输入账号密码'),'JSON');
    }

    public function logout(){
        cookie('zh_user[id]',null);
        cookie('zh_user[username]',null);
        cookie('zh_user[key]',null);
        $this->redirect('Index/Index/index');
    }
}
?>