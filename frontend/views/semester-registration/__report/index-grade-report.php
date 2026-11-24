<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

/** @var yii\web\View $this */
/* @var $searchModel frontend\models\SemesterRegistration2Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Semester Registration');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-registration-index table-responsive">
    <h3><?= $this->title ?>
        <p class="pull-right">
            <?= Yii::$app->user->can('Student') ? "" : '<button class="btn btn-xs btn-info" onclick="printReport3();return false;" title="Print Active Student List" style="margin-right: 2px;">Print</button>'; ?>

            <?= Yii::$app->user->can('Student') ? "" : '<button class="btn btn-xs btn-warning" onclick="printReport();return false;" title="Print Semester Status Report" style="margin-right: 2px;">Print</button>'; ?>

            <?= Yii::$app->user->can('Student') ? "" : '<button class="btn btn-xs btn-danger" onclick="printReport(1);return false;" title="Display backloag report" style="margin-right: 2px;">Print Back-Log</button>'; ?>
        </p>
    </h3>
    <?php

    Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'stdId',
                'value' => function ($model) {
                    $model->upgradeSrd2();
                    return $model->semregStdRel->std_regId;
                }
            ],
            [
                'attribute' => 'first_name',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->semregStdRel->getFullname(), ['/student/view', 'id' => $model->semreg_std_relId], ['data-pjax' => 0]);
                }
            ],

            [
                'attribute' => 'semreg_acadYear_relId',
                'value' => function ($model) {
                    return $model->semregAcadYearRel->sch_year_desc;
                },
                'filter' => ArrayHelper::map(\backend\models\SemesterRegistration2::find()->groupBy('semreg_acadYear_relId')->all(), 'semreg_acadYear_relId', function ($model) {
                    return $model->semregAcadYearRel->sch_year_desc;
                }),
            ],
            [
                'attribute' => 'semreg_semester_relId',
                'value' => function ($model) {
                    return $model->semregSemesterRel->semester_name;
                },
                'filter' => ArrayHelper::map(\backend\models\SemesterRegistration2::find()->groupBy('semreg_semester_relId')->all(), 'semreg_semester_relId', function ($model) {
                    return $model->semregSemesterRel->semester_name;
                }),
            ],
            [
                'attribute' => 'semreg_clslvl_relId',
                'format' => 'raw',
                'value' => function ($model) {

                    return $model->semregClslvlRel->getClslvlDisp();
                },
                // 'title' => function($model){
                //     return $model->semregClslvlRel->clslvlProgramrel->program_name;
                // },
                'filter' => ArrayHelper::map(\backend\models\SemesterRegistration2::find()->groupBy('semreg_clslvl_relId')->all(), 'semreg_clslvl_relId', function ($model) {
                    return $model->semregClslvlRel->clslvl_name . "-{$model->semregClslvlRel->clslvlProgramRel->program_name}";
                }),
            ],

            [
                'attribute' => 'std_prog_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->semregStdRel->stdProgRel->program_name;
                },
                'filter' => ArrayHelper::map(\backend\models\SemesterRegistration2::find()->innerJoin('student', 'student.id = semester_registration_2.semreg_std_relId')->groupBy('student.std_prog_relId')->all(), function ($model) {
                    return $model->semregStdRel->std_prog_relId;
                }, function ($model) {
                    return $model->semregStdRel->stdProgRel->program_name;
                }),
            ],
            [
                'attribute' => 'std_dept_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    $gset = $model->getStudentGroup();
                    if ($gset) {
                        $gset = $gset->gasGsetRel;
                        return $gset->gsDeptRel->dept_name;
                    } else {
                        NULL;
                    }
                },
                'filter' => ArrayHelper::map(\backend\models\SemesterRegistration2::find()->innerJoin('student', 'student.id = semester_registration_2.semreg_std_relId')->groupBy('student.std_dept_relId')->all(), function ($model) {
                    return $model->semregStdRel->std_dept_relId;
                }, function ($model) {
                    return $model->semregStdRel->stdDeptRel->dept_name;
                }),
            ],

            [
                'attribute' => 'semreg_SGPA',
                'format' => 'raw',
                'value' => function ($model) {
                    if (Yii::$app->user->can('Student')) {
                        return $model->semreg_gradeCompleted ? ($model->evaluatedAll() ? sprintf("%0.3f", $model->semreg_SGPA) : '<span title="Pending evaluation!" class="bg-warning">PE</span>') : '<span title="pending grade submission">PG</span>';
                    } else {
                        return $model->semreg_gradeCompleted ? sprintf("%0.3f", $model->semreg_SGPA) : '<span title="pending grade submission" class="bg-warning">PG</span>';
                    }
                },
            ],

            [
                'attribute' => 'semreg_CGPA',
                'format' => ['decimal', 3],
                'value' => function ($model) {
                    if (Yii::$app->user->can('Student')) {
                        return $model->semreg_gradeCompleted ? ($model->evaluatedAll() ? $model->semreg_CGPA : NULL) : NULL;
                    } else {
                        return $model->semreg_gradeCompleted ? $model->semreg_CGPA : NULL;
                    }
                },
            ],

            [
                'attribute' => 'semreg_last_ssn_relId',
                'value' => function ($model) {
                    return $model->semesterLastSsnRel ? $model->semesterLastSsnRel->sssn_name : '-';
                },
                'filter' => ArrayHelper::map(\backend\models\SemesterRegistration2::find()->groupBy('semreg_last_ssn_relId')->all(), 'semreg_last_ssn_relId', function ($model) {
                    return $model->semesterLastSsnRel ? $model->semesterLastSsnRel->sssn_name : '-';
                }),
            ],
            [
                'attribute' => 'semreg_gradeCompleted',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->semreg_gradeCompleted ? "<span class='bg-success'>Ready<span>" : NULL;
                },
            ],

            [
                'attribute' => 'Option',
                'format' => 'raw',
                // 'visible' => function($model){
                //     return $model->evaluatedAll();
                // },
                'value' => function ($model) {
                    return Html::a(
                        Yii::t('rms', 'Course Grade'),
                        [''],
                        [
                            'class' => 'btn btn-success btn-xs',
                            'data-pjax' => 0,
                            'title' => 'Show Course Grade',
                            'value' => $model->id,
                            'onclick' => 'showEnrollments(' . $model->id . ');return false;',
                        ]
                    );
                }
            ],
            [
                'attribute' => 'Option',
                'format' => 'raw',
                'visible' => !Yii::$app->user->can('Student'),
                'value' => function ($model) {
                    return Html::a(
                        Yii::t('rms', 'Backlog'),
                        [''],
                        [
                            'class' => 'btn btn-warning btn-xs',
                            'data-pjax' => 0,
                            'title' => 'Show Back-Log History',
                            'value' => $model->id,
                            'onclick' => 'showBacklog(' . $model->semreg_std_relId . ');return false;',
                        ]
                    );
                }
            ],
            [
                'attribute' => 'Option',
                'format' => 'raw',
                'visible' => Yii::$app->user->can('Admin'),
                'value' => function ($model) {
                    return Html::a(
                        Yii::t('rms', 'Grade Report'),
                        [''],
                        [
                            'class' => 'btn btn-success btn-xs',
                            'data-pjax' => 0,
                            'title' => 'Generate Grade Report',
                            'value' => $model->id,
                            'onclick' => 'gradeReport(' . $model->id . ');return false;',
                        ]
                    );
                }
            ],
            [
                'attribute' => 'Option',
                'format' => 'raw',
                'visible' => Yii::$app->user->can('DepartmentHead'),
                'value' => function ($model) {
                    return Html::a(
                        Yii::t('rms', 'Withdraw'),
                        [''],
                        [
                            'class' => 'btn btn-warning btn-xs',
                            'data-pjax' => 0,
                            'title' => 'Process student withdrawal',
                            'value' => $model->id,
                            'onclick' => 'withdrawal(' . $model->id . ');return false;',
                        ]
                    );
                }
            ],

            [
                'attribute' => 'Option',
                'format' => 'raw',
                'visible' => Yii::$app->user->can('Registrar'),
                'value' => function ($model) {
                    return Html::a(
                        Yii::t('rms', 'Delete'),
                        [''],
                        [
                            'class' => 'btn btn-danger btn-xs',
                            'data-pjax' => 0,
                            'title' => 'Remove Semester Registration Record',
                            'value' => $model->id,
                            'onclick' => 'removeEntry(' . $model->id . ');return false;',
                        ]
                    );
                }
            ],

            // 'semreg_gradeCompleted',
            // 'semreg_approved',
            // 'semreg_approved_by',
            // 'semreg_approved_at',
            // 'semreg_created_by',
            // 'semreg_updated_by',
            // 'semreg_created_at',
            // 'semreg_updated_at',

            // [
            //     // 'class' => 'yii\grid\ActionColumn'
            // ],
        ],
    ]); ?>

    <script type="text/javascript">
        __semregId = null;
        const printReport3 = () => {
            window.open(
                '/semester-registration/active-student-list',
                'title=Active Students',
                'height=1100,width=6000');
        }
        window.removeEntry = (semregId) => {
            if (confirm('Are you sure to remove this entry?')) {
                $.post('/semester-registration/delete?id=' + semregId).done(function(data) {
                    if (data == 1) {
                        window.location.href = window.location;
                    } else {
                        alert(data);
                    }
                });
            }
        }

        window.withdrawal = (semregId) => {
            var window2 = window.open("/WithdrawalReadmission/w-student-withdrawal-history/create?hide-content=1&bool=true&semregId=" + semregId, "_blank");
            window2.focus();
            return false;
        }

        window.showBacklog = (stdId) => {
            window.open(
                '/add-history/get-report-print-backlog?id=' + stdId,
                'title=Student Back-Log History',
                'height=1100,width=6000');
        }
        window.printReport = (backLog) => {
            if (backLog == 1) {
                myWindow = window.open(
                    '/semester-registration/get-report-print-backlog',
                    'title=Student Back-Log History',
                    'height=1100,width=8000')
            } else {
                myWindow = window.open(
                    '/semester-registration/get-report-print',
                    'title=Semester Status Report',
                    'height=1100,width=8000');
            }

        }
        window.gradeReport = (semregId) => {
            __semregId = semregId;
            myWindow = window.open(
                '/semester-registration/get-semester-grade-report?semregId=' + semregId,
                'title=Student Semester Grade Report',
                'height=1100,width=700');
        }
        window.showEnrollments = (semregId) => {
            __semregId = semregId;
            $('#modal').find('.modal-header').html('Course Enrollments');
            $('#modalContent').css('text-align', 'left');
            $('#modalContent').html('Loading data...');
            $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
            $('#modal').modal('show');
            $.post('/semester-registration/get-srd2-detail', {
                semregId: semregId
            }).done(function(data) {
                $('#modalContent').html(data);
            });
        }
    </script>
    <?php Pjax::end();
    ?>
</div>

<?php
// $y = \Yii::$app->user->isGuest;
Modal::begin([
    'header' => '<h4></h4>',
    'id' => 'modal',
    'size' => 'modal-sm',
]);
echo "<div id='modalContent'>Loading report...</div>";
Modal::end();
?>