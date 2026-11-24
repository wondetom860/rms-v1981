<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseEvaluationControlSetting */

$this->title = $model->deptPlanRel->criteriaBundleRel->code.": ({$model->candidateStaffRel->getFullName()})";
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'AcStEva'), 'url' => ['/AcStEva/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Evaluation Control Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ase-evaluation-control-setting-view">

    <h3><?= Html::encode($this->title) ?></h3>

    <p class="pull-right">
        <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs']) ?>
        <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-xs',
            'data' => [
                'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            
            [
                'attribute' => 'dept_plan_relId',
                'value' => function($model){
                    return $model->deptPlanRel->criteriaBundleRel->code;
                }
            ],
            [
                'attribute' => 'candidate_staff_relId',
                'value' => function($model){
                    return $model->candidateStaffRel->getFullName();
                }
            ],
            [
                'attribute' => 'evaluators',
                'format' => 'raw',
                'value' => function($model){
                    $done = $model->getDoneCount();
                    $count = count(json_decode($model->evaluators));
                    return "<a href='#' onclick='displayEvaluators($model->id);return false;'>{$count}</a> Evaluatros. ({$done} done.)";
                }
            ],
            'evaluated_as',
            'total_percentage',
            'finalized',
            [
                'attribute' => 'created_by',
                'value' => function($model){
                    return $model->createdBy->username;
                }
            ],[
                'attribute' => 'created_at',
                'value' => function($model){
                    return date("d/m/Y",$model->created_at);
                }
            ],
            
            // 'updated_at',
            // 'updated_by',
            // 'data:ntext',
        ],
    ]) ?>

</div>

<script type="text/javascript">
    const displayEvaluators = (controlId) => {
        // __candidate_staff_id = '';
        // __evaluators = [];
        // __bundle_id = bundleId;
        $('#modal').find('.modal-header').html('Evaluation Criteria Deatils');
        $('#modalContent').css({'text-align':'left','margin-top':'0px'});
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/AcStEva/ase-evaluation-control-setting/show-evaluators',{id:controlId}).done(function(data){
            $('#modalContent').html(data);
        });
    }
</script>
<?php

 Modal::begin([
    'header'=>'<h4>List of Evaluators Evaluators</h4>',
    'id'=>'modal',
    'size'=>'modal-lg',
 ]);
 echo "<div id='modalContent'>Loading form...</div>";
 Modal::end();

 ?>