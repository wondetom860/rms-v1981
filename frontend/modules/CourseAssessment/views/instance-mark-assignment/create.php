<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\CourseAssessment\models\InstanceMarkAssignment */

$this->title = Yii::t('rms', 'Create Instance Mark Assignment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Instance Mark Assignments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="instance-mark-assignment-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
