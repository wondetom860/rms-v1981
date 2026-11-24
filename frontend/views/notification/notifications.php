<?php
$notCount = 0;
	if (count(Yii::$app->params['notifications']) > 0) {
		echo '<li class="header" id="not_id" data-c = "'.Yii::$app->params['notifications']['count'].'">You have '. Yii::$app->params['notifications']['count'].' notifications</li>';
		foreach (Yii::$app->params['notifications'] as $key) {
			if (empty($key['link']) || ($key['count'] == 0)) {
				continue;
			}
			echo '<li style=" word-break: break-all;">
	            <a href="'. $key['link'] .'" style="word-break: break-all; overflow: hidden;">
	                <i class="fa text-aqua"> '. $key['title'] .'</i>
	                <span class="pull-right warning" style="font-style-italic; color:red; background-color:inherit; font-size:10pt;">('.$key['count'].')</span>
	            </a>
	        </li>';
	        $notCount+= $key['count'];
		}
	} 
?>
<script type="text/javascript">
	// console.log($('#not_id').data('c'));
	$('#notifications_count').html(<?= $notCount ?>);
	$('#messagaes_count').html(<?= Yii::$app->params['notifications']['count'] ?>);
</script>