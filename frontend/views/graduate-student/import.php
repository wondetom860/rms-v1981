<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\GraduateStudent */

$this->title = Yii::t('rms', 'Add New Graduate Student Record');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Graduate Students'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="graduate-student-create">

    <h3 style="border-bottom: 1px solid black;"><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form2', [
        'model' => $model,
        'modelUser'=>$modelUser,
    ]) ?>

</div>

<script type="text/javascript">
	window.loadForm = (userId,userInfo)=>{
		$("#graduatestudent-fullname").val(userInfo);
		$("#graduatestudent-gstd_user_relid").val(userId);
	}
</script>
