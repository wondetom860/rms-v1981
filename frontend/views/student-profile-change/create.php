<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\StudentProfileChange */

$this->title = Yii::t('rms', 'Request Student Profile Change');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Student Profile Changes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-profile-change-create">

    <h3><?= Html::encode($this->title) . ": " . $model->stdRel->std_regId ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
        'attributeValues' => $attributeValues,
        'attributeName' => $attributeName,
    ]) ?>

</div>