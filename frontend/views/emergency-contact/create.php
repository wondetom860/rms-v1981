<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\EmergencyContact */

$this->title = Yii::t('decirs', 'Create Emergency Contact');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Emergency Contacts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="emergency-contact-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
