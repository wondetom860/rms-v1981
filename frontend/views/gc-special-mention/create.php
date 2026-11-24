<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model frontend\models\GcSpecialMention */

$this->title = Yii::t('rms', 'Create Gc Special Mention');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Gc Special Mentions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gc-special-mention-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
