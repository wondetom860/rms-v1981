<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\CalConv */

$this->title = Yii::t('decirs', 'Create Cal Conv');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Cal Convs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cal-conv-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
