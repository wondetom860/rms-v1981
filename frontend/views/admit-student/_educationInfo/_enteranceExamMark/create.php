<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\EnteranceExamMark */

$this->title = Yii::t('decirs', 'Enterance Exam Mark');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Enterance Exam Marks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="enterance-exam-mark-create">

    <h2><?= Html::encode($this->title) ?></h2>

    Ethiopian Higher Education Enterance Examination (EHEEE) Information
	<br>
	<span class="bd-info" style="font-size: 7pt; font-style: italic; font-weight: normal;">(Write five subjects with the best grades earned; <b>English</b>, <b>Maths</b>, and <b>Science(s)</b> must be included):
	</span>
    <?= $this->renderAjax('_form', [
        'sessionForm' => $sessionForm,
    ]) ?>

</div>
