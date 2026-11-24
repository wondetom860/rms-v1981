<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WEvaluationEntryTable */

$this->title = Yii::t('decris', 'Create Wevaluation Entry Table');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Wevaluation Entry Tables'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wevaluation-entry-table-create">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
