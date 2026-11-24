<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\CatagoryName2 */

$this->title = Yii::t('decirs', 'Update {modelClass}: ', [
    'modelClass' => 'Catagory Name2',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Catagory Name2s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decirs', 'Update');
?>
<div class="catagory-name2-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
