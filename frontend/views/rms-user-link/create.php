<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\RmsUserLink */

$this->title = Yii::t('rms', 'Link Account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Linked Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rms-user-link-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
