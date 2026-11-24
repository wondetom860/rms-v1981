<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model frontend\models\ContAssessCatagoryName */

$this->title = Yii::t('rms', 'Create Continous Assesement Catagory Name');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Cont Assess Catagory Names'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cont-assess-catagory-name-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
