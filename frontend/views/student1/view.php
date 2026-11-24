<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\Student1 */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Student1s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student1-view">

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
            'std_user_relId',
            'std_regId',
            'std_type',
            'std_dept_relId',
            'std_spec_relId',
            'std_clslvl_relId',
            'std_prog_relId',
            'std_schlrshpType_relId',
            'std_admission_relId',
            'std_acadYear_relId',
            'std_group_realId',
            'std_semester_status',
            'std_marital_status',
            'std_emergency_contact_relId',
            'st_educ_info_relId',
            'std_created_at',
            'std_created_by',
            'std_updated_at',
            'std_updated_by',
        ],
    ]) ?>

</div>
