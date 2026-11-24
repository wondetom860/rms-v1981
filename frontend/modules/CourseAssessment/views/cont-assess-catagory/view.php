<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\modules\CourseAssessment\models\ContAssessCatagory */

$this->title = $model->catCoffRel->coff2detAcProgRel->getAcadYearHeader();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Course Assessment Catagories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="cont-assess-catagory-view">

    <h3><?= Html::encode($this->title) ?>

    <p class="pull-right">
        <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs']) ?>
        <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-xs',
            'data' => [
                'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    </h3>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'cat_acadYear_relId',
            'cat_sem_relId',
            'cat_coff_relId',
            'cat_course_relId',
            'cat_catagory_relId',
            'cat_maxPoint',
            'cat_created_at',
            'cat_created_by',
            'cat_updated_by',
            'cat_updated_at',
        ],
    ]) ?>

</div>
