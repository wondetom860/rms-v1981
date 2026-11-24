<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\RmsUserLink */

$this->title = $model->rmsUserRel->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Linked Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="rms-user-link-view">

    <h3><?= Html::encode($this->title) ?>

        <p class="float-right pull-right">
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
            [
                'attribute' => 'person_relId',
                'value' => function ($model) {
                    return $model->getFullNameHrms();
                }
            ],
            [
                'attribute' => 'rms_user_id',
                'value' => function ($model) {
                    return $model->getFullNameRms();
                }
            ],

            [
                'attribute' => 'rms_inst_id',
                'value' => function ($model) {
                    return $model->rmsInstRel ? $model->rmsInstRel->instDepRel->dept_name : null;
                }
            ],
            [
                'attribute' => 'rms_std_id',
                'value' => function ($model) {
                    $spec = $model->rmsStdRel && $model->rmsStdRel->stdSpecRel ? ' > ' . $model->rmsStdRel->stdSpecRel->spec_name : '';
                    return $model->rmsStdRel ? ($model->rmsStdRel->std_regId . " >> " . $model->rmsStdRel->stdDeptRel->dept_code . $spec) : null;
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    // if (Yii::$app->language == 'am') {

                    // }else{

                    // }

                    return date_format(date_create($model->created_at), 'd/m/Y');
                }
            ],
        ],
    ]) ?>

</div>