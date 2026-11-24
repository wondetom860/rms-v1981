<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\PgSecondarySchoolsAttended */

$this->title = Yii::t('decirs', 'Update {modelClass}: ', [
    'modelClass' => 'Pg Secondary Schools Attended',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Pg Secondary Schools Attendeds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decirs', 'Update');
?>
<div class="pg-secondary-schools-attended-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
