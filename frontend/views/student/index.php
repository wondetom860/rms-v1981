<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
// use backend\models\User;
// use backend\models\StudentType;
use backend\models\Student;
use yii\bootstrap\Modal;

/** @var yii\web\View $this
 * @var $searchModel backend\models\StudentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Students');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-index table-responsive">

    <h3>
        <?= Html::encode($this->title) ?>
        <button class="btn btn-warning btn-xs pull-right" title="Print Login Credentials of selected students" onclick="printList();return false;">Print Login Credentials</button>
        <!-- <button class="btn btn-info btn-xs pull-right" title="Admit Transfer Students" onclick="admitTransfers();return false;">Admit Transfer Students</button> -->
    </h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?php

    Modal::begin([
        'header' => '<h4>Create new user account</h4>',
        'id' => 'modal',
        'size' => 'modal-lg',
        'options' => [
            'style' => [
                'text-align' => 'left',
            ],
        ],
    ]);
    echo "<div id='modalContent'>Loading user account form...</div>";
    Modal::end();
    ?>

    <div class="container-fluid table-responsive">
        <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'std_regId',
                [
                    'attribute' => 'stdUserRel.first_name',
                    'value' => function ($model) {
                        return $model->stdUserRel ? $model->getFullName() : 'ERROR WITH USER INFO';
                    },
                ],
                [
                    'attribute' => 'stdAcadYearRel.sch_year_desc',
                    'value' => 'stdAcadYearRel.sch_year_desc',
                    'filter' => ArrayHelper::map(\backend\models\SchoolYear::find()->all(), 'sch_year_desc', 'sch_year_desc'),
                ],

                [
                    'attribute' => 'std_sem_relId',
                    'value' => 'stdSemRel.semester_name',
                    'filter' => ArrayHelper::map(Student::find()->groupBy('std_sem_relId')->all(), 'std_sem_relId', function ($model) {
                        return $model->stdSemRel ? $model->stdSemRel->semester_name : '';
                    }),
                ],
                [
                    'attribute' => 'stdAdmissionRel.admission_name',
                    'value' => 'stdAdmissionRel.admission_name',
                    'filter' => ArrayHelper::map(\backend\models\Admission::find()->all(), 'admission_name', 'admission_name'),
                ],
                [
                    'attribute' => 'stdProgRel.program_name',
                    'value' => 'stdProgRel.program_name',
                    'filter' => ArrayHelper::map(\backend\models\Program::find()->all(), 'program_name', 'program_name'),
                ],
                [
                    'attribute' => 'stdDeptRel.dept_name',
                    'value' => 'stdDeptRel.dept_code',
                    'filter' => ArrayHelper::map(\backend\models\Department::find()->all(), 'dept_name', 'dept_name'),
                ],
                [
                    'attribute' => 'stdClslvlRel.clslvl_name',
                    'value' => 'stdClslvlRel.clslvl_name',
                ],
                [
                    'attribute' => 'std_spec_relId',
                    'value' => 'stdSpecRel.spec_name',
                    'filter' => ArrayHelper::map(Student::find()->groupBy('std_spec_relId')->all(), 'std_spec_relId', function ($model) {
                        return $model->std_spec_relId ? $model->stdSpecRel->spec_name : null;
                    }),
                ],
                [
                    'attribute' => 'Grp',
                    'value' => function ($model) {
                        if (is_null($model->std_group_realId)) {
                            return 'N/A';
                        }
                        $gs = \backend\models\WGroupSetup::findOne(['id' => $model->std_group_realId]);
                        if ($gs) {
                            return \backend\models\WGroup::findOne(['id' => $gs->gs_group_relId])->group_name;
                        }
                        return 'N/A';
                    },
                ],

                [
                    'attribute' => 'std_semester_status',
                    'value' => function ($model) {
                        return $model->std_semester_status;
                    },
                    'filter' => ArrayHelper::map(Student::find()->groupBy('std_semester_status')->all(), 'std_semester_status', 'std_semester_status'),
                ],
                [
                    'attribute' => 'std_type',
                    'value' => function ($model) {
                        // $stdt = StudentType::findOne(['id'=>$model->std_type]);
                        return strtoupper($model->std_type);
                    },
                    'filter' => ArrayHelper::map(Student::find()->groupBy('std_type')->all(), 'std_type', 'std_type'),
                ],
                // 'std_created_at',
                // 'std_created_by',
                // 'std_updated_at',
                // 'std_updated_by',

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>


<?php

$this->registerJs(
    "
    window.admitTransfers = () => {
    
    }
    window.printList = ()=>{
        myWindow = window.open(
          '/student/print-login-creden', 
          'title:Student List', 
          'height=600,width=1400');

        myWindow.print();
        return false;
    }
    $(function(){
        $('#modalBotton').click(function(){
            $('#modal').modal('show').css('text-align','left')
            .find('#modalContent')
            .load($(this).attr('value'));
        });
    });
    ",
    $this::POS_READY
);

?>