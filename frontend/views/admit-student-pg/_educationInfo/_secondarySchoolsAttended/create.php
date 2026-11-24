<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\SecondarySchoolAttended */

$this->title = Yii::t('decirs', 'Secondary School Attended');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Secondary School Attendeds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="secondary-school-attended-create">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->renderAjax('_form', [
        'sessionForm' => $sessionForm,
    ]) ?>

</div>
