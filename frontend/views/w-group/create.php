<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\WGroup */

$this->title = Yii::t('decirs', 'Add new group name');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Wgroups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wgroup-create">

    <h4><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
