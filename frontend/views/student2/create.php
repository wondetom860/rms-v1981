<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\Student */

$this->title = Yii::t('decirs', 'Create Student');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Students'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'userModel'=>$userModel,
    ]) ?>

</div>
