<?php
class QuestionModel extends Model {
	// 定义自动验证
    protected $_validate    =   array(
    	array('category','require','必须选择一个领域'),
        array('questionContent','require','问题内容不能为空'),
        array('answerContent','require','问题答案不能为空')
        );
   protected $_auto    =   array(
    	array('entityCreateTime','time',1,'function'),
        array('entityUpdateVersion','time',3,'function')
        );
   // 数据表名（不包含表前缀）
    protected $tableName        =   'question';
}
?>