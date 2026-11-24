<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\ContinuousAssessment2 */

$this->title = Yii::t('decirs', 'Update {modelClass}: ', [
    'modelClass' => 'Continuous Assessment2',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Continuous Assessment2s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decirs', 'Update');
?>
<div class="continuous-assessment2-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
