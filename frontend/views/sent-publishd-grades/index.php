<?php

use backend\models\ClassLevel;
use backend\models\SchoolYear;
use backend\models\Semester;
use backend\models\SemesterRegistration2;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;

/** 
 * @var $this yii\web\View 
 * @var backend\models\SentPublishdGradesSearch $searchModel 
 * @var yii\data\ActiveDataProvider $dataProvider
 * 
 */
$semregModel = new SemesterRegistration2();
$this->title = Yii::t('rms', 'Attach Published Grades');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sent-publishd-grades-index">

    <h3><?= Html::encode($this->title) ?>
        button <?= Html::a(Yii::t('rms', 'Load Published Grades'), ['#'], ['class' => 'btn float-right pull-right btn-xs btn-success', 'onclick' => 'loadPublishedGrades()']) ?></h3>
    </h3>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <div class="row">
        <div class="col-md-12">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'acadYear',
                        'value' => function ($model) {
                            return $model->semregRel->semregAcadYearRel->sch_year_desc;
                        },
                        'label' => Yii::t('rms', 'Academic Year'),
                        'filter' => ArrayHelper::map(SchoolYear::find()->orderBy(['sch_year_desc' => SORT_DESC])->all(), 'id', 'sch_year_desc'),
                    ],
                    [
                        'attribute' => 'semester',
                        'value' => function ($model) {
                            return $model->semregRel->semregSemesterRel->semester_name;
                        },
                        'label' => Yii::t('rms', 'Semester'),
                        'filter' => ArrayHelper::map(Semester::find()->orderBy(['semester_name' => SORT_ASC])->all(), 'id', 'semester_name'),
                    ],
                    [
                        'attribute' => 'std_regId',
                        'value' => function ($model) {
                            return $model->semregRel->semregStdRel->std_regId;
                        },
                        'label' => Yii::t('rms', 'Student ID'),
                    ],
                    [
                        'attribute' => 'studentName',
                        'value' => function ($model) {
                            return $model->semregRel->semregStdRel->getFullName();
                        },
                        'label' => Yii::t('rms', 'Student Name'),
                    ],
                    [
                        'attribute' => 'class_level',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->semregRel->semregClslvlRel->getClslvlDisp();
                        },
                        'label' => Yii::t('rms', 'Class Level'),
                        'filter' => ArrayHelper::map(ClassLevel::find()->orderBy(['id' => SORT_ASC])->all(), 'id', function ($model) {
                            return $model->clslvl_name . ' (' . $model->clslvlProgramRel->program_name . ')';
                        }),
                    ],
                    'recipient_email:email',
                    'relation',
                    // 'reg_approved',
                    [
                        'attribute' => 'reg_approved',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if ($model->reg_approved == 1) {
                                return "<span class='badge badge-success alert-success'>Approved</span>";
                            } else {
                                return "<span class='badge badge-danger alert-danger'>Not Approved</span>";
                            }
                        },
                        'filter' => [
                            1 => 'Approved',
                            0 => 'Not Approved',
                        ],
                    ],
                    [
                        'attribute' => 'mail_sent',
                        'format' => 'raw',
                        'value' => function ($model) {
                            if ($model->mail_sent == 1) {
                                return "<span class='badge badge-success alert-success'>Sent</span>";
                            } else {
                                return "<span class='badge badge-danger alert-danger'>Not Sent</span>";
                            }
                        },
                        'filter' => [
                            1 => 'Sent',
                            0 => 'Not Sent',
                        ],
                    ],
                    //'date_sent',
                    //'grade_report_path',
                    // 'trail_count',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
        </div>
    </div>

    <?php Pjax::end(); ?>

</div>
<script>
    var __semregIds = null;
    const importStudentRecords = () => {
        if (__semregIds && __semregIds.length > 0) {
            $.post('<?= \yii\helpers\Url::to(['sent-publishd-grades/import-student-record']) ?>', {
                ids: JSON.stringify(__semregIds)
            }, function(response) {
                // Handle the response from the server
                console.log(response);
                // Optionally, you can close the modal or provide feedback to the user
                $('#modal').modal('hide');
                location.reload(); // Reload the page to reflect changes
            }).fail(function(xhr, status, error) {
                // Handle errors
                console.error('Error importing student records:', error);
            });
        } else {
            alert('No students selected for import.');
        }
    }
    const addToList = (semregId) => {
        if (!__semregIds) {
            __semregIds = [];
        }
        const index = __semregIds.indexOf(semregId);
        if (index === -1) {
            __semregIds.push(semregId);
        } else {
            __semregIds.splice(index, 1);
        }
    }
    const loadPublishedGrades = () => {
        $('#modal').find('.modal-header').html('Import Published Grades');
        $('#modalContent').css('text-align', 'left');
        $('#modalContent').html('Loading data...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['sent-publishd-grades/load-published-grades']) ?>',
            type: 'POST',
            // dataType: 'json',
        }).done((response) => {
            $('#modalContent').html(response);
            // $('#modalContent').parent().modal('show');
        }).fail((xhr, status, error) => {
            $('#modalContent').html('Error loading form: ' + error);
        });
    }
</script>