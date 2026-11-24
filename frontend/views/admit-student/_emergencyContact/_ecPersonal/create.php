<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\EmergencyContact */

$this->title = Yii::t('decirs', 'Student Emergency Contact');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Emergency Contacts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="emergency-contact-create">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->renderAjax('_form', [
        'sessionForm' => $sessionForm,
    ]) ?>

</div>
