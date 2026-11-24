<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\CourseAssessment\models\ContAssessCatagory */

$this->title = Yii::t('rms', 'Assessment Catagory: {name}', [
    'name' => $model->catCatagoryRel->catagory_name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Assessment Catagories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="cont-assess-catagory-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
