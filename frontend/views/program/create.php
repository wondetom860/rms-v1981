<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\Program */

$this->title = Yii::t('decirs', 'Add New Program');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Programs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
