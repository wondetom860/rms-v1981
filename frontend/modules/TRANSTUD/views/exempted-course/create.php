<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/** @var frontend\modules\TRANSTUD\models\ExemptedCourse  $model*/

$this->title = Yii::t('rms', 'Create Exempted Course');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Exempted Courses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="exempted-course-create">

    <h4><?= Html::encode($this->title). " >> ". $model->tsRel->getTsHeader() ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
