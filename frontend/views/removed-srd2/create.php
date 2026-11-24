<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\RemovedSrd2 */

$this->title = Yii::t('rms', 'Create Removed Srd2');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Removed Srd2s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="removed-srd2-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
