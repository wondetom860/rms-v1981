<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;
// use frontend\modules\AcStEva\models\AseSemesterEvaluation;

/** @var yii\web\View  $this
 * @var $model frontend\modules\AcStEva\models\AseSemesterEvaluation 
 */

$this->title = $model->evaluation_header;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'AcStEva'), 'url' => ['/AcStEva/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Semester Evaluations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ase-semester-evaluation-view">

    <h3><?= Html::encode($this->title) ?></h3>
    <p class="pull-right">
        <?=
        Yii::$app->user->can('DepartmentHead') && $model->status != $model::STATUS_CLOSED ?
            Html::a(Yii::t('rms', 'Schedule Department Staff Evaluation'), ['#'], ['class' => 'btn btn-primary btn-xs', 'onclick' => "scheduleDepartmentStaffEvaluation({$model->id});return false;"]) : '' ?>

        <?php if (Yii::$app->user->can('AcademicDean')): ?>
            <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs']) ?>
            <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-xs',
                'data' => [
                    'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'academic_year_relId',
                'value' => function ($model) {
                    return $model->academicYearRel->getAcHeader();
                }
            ],
            'evaluation_header',
            [
                'attribute' => 'force_above_half_evaluation',
                'format' => 'raw',
                'value' => function ($model) {
                    $hh = $model->force_above_half_evaluation ? '(YES)' : 'NOT';
                    return "Above half evaluation is required for generating result : <b>{$hh}</b>";
                }
            ],
            [
                'attribute' => 'Schedule',
                'format' => 'raw',
                'value' => function ($model) {
                    $status = "<span class='pull-right bg-warning' style='font-size: 10pt; font-style: italic;'>Status: {$model->getStatus()}</span>";
                    return $model->getScheduleInterval() . "{$status}";
                }
            ],
            [
                'attribute' => 'Selected Evaluation Types',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->render('__report/criteria-bundle-in-entry', ['model' => $model]);
                }
            ],
            [
                'attribute' => 'Department Schedules:',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->render('../ase-department-evaluation-schedule/__report/schedule-under-entry', ['model' => $model]);
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return date('d/m/Y', $model->created_at);
                }
            ],
            [
                'attribute' => 'created_by',
                'value' => function ($model) {
                    return $model->createdBy ? $model->createdBy->username : '-';
                }
            ],
            // 'created_by',
            // 'updated_at',
            // 'updated_by',
            // 'data:ntext',
        ],
    ]) ?>

</div>

<?php

Modal::begin([
    'header' => '<h4>Schedule Department Staff Performance Evaluation</h4>',
    'id' => 'modal',
    'size' => 'modal-lg',
]);
echo "<div id='modalContent'>Loading form...</div>";
Modal::end();

?>
<script type="text/javascript">
    const scheduleDepartmentStaffEvaluation = (sevId) => {
        $('#modal').find('.modal-header').html('Schedule Department Staff Performance Evaluation');
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/AcStEva/ase-department-evaluation-schedule/create', {
            sevid: sevId
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
</script>