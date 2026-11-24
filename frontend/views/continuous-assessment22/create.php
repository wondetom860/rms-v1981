<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\ContinuousAssessment */

$this->title = Yii::t('decirs', 'Create Continuous Assessment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Continuous Assessments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="continuous-assessment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
