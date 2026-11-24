<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\Student1 */

$this->title = Yii::t('decirs', 'Update {modelClass}: ', [
    'modelClass' => 'Student1',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Student1s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decirs', 'Update');
?>
<div class="student1-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
