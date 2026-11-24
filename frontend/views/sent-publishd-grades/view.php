<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\SentPublishdGrades */

$this->title = $model->semregRel->semregStdRel->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Sent Publishd Grades'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sent-publishd-grades-view">

    <h3><?= Html::encode($this->title) ?>

        <?php if (!($model->reg_approved || $model->mail_sent)) : ?>

            <p class="pull-right float-right">
                <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => ' btn-xs btn btn-primary']) ?>
                <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger btn-xs',
                    'data' => [
                        'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>
            </p>
        <?php endif; ?>
    </h3>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'acadYear',
                'value' => function ($model) {
                    return $model->semregRel->semregAcadYearRel->sch_year_desc . '-' . $model->semregRel->semregSemesterRel->semester_name;
                },
                'label' => Yii::t('rms', 'Academic Year'),
                // 'filter' => ArrayHelper::map(SchoolYear::find()->orderBy(['sch_year_desc' => SORT_DESC])->all(), 'id', 'sch_year_desc'),
            ],
            [
                'attribute' => 'std_regId',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->semregRel->semregStdRel->std_regId, ['/student/view', 'id' => $model->semregRel->semregStdRel->id], ['target' => '_blank']);
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
            ],
            [
                'attribute' => 'recipient_email:email',
                'format' => 'raw',
                'value' => function ($model) {
                    $email = $model->recipient_email;
                    $refreshbutn = '';
                    if ($model->mail_sent == 1) {
                        $email = "<span class='badge badge-success alert-success'>" . $model->recipient_email . "</span>";
                    } else {
                        $refreshbutn = "<button class='btn btn-xs btn-warning float-right' onclick='refreshEmail({$model->id});return false;'><i class='fa fa-refresh'></i> Refresh</button>";
                        $email = "<span class='badge badge-danger alert-danger'>" . $model->recipient_email . "</span>";
                    }
                    return $email . $refreshbutn;
                },
                'label' => Yii::t('rms', 'Recipient Email'),
            ],
            'relation',
            [
                'attribute' => 'reg_approved',
                'format' => 'raw',
                'value' => function ($model) {
                    $regapprovalBtn = "";
                    $btn = "";
                    if ($model->reg_approved == 1) {
                        $regapprovalBtn = "<span class='badge badge-success'>Approved</span>";
                        if ($model->mail_sent == 1) {
                            $btn = "<span class='badge badge-success'>Email Sent</span>";
                        } else {
                            $btn = "<button class='btn btn-xs btn-warning float-right' onclick='cancelRegistration({$model->id});return false;'>Cancel</button>";
                        }
                    } elseif ($model->reg_approved == 0) {
                        $regapprovalBtn = "<span class='badge badge-danger'>Not Approved</span>";
                        $btn = "<button class='btn btn-xs btn-primary float-right' onclick='approveRegistration({$model->id});return false;'>Approve</button>";
                    } else {
                        $regapprovalBtn = "<span class='badge badge-warning alert-warning'>Pending</span>";
                    }

                    if (!Yii::$app->user->can('Registrar')) {
                        $regapprovalBtn = "<span class='badge badge-warning alert-warning'>Pending</span>";
                        $btn = "";
                    }

                    return $regapprovalBtn . " " . $btn;
                },
                'label' => Yii::t('rms', 'Registral Approval'),
            ],
            [
                'attribute' => 'mail_sent',
                'format' => 'raw',
                'value' => function ($model) {
                    $msg = '';
                    $sendButton = '';
                    $publishGrade = '';
                    if ($model->mail_sent == 1) {
                        $msg = "<span class='badge badge-success alert-success'>Email Sent</span>";
                        if (($model->grade_report_path == null || !file_exists($model->grade_report_path)) && ($model->reg_approved == 1)) {
                            $sendButton = "<button title='Send published semester grade report' class='btn btn-xs btn-success' onclick='sendMail({$model->id});return false;'>Send Mail</button>";
                        }
                    } else {
                        $publishGrade = "<button title='Generate semester grade report' class='btn btn-xs btn-primary' onclick='generateSemGrade({$model->id});return false;'>Generate Grade Report</button>";
                        $msg = "<span class='badge badge-danger alert-danger'>Email Not Sent</span>";
                        if ($model->reg_approved == 1) {
                            $sendButton = "<button title='Send published semester grade report' class='btn btn-xs btn-success' onclick='sendMail({$model->id});return false;'>Send Mail</button>";
                        }
                        // $publishGrade = "<button title='Generate semester grade report' class='btn btn-xs btn-primary' onclick='generateSemGrade({$model->id});return false;'>Generate Grade Report</button>";
                        // $sendButton = "<button title='Send published semester grade report' class='btn btn-xs btn-success' onclick='sendMail({$model->id});return false;'>Send Mail</button>";
                    }
                    return $msg . " " . $publishGrade . " " . $sendButton;
                },
            ],
            [
                'attribute' => 'date_sent',
                'format' => ['date', 'php:dMY'],
                'label' => Yii::t('rms', 'Date Sent'),
            ],
            [
                'attribute' => 'grade_report_path',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->grade_report_path && file_exists($model->grade_report_path)) {
                        return "Report Generated";
                    } else {
                        $model->grade_report_path = null;
                        $model->save(false, ['grade_report_path']);
                        return "<span class='badge badge-warning'>Not Generated</span>";
                    }
                },
            ],
            'trail_count',
        ],
    ]) ?>

