<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\AddressInfo */

$this->title = Yii::t('decirs', 'Emergency Contact - Address Info');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Address Infos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="address-info-create">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->renderAjax('_form', [
        'sessionForm' => $sessionForm,
    ]) ?>

</div>
