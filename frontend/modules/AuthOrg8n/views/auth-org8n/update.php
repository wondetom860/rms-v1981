<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model common\models\AuthOrg8n */

$this->title = Yii::t('decris', 'Update Authenticated Organization Information: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Auth Org8ns'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decris', 'Update');
?>
<div class="auth-org8n-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
