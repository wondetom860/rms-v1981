<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\Faculty */

$this->title = Yii::t('rms', 'Add New Faculty');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Faculties'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="faculty-create">

    <h4><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
