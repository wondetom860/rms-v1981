<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\ClassLevel */

$this->title = Yii::t('decirs', 'Create Class Level');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Class Levels'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="class-level-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
