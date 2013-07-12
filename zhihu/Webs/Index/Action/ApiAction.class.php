<?php
class ApiAction extends Action {
	
	public function qlist(){
        $page = (isset($_GET['p']) && intval($_GET['p'])) ? intval($_GET['p']) : 1;
        $questionList = M('Question');
        $limit = 20*($page-1).',20';
        $qList = $questionList->order('entityUpdateVersion DESC')->limit($limit)->select();
        foreach ($qList as $key => $value) {
            $qList[$key]['questionContent'] = strip_tags($value['questionContent']); 
            $c = M('CommonCategories')->where("number = '".$value['category']."'")->find();
            $qList[$key]['categoryName'] = $c['title'];
            $qList[$key]['questionTypeName'] = getQuestionTypeName($value['questionType']);
            $qList[$key]['tag'] = trim($value['tag']) ? explode(' ', trim($value['tag'])) : null;
            $qList[$key]['lastUpdateTime'] = showFriendlyTime($value['entityUpdateVersion']);
        }

        $this->qList = $qList;
        // DP($qList);
        // die();
        $this->ajaxReturn($qList,'JSON');
    }
	
}

?>