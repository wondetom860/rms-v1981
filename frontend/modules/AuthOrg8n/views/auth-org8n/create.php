<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model common\models\AuthOrg8n */

$this->title = Yii::t('decris', 'Register New Authenticated Organization Information');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Auth Org8ns'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-org8n-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
