<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\WGroupSetupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Group Setups');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wgroup-setup-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'gs_acadYear_relId',
                'value'=>'gsAcadYearRel.sch_year_desc',
                'filter' => ArrayHelper::map(\backend\models\WGroupSetup::find()->groupBy('gs_acadYear_relId')->all(),'gs_acadYear_relId',function($model){ return $model->gsAcadYearRel->sch_year_desc ;})
            ],
            [
                'attribute'=>'gs_sem_relId',
                'value'=>'gsSemRel.semester_name',
                'filter' => ArrayHelper::map(\backend\models\WGroupSetup::find()->groupBy('gs_sem_relId')->all(),'gs_sem_relId',function($model){ return $model->gsSemRel->semester_name; })
            ],

            [
                'attribute'=>'gs_group_relId',
                'value'=>'gsGroupRel.group_name',
                'filter' => ArrayHelper::map(\backend\models\WGroupSetup::find()->groupBy('gs_group_relId')->all(),'gs_group_relId',function($model){ return $model->gsGroupRel->group_name; })
            ],
            [
                'attribute'=>'gs_adm_relId',
                'value'=>'gsAdmRel.admission_name',
                'filter' => ArrayHelper::map(\backend\models\WGroupSetup::find()->groupBy('gs_adm_relId')->all(),'gs_adm_relId',function($model){ return $model->gsAdmRel->admission_name; })
            ],
             [
                'attribute'=>'gs_prog_relId',
                'value'=>'gsProgRel.program_name',
                'filter' => ArrayHelper::map(\backend\models\WGroupSetup::find()->groupBy('gs_prog_relId')->all(),'gs_prog_relId',function($model){ return $model->gsProgRel->program_name ;})
            ],

            [
                'attribute'=>'gs_dept_relId',
                'value'=>'gsDeptRel.dept_name',
                'filter' => ArrayHelper::map(\backend\models\WGroupSetup::find()->groupBy('gs_dept_relId')->all(),'gs_dept_relId',function($model){ return $model->gsDeptRel->dept_name ;})
            ],
            [
                'attribute'=>'gs_clslvl_relId',
                'format'=>'raw',
                'value' => function($model){
                    return $model->gs_clslvl_relId ? $model->gsClslvlRel->getClslvlDisp()." - {$model->gsClslvlRel->clslvlProgramRel->program_name}" : NULL;
                },
                'filter' => ArrayHelper::map(\backend\models\WGroupSetup::find()->groupBy('gs_clslvl_relId')->all(),'gs_clslvl_relId',function($model){ return $model->gs_clslvl_relId ? $model->gsClslvlRel->clslvl_num." - {$model->gsClslvlRel->clslvlProgramRel->program_name}" : NULL;})
            ],

            // 
            // 'gs_clslvl_relId',
            [
                'attribute'=>'gs_spec_relId',
                'value'=>'gsSpecRel.spec_name',
                'filter' => ArrayHelper::map(\backend\models\WGroupSetup::find()->groupBy('gs_spec_relId')->all(),'gs_spec_relId',function($model){ return $model->gs_spec_relId ? $model->gsSpecRel->spec_name : NULL ;})
            ],
            [
                'attribute'=>'Option',
                'format'=>'raw',
                'value'=>function($model){
                    return Html::a(Yii::t('rms', 'Show'), [''], 
                        [
                        'class' => 'btn btn-success btn-xs',
                        'data-pjax'=>0,
                        'title'=>'Show studnets in this group',
                        'value'=>$model->id,
                        'onclick'=>'showStudentsList('.$model->id.');return false;',
                    ]);
                }
            ],

            // 
            // 'gs_created_by',
            // 'gs_updated_by',
            // 'gs_created_at',
            // 'gs_updated_at',
            // 'gs_status',
            // 'gs_size',
            // 'remark:ntext',

            // ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <script type="text/javascript">
        window.showPointHistory = (stdId) =>{
            $("#point_history_container").focus();
            $("#point_history_container").html('Loading semester registration & point history....');

            $('.btn__stds').removeClass('btn-info');
            $('#btn__std'+stdId).addClass('btn-info');

            $.post('/student/get-registration-history',{stdId:stdId}).done((resp) =>{
                $("#point_history_container").html(resp);
            });
        }
        window.showStudentsList = (gsetId) => {

            $('#modal').find('.modal-header').html('Student for your department');
            $('#modalContent').css('text-align','left');
            $('#modalContent').html('Loading data...');
            $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
            $('#modal').modal('show');
            $.get('/w-group-setup/get-assignments',{wt_gsetUp_entry:gsetId}).done(function(data){
                $('#modalContent').html(data);
            });
            // return false;

            // window.open('/w-group-setup/get-assignments?wt_gsetUp_entry='+gsetId,"height=600,width=800","Student List");
        }
    </script>
<?php Pjax::end(); ?></div>

<?php
    // $y = \Yii::$app->user->isGuest;
    Modal::begin([
        'header'=>'<h4></h4>',
        'id'=>'modal',
        'size'=>'modal-sm',
    ]);
    echo "<div id='modalContent'>Loading form...</div>";
    Modal::end();
?>
