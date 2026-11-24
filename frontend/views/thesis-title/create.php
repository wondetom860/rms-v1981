<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\ThesisTitle */

$this->title = Yii::t('rms', 'Create Thesis Title');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Thesis Titles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="thesis-title-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
