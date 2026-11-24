<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\Nationality */

$this->title = Yii::t('decirs', 'Create Nationality');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Nationalities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nationality-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
