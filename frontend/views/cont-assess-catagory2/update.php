<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\ContAssessCatagory2 */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Continous Assessment Catagory',
]) . $model->catCatagoryRel->catagory_name."[{$model->catCourseRel->course_code}]";
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Cont Assess Catagories'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="cont-assess-catagory2-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
