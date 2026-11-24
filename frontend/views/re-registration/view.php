<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\ReRegistration */

$this->title = $model->stdRel->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Re-Registrations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$p = isset($print);
?>
<div class="re-registration-view">

        <?php if(!isset($print)): ?>
            <h3><?= Html::encode($this->title) ?>
        <p class="pull-right">
            <?= Html::button(Yii::t('rms', 'Print Request Form'), ['class' => 'btn btn-warning btn-xs', 'onclick' => "printRequestForm({$model->id});return false;"]) ?>
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
        <?php else:  ?>
            <h3 style="text-align:center"><?= Html::encode($this->title) ?><br><small class="text-mute">Course Re-Registration Form</small></h3>
        <?php endif ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'std_regId',
                'label' => 'Reg. ID',
                'format' => 'raw',
                'value' => function ($model) use($p){
                    return $p ? $model->stdRel->std_regId : Html::a($model->stdRel->std_regId,['student/view','id' => $model->std_relId]);
                }
            ],
            [
                'attribute' => 'std_relId',
                'value' => function ($model) {
                    return $model->stdRel->getFullName();
                }
            ],
            [
                'attribute' => 'course_relId',
                'value' => function ($model) {
                    return $model->courseRel->getCourseCredits() . " >> " . $model->courseRel->course_title;
                }
            ],

            [
                'attribute' => 'prev_gas_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->prevGasRel->getGsDisp();
                }
            ],
            [
                'attribute' => 'new_coff2det_relId',
                'format' => 'raw',
                'value' => function ($model) use($p) {
                    // var_dump($p);exit;
                    $coff2detGset = '';
                    if ($model->newCoff2detRel) {
                        $gset = $model->newCoff2detRel->coff2detCoff2Rel->coff2GroupRel;
                        $coff2detGset = $gset->getGsDisp() . " >> [{$gset->gsAcadYearRel->sch_year_desc}]";
                    } else {
                        if ((Yii::$app->user->can('Admin') || Yii::$app->user->can('Registrar'))) {
                            $coff2detGset = $p? "": "<button class='btn btn-xs btn-success' onclick='getCourseOfferings({$model->id});return false;'>Get Offerings</button>";
                        }
                    }
                    return $coff2detGset;
                }
            ],
            'trail_count',
            [
                'attribute' => 'reg_approved',
                'format' => 'raw',
                'visible' => Yii::$app->user->can('Registrar') && !isset($print),
                'value' => function ($model) {
                    if ($model->reg_approved == 1) {
                        return "<button class='btn btn-xs btn-warning' onclick='changeApproveStatus({$model->id},2);return false;' title='Cancel approval'>Cancel</button>";
                    } else {
                        if ($model->new_gas_relId && $model->new_coff2det_relId) {
                            return "<button class='btn btn-xs btn-primary' onclick='changeApproveStatus({$model->id},1);return false;' title='Approve request'>Approve</button>";
                        } else {
                            return "Request processing not completed yet.";
                        }
                    }
                }
            ],
            // 'reg_approved_at',
            // 'reg_approved_by',
            [
                'attribute' => 'dept_approved',
                'format' => 'raw',
                'visible' => Yii::$app->user->can('DepartmentHead'),
                'value' => function ($model) {
                    if ($model->dept_approved == 1) {
                        if ($model->reg_approved == 1) {
                            return "<small>Approved by the Registrar</small>";
                        } else {
                            return "<button class='btn btn-xs btn-warning' onclick='changeApproveStatus({$model->id},2);return false;' title='Cancel approval'>Cancel</button>";
                        }
                    } else {
                        return "<button class='btn btn-xs btn-primary' onclick='changeApproveStatus({$model->id},1);return false;' title='Approve request'>Approve</button>";
                    }
                }
            ],
            // 'dept_approved_at',
            // 'dept_approved_by',
            [
                'attribute' => 'admin_processed',
                'value' => function ($model) {
                    if ($model->new_gas_relId && ($model->admin_processed == 0)) {
                        $model->admin_processed = 1;
                        $model->updated_at = time();
                        $model->updated_by = Yii::$app->user->identity->id;
                        $model->getData();
                        $model->save();
                        $model->refresh();
                    }
                    return $model->new_gas_relId ? "Processed" : "Not Processed";
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return date('d-M-Y', $model->created_at);
                },
            ],
            [
                'attribute' => 'created_by',
                'value' => function ($model) {
                    return $model->createdBy->username;
                },
            ],
            // 'deleted_at',
            // 'deleted_by',
            // 'created_by',
            // 'updated_by',
            // 'data:ntext',
        ],
    ]) ?>

</div>
<script>
    const printRequestForm = (req_id) => {
        myWindow = window.open(
            '/re-registration/request-form-print?gadget=classical&req_id=' + req_id,
            'title=Re-registration application form',
            'height=800,width=900');
    }
    const selectOffering = function(offering_detail_id, req_id) {
        $.post('/re-registration/set-new-offering', {
            offering_detail_id: offering_detail_id,
            req_id: req_id
        }).done((resp) => {

        });
    }
    const getCourseOfferings = function(req_id) {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['/re-registration/get-offerings']) ?>',
            type: 'POST',
            data: {
                'req_id': req_id,
            },
            dataType: 'html',
            success: function(data) {
                $('#modal').find('.modal-header').html('Select Offering');
                $('#modalContent').css({
                    'text-align': 'left',
                    'margin-top': '0px'
                });
                $('#modalContent').html('Loading form...');
                $('#modelContent').addClass('table-responsive');
                $('#modalContent').css('overflow', 'auto');
                $('#modalContent').css('height', '500px');
                $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
                $('#modal').modal('show');
                $('#modalContent').html(data);
            }
        });
    }
    const changeApproveStatus = function(req_id, option) {
        $.post('/re-registration/change-apporve-status', {
            req_id: req_id,
            option: option
        }).done((resp) => {
            if (resp == 1) {
                window.location.href = window.location;
            } else {
                alert(resp);
            }
        });
    }
</script>