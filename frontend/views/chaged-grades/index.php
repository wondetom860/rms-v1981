<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;

/**  @var yii\web\View  $this
 * @var $searchModel backend\models\TouchedGradesSearch 
 * @var $dataProvider yii\data\ActiveDataProvider 
 */

$this->title = Yii::t('rms', 'Changed Grades');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="touched-grades-index table-responsive">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Yii::$app->user->can('Admin') ? Html::a(Yii::t('rms', 'Create grade change request'), ['create'], ['class' => 'btn btn-success btn-xs']) : "" ?>
            <button class="btn btn-xs btn-warning" onclick="printReport();return false;" title="Print selected list">Print</button>
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

            [
                'attribute' => 'tg_acadYear_relId',
                'value' => 'tgAcadYearRel.sch_year_desc',
                'filter' => ArrayHelper::map(\backend\models\TouchedGrades::find()->groupBy('tg_acadYear_relId')->all(), 'tg_acadYear_relId', function ($model) {
                    return $model->tgAcadYearRel->sch_year_desc;
                }),
            ],
            [
                'attribute' => 'tg_sem_relId',
                'value' => 'tgSemRel.semester_name',
                'filter' => ArrayHelper::map(\backend\models\TouchedGrades::find()->groupBy('tg_sem_relId')->all(), 'tg_sem_relId', function ($model) {
                    return $model->tgSemRel->semester_name;
                }),
            ],
            [
                'attribute' => 'tg_prog_relId',
                'value' => 'tgProgRel.program_name',
                'filter' => ArrayHelper::map(\backend\models\TouchedGrades::find()->groupBy('tg_prog_relId')->all(), 'tg_prog_relId', function ($model) {
                    return $model->tgProgRel->program_name;
                }),
            ],
            [
                'attribute' => 'tg_clslvl_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->tgClslvlRel->getClslvlDisp();
                },
                'filter' => ArrayHelper::map(\backend\models\TouchedGrades::find()->groupBy('tg_clslvl_relId')->all(), 'tg_clslvl_relId', function ($model) {
                    return $model->tgClslvlRel->clslvl_num . "-{$model->tgClslvlRel->clslvlProgramRel->program_name}";
                }),
            ],
            [
                'attribute' => 'first_name',
                'value' => function ($model) {
                    // $user = ->stdUserRel;
                    return $model->tgSrd2Rel->srd2SemregRel->semregStdRel->getFullName();
                }
            ],
            [
                'attribute' => 'courseTitle',
                'value' => function ($model) {
                    return $model->tgSrd2Rel->srd2CourseRel->course_title;
                }
            ],
            [
                'attribute' => 'tg_beforeGrade_relId',
                'contentOptions' => [
                    'style' => 'text-align:center; font-size:9pt;',
                ],
                'value' => function ($model) {
                    return $model->tgBeforeGradeRel ? $model->tgBeforeGradeRel->gs_ltr_grade : '-';
                }
            ],
            [
                'attribute' => 'tg_afterGrade_relId',
                'contentOptions' => [
                    'style' => 'text-align:center; font-size:9pt;',
                ],
                'value' => function ($model) {
                    return $model->tgAfterGradeRel ? $model->tgAfterGradeRel->gs_ltr_grade : '-';
                }
            ],

            [
                'attribute' =>  'tg_reason',
                'contentOptions' => [
                    'style' => 'word-break: break-all; white-space: pre-line; font-size:9pt;',
                    // 'title' => function($model){ return $model->tg_reason; },
                ],
                'value' => 'tg_reason',
            ],

            [
                'attribute' => 'tg_approved_by',
                'label' => 'Approval Status',
                'format' => 'raw',
                'value' => function ($model) {
                    if (Yii::$app->user->can('Registrar')) {
                        if (!is_null($model->tg_approved_by)) {
                            // request is already approved.
                            return "Approved, <button class='btn btn-xs btn-danger' onclick='changeStatus({$model->id},2);return false;' title='Cancel Approval'>Change</button>";
                        } else {
                            return "<button class='btn btn-xs btn-primary' onclick='changeStatus({$model->id},1);return false;' title='Accept request'>Approve</button>";
                        }
                    } else {
                        if (!is_null($model->tg_approved_by)) {
                            // request is already approved.
                            return "Approved at " . date_format(date_create($model->tg_approved_at), 'd M, Y');
                        } else {
                            return "Waiting approval by Registrar";
                        }
                    }
                }
            ],

            [
                'attribute' => 'tg_approved_at',
                'visible' => false,
                'value' => function ($model) {
                    return $model->tg_approved_at ?   date_format(date_create($model->tg_approved_at), 'd M, Y') : 'NOT APPROVED YET';
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'visible' => Yii::$app->user->can('Admin'),
            ],
        ],
    ]); ?>

    <script type="text/javascript">
        window.printReport = () => {
            window.open(
                '/chaged-grades/get-report-print',
                'title=Changed Grades List Report',
                'height=1100,width=6000');
        }
        window.changeStatus = (requestId, option) => {
            if (confirm('Are you sure to change request status?')) {
                url = option == 1 ? '/chaged-grades/approve-grade-change' : '/chaged-grades/reject-grade-change';
                $.post(url, {
                    reqId: requestId,
                    option: option
                }).done(function(data) {
                    if (data == 1) {
                        window.location.href = window.location;
                    } else {
                        alert(data);
                    }
                });
            }
        }
    </script>
    <?php Pjax::end(); ?>

</div>