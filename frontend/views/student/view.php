<?php

use common\components\CommonControllerFunctions;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\Student */

$userObject = \common\models\User1::findOne($model->std_user_relId);
$this->params['userObject'] = $userObject;
$this->title = 'Student - ' . $model->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Students'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$model->calculateGPAs();
?>
<div class="student-view">
    <h3><?= Html::encode("Student Profile") ?>
        <div class="<?= (Yii::$app->language == 'ar') ? 'pull-left' : 'pull-right' ?>">
            <?php if (Yii::$app->user->can("Registrar") || Yii::$app->user->can("Admin") || Yii::$app->user->can("clerk")) { ?>
                <!-- <?= Html::a('<i class="fa fa-file-pdf-o"></i> ' . Yii::t('stu', 'Generate PDF'), ['student/student-profile-pdf', 'sid' => $model->id], ['class' => 'btn-sm btn btn-warning', 'id' => 'export-pdf', 'target' => 'blank']) ?>
            <?php } ?> -->
        </div>
    </h3>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5">
                <div class="panel panel-default">
                    <div class="panel-heading" style="align-content: center;">
                        <center>
                            <img src="/uploads/<?= $userObject->photo_num ?>" alt="" class="img-circle" alt="Profile Image" height="100" width="80">
                            <p style="font-size: 7pt;"><?= $model->std_regId  ?></p>
                        </center>
                    </div>
                    <div class="panel-body">
                        <?= DetailView::widget([
                            'model' => $model,
                            'attributes' => [
                                [
                                    'attribute' => 'std_regId',
                                    'format' => 'raw',
                                    'label' => 'Reg. ID',
                                    'value' => function ($model) {
                                        $btnChange = "";
                                        // (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')) ?
                                        // Html::button('Change',['class'=> 'btn btn-xs pull-right btn-warning','onclick' => "chandregId({$model->id});return false;"]):
                                        // '';
                                        if ($model->regNumDuplicate()) {
                                            return $model->std_regId . ' <span class="label label-danger pull-right">Duplicate</span>';
                                        }
                                        return $model->std_regId . $btnChange;
                                    }
                                ],
                                [
                                    'attribute' => 'std_user_relId',
                                    'Label' => 'Name',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        $fullName_am = $model->getFullName_am();
                                        if ($fullName_am == NULL) {
                                            $btnam =  Html::button('Set', ['class' => 'btn btn-xs btn-info pull-right', 'onclick' => "setNameAmharic({$model->id});return false;"]);
                                            $fullName_am = "<small class='bg-warning'>Not Set</small>";
                                        }else{
                                            $btnam =  Html::button('Update', ['class' => 'btn btn-xs btn-warning pull-right', 'onclick' => "setNameAmharic({$model->id});return false;"]);
                                        }

                                        return $model->getFullName() . "<br><strong style='border-top: 1px solid black;'>" . $fullName_am."{$btnam}</strong>";
                                    }
                                ],
                                [
                                    'attribute' => 'std_type',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        $apt = 'std_type';
                                        $relationName = NULL;
                                        $relatedAttributeName = NULL;
                                        $pendingRequest = $model->checkPendingStatusChange($apt);
                                        if ($pendingRequest) {
                                            $pp = $pendingRequest->relation_name ? $pendingRequest->stdRel->$relationName::findOne(["id" => $pendingRequest->new_value])->{$pendingRequest->related_attribute_name} :
                                                $pendingRequest->new_value;
                                            $btnView =  Html::button('View', ['class' => 'btn btn-xs btn-info pull-right', 'title' => "View Detail", 'onclick' => "viewChangeDetail({$pendingRequest->id});return false"]);
                                            if (Yii::$app->user->can('Registrar')) {
                                                $btnApprove = Html::button('Approve', ['class' => 'btn btn-xs btn-primary pull-right', 'title' => "Approve request", 'onclick' => "approveRequest({$pendingRequest->id});return false"]);
                                                $btn = "<small class='pull-right bg-warning' title=' [{$pp}] Change request pending approval'>Pending {$btnView}{$btnApprove}</small>";
                                            } else {
                                                $btn = "<small class='pull-right bg-warning' title=' [{$pp}] Change request pending approval'>Pending {$btnView}</small>";
                                            }
                                        } else {
                                            $btn = Html::button('Change', ['class' => 'btn btn-xs btn-warning pull-right', 'title' => "Change User type", 'onclick' => "changeProfile({$model->id},\"{$apt}\",\"{$relationName}\",\"{$relatedAttributeName}\");return false"]);
                                        }
                                        return $model->std_type . $btn;
                                    }
                                ],
                                [
                                    'attribute' => 'std_admission_relId',
                                    'value' => function ($model) {
                                        return $model->stdAdmissionRel->admission_name . ' ' . $model->stdProgRel->program_name;
                                    }
                                ],
                                [
                                    'attribute' => 'Sponsorship',
                                    'format' => 'raw',
                                    'value' => function ($model) {

                                        $apt = 'std_schlrshpType_relId';
                                        $relationName = 'stdScholarshpTypeRel';
                                        $relatedAttributeName = 'schType_name';
                                        $pendingRequest = $model->checkPendingStatusChange($apt);
                                        if ($pendingRequest) {
                                            $pp = $pendingRequest->relation_name ? $pendingRequest->stdRel->$relationName::findOne(["id" => $pendingRequest->new_value])->{$pendingRequest->related_attribute_name} :
                                                $pendingRequest->new_value;
                                            $btnView =  Html::button('View', ['class' => 'btn btn-xs btn-info pull-right', 'title' => "View Detail", 'onclick' => "viewChangeDetail({$pendingRequest->id});return false"]);
                                            if (Yii::$app->user->can('Registrar')) {
                                                $btnApprove = Html::button('Approve', ['class' => 'btn btn-xs btn-primary pull-right', 'title' => "Approve request", 'onclick' => "approveRequest({$pendingRequest->id});return false"]);
                                                $btn = "<small class='pull-right bg-warning' title=' [{$pp}] Change request pending approval'>Pending {$btnView}{$btnApprove}</small>";
                                            } else {
                                                $btn = "<small class='pull-right bg-warning' title=' [{$pp}] Change request pending approval'>Pending {$btnView}</small>";
                                            }
                                        } else {
                                            $btn = Html::button('Change', ['class' => 'btn btn-xs btn-warning pull-right', 'title' => "Change Sponsorship type", 'onclick' => "changeProfile({$model->id},\"{$apt}\",\"{$relationName}\",\"{$relatedAttributeName}\");return false"]);
                                        }
                                        return $model->$relationName->$relatedAttributeName . $btn;
                                    }
                                ],
                                [
                                    'attribute' => 'Email',
                                    'value' => function ($model) {
                                        return $model->stdUserRel->email;
                                    }
                                ],
                                [
                                    'attribute' => 'Phone',
                                    'value' => function ($model) {
                                        return $model->stdUserRel->phone_mobile;
                                    }
                                ],
                                [
                                    'attribute' => 'std_acadYear_relId',
                                    'value' => function ($model) {
                                        $sem = $model->std_sem_relId ? $model->stdSemRel->semester_name : 'Semester';
                                        return $model->stdAcadYearRel->sch_year_desc . '-' . $sem;
                                    },
                                ],
                                [
                                    'attribute' => 'entry_type',
                                    'value' => function ($model) {
                                        return $model->entry_type == 1 ? 'Regular' : 'Transfer';
                                    },
                                ],
                                [
                                    'attribute' => 'std_group_realId',
                                    'value' => function ($model) {
                                        if (!is_null($model->std_group_realId)) {
                                            return $model->stdGroupReal->gsGroupRel->group_name . "|> " . $model->stdGroupReal->gsDeptRel->dept_name;
                                            // return \backend\models\WGroup::findOne($model->stdGroupReal->gs_group_relId)->group_name;
                                        } else {
                                            CommonControllerFunctions::currentStudentGroup($model);
                                            if ($model->stdGroupReal) {
                                                return $model->stdGroupReal->gsGroupRel->group_name . "|> " . $model->stdGroupReal->gsDeptRel->dept_name;
                                            }
                                        }
                                        return 'Not Assigned';
                                    }
                                ],
                                [
                                    'attribute' => 'id',
                                    'label' => 'Registration Code',
                                    'value' => function ($model) {
                                        return $model->stdUserRel->OTP;
                                    }
                                ],
                                [
                                    'attribute' => 'std_clslvl_relId',
                                    'format' => 'raw',
                                    'value' => function ($model) {
                                        if (!is_null($model->std_group_realId)) {
                                            return $model->stdGroupReal->gsClslvlRel->getClslvlDisp();
                                            // return \backend\models\ClassLevel::findOne($model->std_clslvl_relId)->clslvl_name;
                                        }
                                        return 'Uknown';
                                    }
                                ],
                                'std_semester_status',
                                [
                                    'attribute' => 'std_created_at',
                                    'value' => function ($model) {
                                        return date_format(date_create($model->std_created_at), 'd M, Y');
                                    }
                                ],
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <ul class="nav nav-tabs responsive" id="profileTab" style="background-color: #8D9; padding: 4px;">
                    <li class="active" id="personal-tab"><a href="#personal" data-toggle="tab"><i class="fa fa-street-view"></i> <?php echo Yii::t('rms', 'Personal'); ?></a></li>

                    <li id="academic-tab"><a href="#academic" data-toggle="tab"><i class="fa fa-graduation-cap"></i> <?php echo Yii::t('rms', 'Academic'); ?></a></li>
                    <li id="guardians-tab"><a href="#guardians" data-toggle="tab"><i class="fa fa-user"></i> <?php echo Yii::t('rms', 'Emergency Contact'); ?></a></li>

                    <li id="address-tab"><a href="#address" data-toggle="tab"><i class="fa fa-home"></i> <?php echo Yii::t('rms', 'Address'); ?></a></li>

                    <li id="documents-tab"><a href="#documents" data-toggle="tab"><i class="fa fa-file-text"></i> <?php echo Yii::t('rms', 'Documents'); ?></a></li>
                    <!-- session->get('stu_id') -->
                    <?php if (!Yii::$app->user->can('Student')) : ?>
                        <li id="fees-tab"><a href="#fees" data-toggle="tab"><i class="fa fa-inr"></i> <?php echo Yii::t('rms', 'Fees'); ?></a></li>
                    <?php endif; ?>

                    <li id="semester-registration-tab"><a href="#semester-registration" data-toggle="tab"><i class="fa fa-file-text"></i> <?php echo Yii::t('rms', 'Semester Reports'); ?></a></li>

                    <li id="course-completion-tab"><a href="#course-completion" data-toggle="tab"><i class="fa fa-pencil"></i> <?php echo Yii::t('rms', 'Course Completion'); ?></a></li>

                    <li id="group-assignment-tab"><a href="#group-assignment" data-toggle="tab"><i class="fa fa-group"></i> <?php echo Yii::t('rms', 'Group Assignments'); ?></a></li>
                </ul>
                <div id='content' class="tab-content responsive">
                    <div class="tab-pane active" id="personal">
                        <?= $this->render('_tab_stu_personal', ['info' => $userObject, 'model' => $model]) ?>
                    </div>
                    <div class="tab-pane" id="academic">
                        <?= $this->render('_tab_stu_academic', ['modelUser' => $userObject, 'modelStudent' => $model]) ?>
                    </div>

                    <div class="tab-pane" id="guardians">
                        <?= $this->render('_tab_stu_guardians', ['modelUser' => $userObject, 'modelStudent' => $model]) ?>
                    </div>

                    <div class="tab-pane" id="address">
                        <?= $this->render('_tab_stu_address', ['modelUser' => $userObject, 'modelStudent' => $model]) ?>
                    </div>

                    <div class="tab-pane" id="semester-registration">
                        <?= $this->render('_tab_stu_semreg', ['modelStudent' => $model]) ?>
                    </div>

                    <div class="tab-pane" id="course-completion">
                        <?= $this->render('_tab_stu_cocomp', ['modelStudent' => $model]) ?>
                    </div>

                    <div class="tab-pane" id="documents">
                        <?= strtoupper($model->stdProgRel->program_name) == 'MASTERS' ?
                            $this->render('_tab_stu_documents_pg', ['modelUser' => $userObject, 'modelStudent' => $model]) :
                            $this->render('_tab_stu_documents', ['modelUser' => $userObject, 'modelStudent' => $model]) ?>
                    </div>

                    <div class="tab-pane" id="fees">
                        <?= $this->render('_tab_stu_fees', ['modelStudent' => $model]) ?>
                    </div>

                    <div class="tab-pane" id="group-assignment">
                        <?= $this->render('_tab_group_assignment', ['modelStudent' => $model]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade col-xs-12 col-lg-12" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="photoup">
        <div class="modal-dialog">
            <div class="modal-content row">
            </div>
        </div>
    </div>
    <script>
        window.setNameAmharic = (id) => {
            name_am = prompt("Enter Student Name in Amharic: (Space separated Fullname) ");
            if (name_am) {
                if (confirm('Sure to submit?')) {
                    $.post('/student/set-am-name', {
                        id: id,
                        name_am: name_am
                    }).done((resp) => {
                        alert(resp);
                    });
                }
            }
        }
        window.approveRequest = (id) => {
            if (confirm('Are younsure to approve status change')) {
                $.post('/student-profile-change/approve-request', {
                    id: id
                }).done((resp) => {
                    alert(resp);
                });
            }
        }
        window.viewChangeDetail = (id) => {
            window.location.href = "/student-profile-change/view?id=" + id;
        }
        window.changeProfile = (id, attribute, relationName, relatedAttributeName) => {
            // console.log(id, attribute);
            $('#modal').find('.modal-header').html('Student Profile Change Request Form');
            $('#modalContent').css('text-align', 'left');
            $('#modalContent').html('Loading data...');
            $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
            $('#modal').modal('show');
            $.get('/student/request-profile-change', {
                stdId: id,
                attribute: attribute,
                relationName: relationName,
                relatedAttributeName: relatedAttributeName
            }).done(function(data) {
                $('#modalContent').html(data);
            });
        }
    </script>
    <?php $this->registerJs("(function($) {
          fakewaffle.responsiveTabs(['xs', 'sm']);
      })(jQuery);", yii\web\View::POS_END, 'responsive-tab');
    ?>
</div>