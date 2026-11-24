<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\Student */

$this->title = Yii::t('decirs', 'Student Academic Profile');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Students'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-create">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->renderAjax('_form', [
        'sessionForm'=>$sessionForm,
    ]) ?>

</div>
