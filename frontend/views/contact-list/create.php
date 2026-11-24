<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\ContactList */

$this->title = Yii::t('rms', 'Create Contact List');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Contact Lists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-list-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