</div>

<script>
    const refreshEmail = (spg_id) => {
        $.post('/sent-publishd-grades/refresh-email', {
            id: spg_id
        }).done((response) => {
            location.reload();
        })
    }
    const generateSemGrade = (spg_id) => {
        myWindow = window.open(
            '/sent-publishd-grades/generate-semester-grade-report?id=' + spg_id,
            'title=Student Semester Grade Report',
            'height=600,width=1000');
        // $.ajax({
        //     url: "<?= Yii::$app->urlManager->createUrl(['/sent-publishd-grades/generate-semester-grade-report']) ?>",
        //     type: "POST",
        //     data: {
        //         id: spg_id,
        //         _csrf: yii.getCsrfToken()
        //     },
        //     success: function(response) {
        //         $('#modal').find('.modal-header').html('Import Published Grades');
        //         $('#modalContent').css('text-align', 'left');
        //         $('#modalContent').html('Loading data...');
        //         $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        //         $('#modal').modal('show');
        //         $('#modalContent').html(response);
        //     },
        //     error: function(xhr, status, error) {
        //         // console.error(error);
        //         // alert("An error occurred while processing your request.");
        //     }
        // });
    }
    const sendMail = (spg_id) => {
        $.post('/sent-publishd-grades/send-email', {
            id: spg_id
        }).done((response) => {
            location.reload();
        })
    }
    const cancelRegistration = (id) => {
        if (confirm("Are you sure you want to cancel approval of this item?")) {
            $.ajax({
                url: "<?= Yii::$app->urlManager->createUrl(['/sent-publishd-grades/approve-registration-toggle']) ?>",
                type: "POST",
                data: {
                    id: id,
                    _csrf: yii.getCsrfToken()
                },
                success: function(response) {
                    if (response.success) {
                        // alert(response.message);
                        location.reload();
                    } else {
                        // alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    // console.error(error);
                    // alert("An error occurred while processing your request.");
                }
            });
        }
    };
    const approveRegistration = (id) => {
        if (confirm("Are you sure you want to approve this registration?")) {
            $.ajax({
                url: "<?= Yii::$app->urlManager->createUrl(['/sent-publishd-grades/approve-registration-toggle']) ?>",
                type: "POST",
                data: {
                    id: id,
                    _csrf: yii.getCsrfToken()
                },
                success: function(response) {
                    if (response.success) {
                        // alert(response.message);
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    // console.error(error);
                    // alert("An error occurred while processing your request.");
                }
            });
        }
    };
</script>