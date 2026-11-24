<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\CocatDetail */

$this->title = Yii::t('decirs', 'Create Cocat Detail');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Cocat Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cocat-detail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
