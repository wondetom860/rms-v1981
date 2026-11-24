<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WEvaluationPoints */

$this->title = Yii::t('decris', 'Create Evaluation Points');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Evaluation Points'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wevaluation-points-create">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
