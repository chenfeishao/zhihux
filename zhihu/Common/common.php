<?php 

function DP($array){
 	dump($array,1,'<pre>',0);
 }

 function showFriendlyTime($time){
 	$now = mktime();
 	$t = $now - $time;
 	if($t<60)
 		return $t.'秒前';
 	elseif($t<3600)
 		return intval($t/60).'分钟前';
 	elseif ($t<3600*24)
 		return intval($t/3600).'小时前';
 	elseif ($t<3600*24*30)
 		return intval($t/(3600*24)).'天前';
 	elseif ($t<3600*24*30*12)
 		return intval($t/(3600*24*30)).'个月前';
 	else
 		return intval($t/(3600*24*30*12)).'年前';
 }

 function getQuestionTypeName($type){
 	if($type==2)
 		return '选择题';
 	elseif($type==3)
 		return '填空题';
 	elseif($type==4)
 		return '解答题';
 	else
 		return '判断题';
 }
 ?>