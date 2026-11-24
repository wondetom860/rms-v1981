<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\AcademicCalander */

$this->title = Yii::t('decirs', 'Update {modelClass}: ', [
    'modelClass' => 'Academic Calander',
]) . $model->getHeaderInfo();
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Academic Calanders'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decirs', 'Update');
?>
<div class="academic-calander-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
