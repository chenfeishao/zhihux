<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends BaseAction {
    public function index(){
        $this->assign('site_title','首页');
        $this->display();
    }
    
    public function newQuestion(){
        $id = (isset($_GET['q']) && intval($_GET['q'])) ? intval($_GET['q']) : 0;
        $updateQuestion = null;
        $this->assign('site_title',$id > 0 ? '更新问题' : '发布问题');
        $user = $this-> getCrrentUser();
        if($id>0){
            $updateQuestion = M('Question')->where(array('questionUserID'=>$user['id'],'id'=>$id))->find();
            if($updateQuestion){
                $c = M('CommonCategories')->where("number = '".$updateQuestion['category']."'")->find();
                $updateQuestion['categoryName'] = $c['title'];
                $this->assign('question',$updateQuestion);
            }else
                halt('Undefined');
        }
        if (IS_POST) {
            $question = D('Question');
            if ($data =$question->create()) {
                if (!$user)
                    $this -> sendSiteMessage('error','请先登录');
                if($id>0){
                    $question -> id = $id;
                    $question -> lastUpdateTime = time();
                    if ($question->save()) {
                        $this -> sendSiteMessage('success','更新成功');
                    }else
                        $this -> sendSiteMessage('error','更新失败');
                }else{
                    //发布问题
                    $data['questionUserID'] = $user['id'];
                    if($question->add($data)){
                        $this -> sendSiteMessage('success','发布成功');
                    }
                    else{
                        $this -> sendSiteMessage('error','发布失败');
                    }
                }
            }else
                 $this -> sendSiteMessage('error',$question -> getError());
        }
        $this->display();
    }

    public function about(){
        $this->assign('site_title','关于');
        $this->display();
    }

    public function qlist(){
        if(!IS_AJAX)
            halt('Undefined');
        $number = (isset($_GET['n']) && trim($_GET['n'])) ? trim($_GET['n']) : null;
        $tag = (isset($_GET['t']) && trim($_GET['t'])) ? trim($_GET['t']) : null;
        $word = (isset($_GET['w']) && trim($_GET['w'])) ? trim(I('w')) : null;
        $page = (isset($_GET['p']) && intval($_GET['p'])) ? intval($_GET['p']) : 1;
        $questionList = M('Question');
        if($number)
            $questionList = $questionList->where("category = '".$number."'");
        elseif ($tag) 
            $questionList = $questionList->where("tag like '%".$tag."%'");
        elseif ($word) 
            $questionList = $questionList->where("questionContent like '%".$word."%'");
        $limit = 20*($page-1).',20';
        $qList = $questionList->order('entityUpdateVersion DESC')->limit($limit)->select();
        foreach ($qList as $key => $value) {
            $u= M('useraccount')->find($value['questionUserID']);
            $qList[$key]['userName'] = $u['userName'];
            $c = M('CommonCategories')->where("number = '".$value['category']."'")->find();
            $qList[$key]['categoryName'] = $c['title'];
            $qList[$key]['questionTypeName'] = getQuestionTypeName($value['questionType']);
            $qList[$key]['tag'] = trim($value['tag']) ? explode(' ', trim($value['tag'])) : null;
        }

        $this->qList = $qList;
        // DP($qList);
        // die();
        $this->ajaxReturn(array('success'=>true,'html'=>$this->fetch()),'JSON');
    }

    public function qsearch(){
        $site_title = '全部问题';
        $number = (isset($_GET['n']) && trim($_GET['n'])) ? trim(I('n')) : null;
        $tag = (isset($_GET['t']) && trim($_GET['t'])) ? trim(I('t')) : null;
        $word = (isset($_GET['w']) && trim($_GET['w'])) ? trim(I('w')) : null;
        if($number){
            $category = M('CommonCategories')->where("number = '".$number."'")->find();
            if($category){
                $site_title = $category['title'].'领域的问题';
                $this->assign('number',$number);
            }
        }elseif ($tag){ 
            $site_title = $tag.'标签的问题';
            $this->assign('tag',$tag);
        }
        elseif ($word){
            $site_title = '与'.$tag.'相关的问题';
            $this->assign('word',$word);
        }
        $this->assign('site_title',$site_title);
        $this->display('index');
    }

    public function addAnswer(){
        if(!IS_AJAX && !IS_POST)
            halt('Undefined');
        $user = $this-> getCrrentUser();
        $answer = D('Answer');
        if($data=$answer->create()){
            if(!$user)
                $this->ajaxReturn(array('success'=>false,'message'=>'请先登录'),'JSON');
            if(intval($_POST['questionID'])>0){
                $count = M('Answer')->where("answerUserID=".$user['id']." and questionID=".intval($_POST['questionID']))->count();
                if($count>0)
                    $this->ajaxReturn(array('success'=>false,'message'=>'已经回答过该问题了'),'JSON');
                $data['answerUserID']=$user['id'];
                $data['updateCount']=1;
                if($answerID = $answer->add($data)){
                    $qId = $data['questionID'];
                    $question = M('Question')->find($qId);
                    $question['answerCount']=$question['answerCount']+1;
                    M('Question')->save($question);
                    $record = D('Record')->create();
                    $record['userId']=$user['id'];
                    $record['recordAction']=1;
                    $record['targetId'] = $answerID;
                    $record['link']=U('Index/Question/Detail',array('id'=>$qId));
                    D('Record')->add($record);
                    $this->ajaxReturn(array('success'=>true),'JSON');
                }
                else
                    $this->ajaxReturn(array('success'=>false,'message'=>'提交答案失败'),'JSON');
            }else
                $this->ajaxReturn(array('success'=>false,'message'=>'请选择回答的问题'),'JSON');
        }else
            $this->ajaxReturn(array('success'=>false,'message'=>$answer->getError()),'JSON');
    }

    public function addfollow(){
        if(!IS_AJAX && !IS_POST)
            halt('Undefined');
        if(isset($_POST['id']) && intval($_POST['id'])){
            $questionID = intval($_POST['id']);
            $user = $this-> getCrrentUser();
            if(!$user)
                $this->ajaxReturn(array('success'=>false,'message'=>'请先登录'),'JSON');
            $count = M('Record')->where("userId=".$user['id']." and recordAction=2 and targetId=".$questionID)->count();
            if($count>0)
                $this->ajaxReturn(array('success'=>false,'message'=>'已经关注了该问题'),'JSON');
            $q = M('Question')->find(intval($_POST['id']));
            $q['followCount']=intval($q['followCount'])+1;
            M('Question')->save($q);
            $record = D('Record')->create();
            $record['userId']=$user['id'];
            $record['recordAction']=2;
            $record['targetId'] = $questionID;
            $record['entityCreateTime']=time();
            D('Record')->add($record);
            $this->ajaxReturn(array('success'=>true),'JSON');
        }else
            $this->ajaxReturn(array('success'=>false,'message'=>'Entity Not Find'),'JSON');
    }

    public function addgoodreputation(){
        if(!IS_AJAX && !IS_POST)
            halt('Undefined');
        if(isset($_POST['id']) && intval($_POST['id'])){
            $questionID = intval($_POST['id']);
            $user = $this-> getCrrentUser();
            if(!$user)
                $this->ajaxReturn(array('success'=>false,'message'=>'请先登录'),'JSON');
            $count = M('Record')->where("userId=".$user['id']." and recordAction=3 and targetId=".$questionID)->count();
            if($count>0)
                $this->ajaxReturn(array('success'=>false,'message'=>'已经赞过该问题了'),'JSON');
            $q = M('Question')->find(intval($_POST['id']));
            $q['goodReputation']=intval($q['goodReputation'])+1;
            M('Question')->save($q);
            $data = D('Record')->create();
            $data['id']=null;
            $data['userId']=$user['id'];
            $data['targetId']=$questionID;
            $data['recordAction']=3;
            $data['entityCreateTime']=time();
            D('Record')->add($data);
            $this->ajaxReturn(array('success'=>true),'JSON');
        }else
            $this->ajaxReturn(array('success'=>false,'message'=>'Entity Not Find'),'JSON');
    }

    public function addbadreputation(){
        if(!IS_AJAX && !IS_POST)
            halt('Undefined');
        if(isset($_POST['id']) && intval($_POST['id'])){
            $questionID = intval($_POST['id']);
            $user = $this-> getCrrentUser();
            if(!$user)
                $this->ajaxReturn(array('success'=>false,'message'=>'请先登录'),'JSON');
            $count = M('Record')->where("userId=".$user['id']." and recordAction=4 and targetId=".$questionID)->count();
            if($count>0)
                $this->ajaxReturn(array('success'=>false,'message'=>'已经鄙过该问题了'),'JSON');
            $q = M('Question')->find(intval($_POST['id']));
            $q['badReputation']=intval($q['badReputation'])+1;
            M('Question')->save($q);
            $data = D('Record')->create();
            $data['id']=null;
            $data['userId']=$user['id'];
            $data['targetId']=$questionID;
            $data['recordAction']=4;
            $data['entityCreateTime']=time();
            D('Record')->add($data);
            $this->ajaxReturn(array('success'=>true),'JSON');
        }else
            $this->ajaxReturn(array('success'=>false,'message'=>'Entity Not Find'),'JSON');
    }

    public function addquestioncomment(){
        if(!IS_AJAX && !IS_POST)
            halt('Undefined');
        if(isset($_POST['questionID']) && intval($_POST['questionID'])){
            $questionID = intval($_POST['questionID']);
            $commentContent = $_POST['commentContent'];
            $user = $this-> getCrrentUser();
            if(!$user)
                $this->ajaxReturn(array('success'=>false,'message'=>'请先登录'),'JSON');
            $q = M('Question')->find($questionID);
            $q['commentCount']=intval($q['commentCount'])+1;
            M('Question')->save($q);
            $data = D('Record')->create();
            $data['id']=null;
            $data['userId']=$user['id'];
            $data['targetId']=$questionID;
            $data['recordAction']=5;
            $data['entityCreateTime']=time();
            D('Record')->add($data);
            $comment = D('Comment')->create();
            $comment['id']=null;
            $comment['userId']=$user['id'];
            $comment['commentContent']=$commentContent;
            $comment['targetId']=$questionID;
            $comment['commentType']=1;
            $comment['entityCreateTime']=time();
            D('Comment')->add($comment);
            $this->ajaxReturn(array('success'=>true),'JSON');
        }else
            $this->ajaxReturn(array('success'=>false,'message'=>'Entity Not Find'),'JSON');
    }

    public function loadcomment(){
        if(!IS_AJAX)
            halt('Undefined');
        if(isset($_GET['id']) && intval($_GET['id'])){
            $questionID = intval($_GET['id']);
            $entities = M('Comment')->where("commentType=1 and targetId=".$questionID)->select();
            foreach ($entities as $key => $value) {
                $u= M('useraccount')->find($value['userId']);
                $entities[$key]['userName'] = $u['userName'];
                $entities[$key]['userLink'] = U('Index/User/infocenter',array('id'=>$value['userId']));
                $entities[$key]['showTime'] = showFriendlyTime($value['entityUpdateVersion']);
            }
            if($entities)
                $this->ajaxReturn(array('success'=>true,'data'=>$entities),'JSON');
            else
                $this->ajaxReturn(array('success'=>true,'data'=>array()),'JSON');
        }else
            $this->ajaxReturn(array('success'=>false,'message'=>'Entity Not Find'),'JSON');
    }
}
?>