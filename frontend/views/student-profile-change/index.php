<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\StudentProfileChangeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Student Profile Changes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-profile-change-index">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right hidden">
            <?= Html::a(Yii::t('rms', 'Request Student Profile Change'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
        </p>
    </h3>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'std_attribute',
            [
                'attribute' => 'std_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->stdRel->getFullName(),Url::to('/student/view?id='.$model->std_relId));
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
                        // $by = $model->approvedBy->username;
                        $dt = date('dMY', $model->approved_at);
                        $status = "<small class='bg-success'>Approved on {$dt}</small>";
                    } else {
                        $status = "<small class='bg-warning'>Pending approval</small>";
                        if (Yii::$app->user->can('Registrar')) {
                            $btnApprove = Html::button('Approve', ['class' => 'btn btn-xs btn-warning pull-right', 'onclick' => "approveStatusChange({$model->id});return false;"]);
                        }
                    }

                    return "{$status} {$btnApprove}";
                }
            ],
            // 'approval_status',
            //'approved_by',
            //'approved_at',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

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