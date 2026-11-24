<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Depal\models\DepalCategory */

$this->title = Yii::t('rms', 'Register New Creteria Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Depal'), 'url' => ['/Depal']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="depal-category-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
