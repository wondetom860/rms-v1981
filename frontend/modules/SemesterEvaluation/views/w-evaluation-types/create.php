<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WEvaluationTypes */

$this->title = Yii::t('decris', 'Create Evaluation Types');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Evaluation Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wevaluation-types-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
