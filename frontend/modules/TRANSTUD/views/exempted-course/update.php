<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\modules\TRANSTUD\models\ExemptedCourse */

$this->title = Yii::t('rms', 'Update Exempted Course >>  {name}', [
    'name' => $model->course_code . ' : ' . $model->course_name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Exempted Courses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->course_code, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="exempted-course-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
