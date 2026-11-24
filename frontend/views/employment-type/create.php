<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\EmploymentType */

$this->title = Yii::t('rms', 'Create Employment Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Employment Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employment-type-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
