<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\CourseAssessment\models\ContAssessCatagory */

$this->title = Yii::t('rms', 'Add New Assessment Catagory');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Assessment Catagories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cont-assess-catagory-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
