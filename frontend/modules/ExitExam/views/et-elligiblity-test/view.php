<?php

use common\components\CommonControllerFunctions;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var $this yii\web\View */
/* @var $model frontend\modules\ExitExam\models\EtElligiblityTest */

$std = $model->semregRel->semregStdRel;
$user = $std->stdUserRel;
$this->title = "Exit Exam: " . $std->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Elligiblity Test Trials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="et-elligiblity-test-view">

    <h3><?= Html::encode($this->title) ?>
        <?= Yii::$app->user->can('Admin') || Yii::$app->user->can('Registrar') ? "&nbsp;<button class='btn btn-primary btn-xs pull-right' onclick='printTempo({$std->id},{$std->std_group_realId})' title='Print Trial Certificate'> Report </button>" : "";
        ?>
    </h3>
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <center>
                        <img src="/uploads/<?= $user->photo_num ?>" alt="" class="img-circle" alt="Profile Image"
                            height="100" width="80">
                        <p style="font-size: 7pt;"><?= $std->std_regId  ?></p>
                    </center>
                </div>
                <div class="panel-body">
                    <?= DetailView::widget([
                        'model' => $std,
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
                                    return Html::a($model->stdUserRel->getFullName(), ['/student/view', 'id' => $model->id], ['target' => '_blank']);
                                }
                            ],
                            'std_type',
                            [
                                'attribute' => 'std_admission_relId',
                                'value' => function ($model) {
                                    return $model->stdAdmissionRel->admission_name . ' ' . $model->stdProgRel->program_name;
                                }
                            ],
                            [
                                'attribute' => 'Sponsorship',
                                'value' => function ($model) {
                                    return $model->stdScholarshpTypeRel->schType_name;
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
                                'label' => 'Recent Section Assignment',
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
                            // [
                            //     'attribute' => 'id',
                            //     'label' => 'Registration Code',
                            //     'value' => function ($model) {
                            //         return $model->stdUserRel->OTP;
                            //     }
                            // ],
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
        <div class="col-md-8">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [

                    [
                        'attribute' => 'semreg_relId',
                        'value' => function ($model) {
                            $std = $model->semregRel->semregStdRel;
                            return $std->std_regId . ' - ' . $std->getFullName();
                        },
                    ],
                    [
                        'attribute' => 'srd2_relId',
                        'visible' => $model->srd2_relId != null,
                        'value' => function ($model) {
                            return $model->srd2Rel ? $model->srd2Rel->srd2CourseRel->getCourseCredits() : '';
                        },
                        // 'filter' => $model->_reasons,
                    ],
                    [
                        'attribute' => 'reason',
                        'value' => function ($model) {
                            return $model->_reasons[$model->reason];
                        },
                        // 'filter' => $model->_reasons,
                    ],
                    [
                        'attribute' => 'Trials',
                        'value' => function ($model) {
                            if ($model->trails != (count($model->etTestDetails))) {
                                $model->trails = count($model->etTestDetails);
                                $model->save();
                                $model->refresh();
                            }
                            return "{$model->trails}/3: Remaining: {$model->remaining_trails}";
                        },
                        // 'filter' => $model->_reasons,
                    ],
                    // 'trails',
                    // 'remaining_trails',
                    [
                        'attribute' => 'pass_status',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $status =  $model->getPassStatus();
                            if (is_string($status)) {
                                return $status;
                            } else {
                                $bg = ($model->pass_status == 1  && $status[1]) ? 'green' : 'red'; //$status=> [pass_status,approval]
                                return "<span class='label label-default' style='background-color:{$bg};'>{$status[0]}</span>";
                            }
                        },
                        // 'filter' => $model->_pass_status,
                    ],
                    [
                        'attribute' => 'Attempts',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $this->render('__reports/attempts', [
                                'model' => $model,
                            ]);
                        },
                        // 'filter' => $model->_pass_status,
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
                    // 'updated_at',
                    // 'updated_by',
                    // 'data:ntext',
                ],
            ]) ?>
        </div>
    </div>

</div>

<script type="text/javascript">
    const printTempo = (stdId, gsetId) => {
        myw3 = window.open(
            '/student/print-temporary-certificate?studentId=' + stdId + "&gsetId=" + gsetId,
            'title=Temporary Certificate',
            'height=1100,width=1100');
    }
</script>