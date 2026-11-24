<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\SecondarySchoolAttended */

$this->title = Yii::t('rms', 'Register Secondary School Attended');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Secondary School Attendeds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="secondary-school-attended-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
