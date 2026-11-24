<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\ScholarshipType */

$this->title = Yii::t('decirs', 'Create Scholarship Type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Scholarship Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scholarship-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
