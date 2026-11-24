<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseEmploymentTypeBundle */

$this->title = Yii::t('rms', 'Create Ase Employment Type Bundle');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Ase Employment Type Bundles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ase-employment-type-bundle-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
