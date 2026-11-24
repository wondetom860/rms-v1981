<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\SchoolYear */

$this->title = Yii::t('decirs', 'Create School Year');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'School Years'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="school-year-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
