<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\SecondarySchoolAttended */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Secondary School Attendeds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="secondary-school-attended-view">

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
            'ssa_ei_relId',
            'ssa_school_name:ntext',
            'ssa_town:ntext',
            'ssa_year',
            'ssa_gradeLevels:ntext',
            'ssa_created_by',
            'ssa_created_at',
            'ssa_updated_by',
            'ssa_updated_at',
        ],
    ]) ?>

</div>
