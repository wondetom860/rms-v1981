<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\SemesterRegistrationControlac */

$this->title = Yii::t('decirs', 'Create Semester Registration Controlac');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Semester Registration Controlacs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-registration-controlac-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
