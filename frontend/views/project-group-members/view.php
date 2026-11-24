<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\ProjectGroupMembers */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Project Group Members'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-group-members-view">

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
            'pgm_pgSetup_relId',
            'pgm_std_relId',
            'pgm_enroll_type',
            'pgm_grade_relId',
            'pgm_gradeApproved',
            'pgm_gradeApproved_by',
            'pgm_gradeApproved_at',
            'pgm_advisor_comment:ntext',
            'pgm_created_at',
            'pgm_craeted_by',
            'pgm_updated_by',
            'pgm_updated_at',
        ],
    ]) ?>

</div>
