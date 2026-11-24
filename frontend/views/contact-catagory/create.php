<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\ContactCatagory */

$this->title = Yii::t('rms', 'Create Contact Catagory');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Contact Catagories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-catagory-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
