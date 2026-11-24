<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\Admission */

$this->title = Yii::t('decirs', 'Create Admission');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Admissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admission-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
