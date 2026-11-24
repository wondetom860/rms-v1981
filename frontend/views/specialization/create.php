<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\Specialization */

$this->title = Yii::t('decirs', 'Create Specialization');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Specializations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="specialization-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
