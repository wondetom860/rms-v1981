<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\WGroupSetup */

$this->title = Yii::t('rms', 'Create Group Setup');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Group Setups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wgroup-setup-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
