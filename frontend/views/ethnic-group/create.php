<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\EthnicGroup */

$this->title = Yii::t('rms', 'Register Ethnic Group');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Ethnic Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ethnic-group-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
