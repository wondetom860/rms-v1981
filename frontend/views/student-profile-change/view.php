<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\StudentProfileChange */

$this->title = $model->stdRel->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Student Profile Changes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="student-profile-change-view">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
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
                'attribute' => 'std_relId',
                'value' => function ($model) {
                    return $model->stdRel->getFullName();
                }
            ],
            'attribute_label',
            'old_value',
            [
                'attribute' => 'new_value',
                'value' => function ($model) {
                    $rr = "";
                    if ($model->relation_name) {
                        $relationName = $model->relation_name;
                        $relatedAttributeName = $model->related_attribute_name;
                        $rr = $model->stdRel->$relationName::findOne(["id" => $model->new_value])->$relatedAttributeName;
                    } else {
                        $rr = $model->new_value;
                    }
                    return $rr;
                }
            ],
            [
                'attribute' => 'approval_status',
                'format' => 'raw',
                'value' => function ($model) {
                    $status = "";
                    $btnApprove = "";
                    if ($model->approval_status) {
                        $by = $model->approvedBy->username;
                        $dt = date('dMY', $model->approved_at);
                        $status = "<small class='bg-success'>Approved by {$by} on {$dt}</small>";
                    } else {
                        $status = "<small class='bg-warning'>Pending approval</small>";
                        if (Yii::$app->user->can('Registrar')) {
                            $btnApprove = Html::button('Approve', ['class' => 'btn btn-xs btn-warning pull-right', 'onclick' => "approveStatusChange({$model->id});return false;"]);
                        }
                    }

                    return "{$status} {$btnApprove}";
                }
            ],
            // 'approved_by',
            // 'approved_at',
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return date("dMY", $model->created_at);
                }
            ],
            [
                'attribute' => 'created_by',
                'value' => function ($model) {
                    return $model->createdBy->username;
                }
            ],
            // 'created_by',
            [
                'attribute' => 'updated_at',
                'value' => function ($model) {
                    return $model->updated_at ? date("dMY", $model->updated_at) : '';
                }
            ],
            [
                'attribute' => 'updated_by',
                'value' => function ($model) {
                    return $model->updated_at ? $model->updatedBy->username : '';
                }
            ],
            // 'data:ntext',
        ],
    ]) ?>

</div>

<script>
    window.approveStatusChange = (id) => {
        if (confirm('Are younsure to approve status change')) {
            $.post('/student-profile-change/approve-request', {
                id: id
            }).done((resp) => {
                alert(resp);
            });
        }
    }
</script>