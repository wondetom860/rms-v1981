<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\AddressInfo */

$this->title = Yii::t('decirs', 'Create Address Info');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Address Infos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="address-info-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
