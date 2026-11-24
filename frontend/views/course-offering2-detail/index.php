<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

/** @var yii\web\View $this */
/* @var $searchModel backend\models\CourseOffering2DetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Course Offering and Enrollment Detail');
$this->params['breadcrumbs'][] = $this->title;
$btn = yii::$app->user->can('AcademicDean') || yii::$app->user->can('DepartmentHead') ? "<button title='Generate instructor load report of active academic year' class='btn btn-xs btn-info pull-right' onclick='generateInstructorLaod();return false;'>Instructor Load Report</button>" : '';
?>
<div class="course-offering2-detail-index table-responsive">

    <h4><?= Html::encode($this->title) ?><?= $btn ?></h4>
    <?= "<button class='btn btn-warning btn-xs pull-right' onclick='printContent();return false;'>Print</button>" ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'acadYear',
                'value' => function ($model) {
                    return $model->coff2detCoff2Rel->coff2AcadYearRel->sch_year_desc;
                },
                'filter' => ArrayHelper::map(\backend\models\CourseOffering2Detail::find()->innerJoin('course_offering2', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')->groupBy('coff2_acadYear_relId')->all(), function ($model) {
                    return $model->coff2detCoff2Rel->coff2_acadYear_relId;
                }, function ($model) {
                    return $model->coff2detCoff2Rel->coff2AcadYearRel->sch_year_desc;
                })
            ],
            [
                'attribute' => 'sem',
                'value' => function ($model) {
                    return $model->coff2detCoff2Rel->coff2SemRel->semester_name;
                },
                'filter' => ArrayHelper::map(\backend\models\CourseOffering2Detail::find()->innerJoin('course_offering2', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')->groupBy('coff2_sem_relId')->all(), function ($model) {
                    return $model->coff2detCoff2Rel->coff2_sem_relId;
                }, function ($model) {
                    return $model->coff2detCoff2Rel->coff2SemRel->semester_name;
                })
            ],
            [
                'attribute' => 'courseTitle',
                'format' => 'raw',
                'value' => function ($model) {
                    return "<a onclick='showCourseOfferingHistory({$model->id}); return false;'>[{$model->coff2detCourseRel->course_code}] " . $model->coff2detCourseRel->course_title . "</a>";
                }
            ],
            [
                'attribute' => 'department',
                'value' => function ($model) {
                    return $model->coff2detCoff2Rel->coff2GroupRel->gsDeptRel->dept_code;
                },
                'filter' => ArrayHelper::map(\backend\models\Department::find()->all(), 'dept_name', function ($model) {
                    return $model->dept_name;
                }),
            ],
            [
                'attribute' => 'group',
                'format' => 'raw',
                'value' => function ($model) {
                    $gset = $model->coff2detCoff2Rel->coff2GroupRel;
                    if ($gset) {
                        if ($gset->gsGroupRel) {
                            return $gset->gsGroupRel->group_name . "[{$gset->gsDeptRel->dept_code}-<i>{$gset->gsProgRel->program_name} {$model->coff2detCoff2Rel->coff2GroupRel->gsClslvlRel->getClslvlDisp()}</i>]";
                        } else {
                            return $gset->id;
                        }
                    } else {
                        return "";
                    }
                },
            ],
            [
                'attribute' => 'inst',
                'value' => function ($model) {
                    if ($model->coff2detCocatRel->cocat_courseProject == 1) {
                        return "N.A";
                    }
                    return $model->coff2detInstRel ? $model->coff2detInstRel->getFullName() : null;
                }
            ],
            [
                'attribute' => 'ta',
                'value' => function ($model) {
                    if ($model->coff2detCourseRel->course_lab_hour == 0 || $model->coff2detCocatRel->cocat_courseProject == 1) {
                        return "N.A";
                    }
                    return $model->coff2det_TA_relId ? $model->coff2detTARel->getFullName() : $model->coff2det_TA_relId;
                }
            ],

            [
                'attribute' => 'Option',
                'format' => 'raw',
                'value' => function ($model) {
                    $enrCount = $model->coff2detCoff2Rel->getSemesterRegistration2Details($model->coff2det_course_relId)->count();
                    $model->updateFaclutyAssignmentData();
                    return Html::a(
                        Yii::t('rms', "Show ({$enrCount})"),
                        [''],
                        [
                            'class' => 'btn btn-success btn-xs',
                            'data-pjax' => 0,
                            'title' => 'Show enrollments',
                            'value' => $model->id,
                            'onclick' => 'showEnrollments(' . $model->id . ');return false;',
                        ]
                    );
                }
            ],
            [
                'attribute' => 'coff2det_bypassEvaluation',
                'format' => 'raw',
                'visible' => Yii::$app->user->can('Registrar') || Yii::$app->user->can('DepartmentHead'),
                'value' => function ($model) {
                    return $model->coff2det_bypassEvaluation ?

                        Html::a(
                            Yii::t('rms', 'Require'),
                            [''],
                            [
                                'class' => 'btn btn-warning btn-xs',
                                'data-pjax' => 0,
                                'title' => 'Require Instructor/TA evaluation for showing course grade to students.',
                                'value' => $model->id,
                                'onclick' => 'ChangeStatusEvaluation(' . $model->id . ');return false;',
                            ]
                        ) :
                        Html::a(
                            Yii::t('rms', 'Bypass'),
                            [''],
                            [
                                'class' => 'btn btn-danger btn-xs',
                                'data-pjax' => 0,
                                'title' => 'Show course grade regardless of semester course Instructor/TA evaluation',
                                'value' => $model->id,
                                'onclick' => 'ChangeStatusEvaluation(' . $model->id . ');return false;',
                            ]
                        );
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'visibleButtons' => [
                    'view' => function ($model, $key, $index) {
                        return false;
                    },
                    'update' => function ($model, $key, $index) {
                        return Yii::$app->user->can('DepartmentHead');
                    },
                    'delete' => function ($model, $key, $index) {
                        return Yii::$app->user->can('Registrar');
                    },
                ]
            ],

            // 'coff2det_created_by',
            // 'coff2det_created_at',
            // 'coff2det_updated_at',
            // 'coff2det_updated_by',
        ],
    ]); ?>
    <script type="text/javascript">
        window.ChangeStatusEvaluation = (coff2detId) => {
            __coff2detId = coff2detId;
            if (confirm('Are you sure to change EVALAUTION_BY_PASS_STATUS? ')) {
                $.post('/course-offering2-detail/change-bypass-evaluation-status', {
                    coff2detId: coff2detId
                }).done((reply) => {
                    if (reply == 1) {
                        window.location.href = window.location;
                    } else {
                        alert('Evaluation bypass status change failed. Please try again.');
                    }
                });
            }
        }
        window.generateInstructorLaod = () => {
            myWindow = window.open(
                '/course-offering2-detail/get-inst-load-report-print2',
                'title=Course Offering report',
                'height=600,width=1550');
        }
        window.printContent = () => {
            myWindow = window.open(
                '/course-offering2-detail/get-offering-history-print',
                'title=Course Offering report',
                'height=600,width=1500');
        }
        window.showCourseOfferingHistory = (coff2detId) => {
            $('#modal').find('.modal-header').html('Course Offering History');
            $('#modalContent').css('text-align', 'left');
            $('#modalContent').html('Loading data...');
            $('#modalContent').addClass('table-reponsive');
            $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
            $('#modal').modal('show');
            $.get('/course-offering2-detail/get-offering-history', {
                coff2detId: coff2detId
            }).done(function(data) {
                $('#modalContent').html(data);
            });
        }
        window.showPointHistory = (stdId) => {
            $("#point_history_container").focus();
            $("#point_history_container").html('Loading semester registration & point history....');

            $('.btn__stds').removeClass('btn-info');
            $('#btn__std' + stdId).addClass('btn-info');

            $.post('/student/get-registration-history', {
                stdId: stdId
            }).done((resp) => {
                $("#point_history_container").html(resp);
            });
        }

        __coff2detId = null;
        window.showEnrollments = (coff2detId) => {
            __coff2detId = coff2detId;
            $('#modal').find('.modal-header').html('Course Enrollments');
            $('#modalContent').css('text-align', 'left');
            $('#modalContent').html('Loading data...');
            $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
            $('#modal').modal('show');
            $.get('/course-offering2-detail/get-enrollments', {
                coff2detId: coff2detId
            }).done(function(data) {
                $('#modalContent').html(data);
            });
            // return false;

            // window.open('/w-group-setup/get-assignments?wt_gsetUp_entry='+gsetId,"height=600,width=800","Student List");
        }
    </script>
    <?php Pjax::end(); ?>
</div>
<?php
// $y = \Yii::$app->user->isGuest;
Modal::begin([
    'header' => '<h4></h4>',
    'id' => 'modal',
    'size' => 'modal-lg',
]);
echo "<div id='modalContent'>Loading form...</div>";
Modal::end();
?>