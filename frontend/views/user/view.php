<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\User $model */

$this->title = $model->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs']) ?>
        </p>
    </h3>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <center>
                    <img src="/uploads/<?= $model->photo_num ?>" height='190' width='160' alt='User Photo'>
                    <p><?= $model->getFullName() ?></p>
                </center>
            </div>
            <div class="col-md-9" style="max-height: 450px; overflow: auto;">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'username',
                        'gender',
                        'user_type',
                        'email:email',
                        'phone_mobile',
                        [
                            'attribute' => 'status',
                            'visible' => Yii::$app->user->can('UniversityRegistrar'),
                            'format' => 'raw',
                            'value' => function ($model) {

                                $std = $model->getStudents()->all();
                                // ->andFilterWhere(['std_semester_status' => ['ACTIVE','NOT APPROVED']])
                                if ($std) {

                                    $btnApprove = "<button class='btn btn-xs btn-warning pull-right' onclick='changeAccountStatus(10,{$model->id});return false;'>Approve</button>";
                                    $btnPrint = "<button class='btn btn-xs btn-warning pull-right' onclick='printReport({$model->id});return false;'>Print</button>";
                                    $st = 'Account Active';
                                    if ($model->status == $model::STATUS_ACTIVE) {
                                        // check if student entry exists and assigned to a group with this user account:
                                        $std = $model->getStudents()->andFilterWhere(['std_semester_status' => ['ACTIVE', 'GRADUATED']])->all();
                                        if ($std) {
                                            $btnApprove = "Student profile set to active/graduated";
                                        } else {
                                            $btnApprove = "<button class='btn btn-xs btn-danger pull-right' onclick='changeAccountStatus(2,{$model->id});return false;'>Change back</button>";
                                        }
                                    } else {
                                        $st = 'Not Approved';
                                    }
                                    return "{$st} >> {$btnApprove} >> $btnPrint";
                                } else {
                                    return "<font class='bg-warning'>Admission Request Not Found!</font>";
                                }
                            },
                        ],
                        [
                            'attribute' => 'nationality',
                            'value' => function ($model) {
                                $nationality =  \backend\models\Nationality::findOne(['id' => $model->nationality]);
                                return $nationality ? $nationality->name : '';
                            }
                        ],
                        [
                            'attribute' => 'dob',
                            'value' => function ($model) {
                                return $model->getDob();
                            }
                        ],
                        [
                            'attribute' => 'OTP',
                            'value' => function ($model) {
                                return $model->OTP;
                            }
                        ],
                        [
                            'attribute' => 'role',
                            'format' => 'raw',
                            'value' => function ($model) {
                                // getUserRole($userId)
                                $role = Yii::$app->authManager->getRolesByUser($model->id);
                                return $role && count($role) > 0 ? "<i>" . json_encode(array_keys($role)) . "</i>" : 'Not Known';
                            }
                        ],
                        [
                            'attribute' => 'activated',
                            'label' => 'User Account Status',
                            'format' => 'raw',
                            'value' => function ($model) {
                                $btn = "";
                                $status = "";
                                if ($model->activated == $model::ACTIVE_ACCOUNT) {
                                    $status = 'Activated';
                                    $btn = "<button class='btn btn-xs btn-warning btn-xs pull-right' onclick='loadAccountMgtFormModel(" . $model::STATUS_INACTIVE . ",{$model->id});return false;'>De-activate</button>";
                                } else {
                                    $status = 'InActivated';
                                    $btn = "<button class='btn btn-xs btn-danger btn-xs pull-right' onclick='loadAccountMgtFormModel(" . $model::STATUS_ACTIVE . ",{$model->id});return false;'>Activate</button>";
                                }
                                return  $status . " >> " . $btn;
                            }
                        ],
                        [
                            'attribute' => 'created_at',
                            'value' => function ($model) {
                                return date("d/m/Y", $model->created_at);
                            }
                        ],

                        [
                            'attribute' => 'Admissions',
                            'format' => 'raw',
                            'value' => function ($model) {
                                // ->andFilterWhere(['std_semester_status' => 'NOT APPROVED'])
                                $admiReq = $model->getStudents()->all();
                                if ($admiReq && count($admiReq) > 0) {
                                    $rep = "";
                                    foreach ($admiReq as $req) {
                                        $rep .= "Admission Request Detail: <button class='btn btn-xs btn-warning' onclick='showStudentInfoView({$req->id}); return false;'>Show Detail</button>";
                                    }
                                    return $rep;
                                }
                            }
                        ],
                        // 'updated_at',
                        // 'updated_by',
                        // 'created_by',
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    const printReport = (userId) => {
        $('#modal').find('.modal-header').html('Admission Request Information');
        $('#modalContent').css('text-align', 'left');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $('#modalContent').html('Loading Student Information');
        // admit-student/admission-form-report?id
        $.get('/admit-student/admission-form-report?id=' + userId, {}).done(function(data) {
            $('#modalContent').html(data);
        });
    }
    window.loadAccountMgtFormModel = (infoOf, id) => {
        $('#modal').find('.modal-header').html('Fill ' + infoOf);
        $('#modalContent').css('text-align', 'left');
        $('#modalContent').html('Loading form data...');
        $('#modal').find('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/user/manage-account', {
            empId: id
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
    const showStudentInfoView = (studentId) => {
        $('#modal').find('.modal-header').html('Admission Request Information');
        $('#modalContent').css('text-align', 'left');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $('#modalContent').html('Loading Student Information');
        $.get('/student/view?id=' + studentId, {}).done(function(data) {
            $('#modalContent').html(data);
        });
    }
    const changeAccountStatus = (option, userId) => {
        if (confirm('Are you sure to change current account status?')) {
            $.post('/user/change-account-status', {
                userId: userId,
                option: option
            }).done((resp) => {
                if (resp == 1) {
                    window.location.href = window.location;
                } else {
                    alert(resp);
                }
            });
        }
    }
</script>