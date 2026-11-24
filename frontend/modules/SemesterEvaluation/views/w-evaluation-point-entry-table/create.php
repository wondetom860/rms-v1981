<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WEvaluationPointEntryTable */

$this->title = Yii::t('rms', 'Create Evaluation Point Entry Table');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Evaluation Point Entry Tables'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wevaluation-point-entry-table-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
        'modelEP'=>$modelEP,
    ]) ?>

</div>
