<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\GraduateStudentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Graduate Students');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="graduate-student-index table-responsive" style="font-size: 10pt;">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php if (\Yii::$app->user->can('Admin')) {
        # code...
    ?>
    <p>

        <?= Html::a(Yii::t('rms', 'Add New Graduate Student Record'), ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a(Yii::t('rms', 'Import From RMS-DEC'), ['import'], ['class' => 'btn btn-success']) ?>
        <button class="btn btn-warning btn-md pull-right" onclick="showPointHistory();return false;">Print Point History</button>
    </p>
<?php }elseif (\Yii::$app->user->can('Registrar')) {
    echo '<p><button class="btn btn-warning btn-md pull-right" onclick="showPointHistory();return false;">Print Point History</button></p>';
} ?>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=> 'gstd_user_relId',
                'value'=>function($model){
                    return $model->gstdUserRel->getFullName();
                }
            ],
            [
                'attribute'=>'gstd_adm_relId',
                'value'=>'gstdAdmRel.admission_name',
                'filter' => \yii\helpers\ArrayHelper::map(\backend\models\GraduateStudent::find()->innerJoin('admission','admission.id = graduate_student.gstd_adm_relId')->groupBy('gstd_adm_relId')->orderBy('admission.admission_name')->all(),'gstd_adm_relId',function($modeld){
                    return $modeld->gstdAdmRel->admission_name;
                }),
            ],
            [
                'attribute'=>'gstd_prog_relId',
                'value'=>'gstdProgRel.program_name',
                'filter' => \yii\helpers\ArrayHelper::map(\backend\models\GraduateStudent::find()->innerJoin('program','program.id = graduate_student.gstd_prog_relId')->groupBy('gstd_prog_relId')->orderBy('program.program_name')->all(),'gstd_prog_relId',function($modeld){
                    return $modeld->gstdProgRel->program_name;
                }),
            ],
            [
                'attribute'=>'gstd_dept_relId',
                'value'=>'gstdDeptRel.dept_name',
                'filter' => \yii\helpers\ArrayHelper::map(\backend\models\GraduateStudent::find()->innerJoin('department','department.id = graduate_student.gstd_dept_relId')->groupBy('gstd_dept_relId')->orderBy('department.dept_name')->all(),'gstd_dept_relId',function($modeld){
                    return $modeld->gstdDeptRel->dept_name;
                }),
            ],
            [
                'attribute'=>'gstd_spec_relId',
                'value'=>function($model){
                    return $model->gstd_spec_relId ? $model->gstdSpecRel->spec_name : '-';
                },
            ],
            // [
            //     'attribute'=>'gstd_sponsorship_relId',
            //     'value'=>'gstdSponsorshipRel.schType_name',
            // ],
            [
                'attribute'=>'gstd_adm_year',
                'value'=> 'gstd_adm_year',
                'filter' => \yii\helpers\ArrayHelper::map(\backend\models\GraduateStudent::find()->groupBy('gstd_adm_year')->orderBy('gstd_adm_year')->all(),'gstd_adm_year','gstd_adm_year'),
            ],
            [
                'attribute'=>'gstd_grad_year',
                'value'=> 'gstd_grad_year',
                'filter' => \yii\helpers\ArrayHelper::map(\backend\models\GraduateStudent::find()->groupBy('gstd_grad_year')->orderBy('gstd_grad_year')->all(),'gstd_grad_year','gstd_grad_year'),
            ],
            
            'gstd_CGPA',
            [
                'attribute' => 'gstd_doc_collected',
                'value' => function($model){
                    if (is_null($model->gstd_doc_collected) || $model->gstd_doc_collected == 0) {
                        return 'Not Yet';
                    }
                    return 'Yes, On '. date_format(date_create($model->gstd_doc_collected_at),'d M, Y') ;
                }
            ],
            // 'gstd_doc_collected_at',
            // 'gsdt_prepared_by',
            // 'gstd_doc_signed_by',
            // 'gstd_doc_signed_at',
            // 'gstd_doc_signed',

            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => [],
                'header'=>'Actions',
                'template' => '{view} {update} {delete}',
                'visibleButtons'=>[
                    'view'=> function($model, $key, $index){
                          return Yii::$app->user->can('Admin');
                     },
                     'update'=> function($model, $key, $index){
                          return Yii::$app->user->can('Admin') && is_null($model->gstd_approved_by);
                     },
                     'delete'=> function($model, $key, $index){
                          return Yii::$app->user->id == $model->gsdt_prepared_by && is_null($model->gstd_approved_by);
                     },
                ]
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>

<script type="text/javascript">
    gstdList = [];
    window.showPointHistory = ()=>{
        myWindow = window.open(
          '/graduate-student/get-point-history?sfsgfgsfjk=sfjkhsjfdgh&fdfhsgh='+JSON.stringify(gstdList)+'&hfggdjhjhd=fsghd', 
          'title=Graduate Student Point History', 
          'height=600,width=1200');
    }
</script>