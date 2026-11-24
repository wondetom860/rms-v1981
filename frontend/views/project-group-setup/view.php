<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\ProjectCourseSetup */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Project Course Setups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-course-setup-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('decirs', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('decirs', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('decirs', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'pcs_acadYear_relId',
            'pcs_sem_relId',
            'pcs_gset_relId',
            'pcs_course_relId',
            'pcs_pgName_relId',
            'pcs_advisor',
            'pcs_group_leader',
            'pcs_title:ntext',
            'pcs_approved',
            'pcs_approved_at',
            'pcs_approved_by',
            'pcs_created_at',
            'pcs_created_by',
            'pcs_updated_at',
            'pcs_updated_by',
        ],
    ]) ?>

</div>
