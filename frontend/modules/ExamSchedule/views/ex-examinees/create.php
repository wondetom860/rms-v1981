<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ExamSchedule\models\ExExaminees */

$this->title = Yii::t('rms', 'Create Ex Examinees');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Ex Examinees'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ex-examinees-create">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
