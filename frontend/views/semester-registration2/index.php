<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
/** @var yii\web\View $this */
/* @var $searchModel frontend\models\SemesterRegistration2Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Semester Registration');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-registration2-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]);

    if (Yii::$app->user->can('Admin')){
     ?>
    <?php 

    Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'first_name',
                'value' => function($model){
                    return $model->semregStdRel->getFullName();
                }
            ],
            [
                'attribute' => 'semreg_acadYear_relId',
                'value' => function($model){
                    return $model->semregAcadYearRel->sch_year_desc;
                },
                'filter' => ArrayHelper::map(\backend\models\SemesterRegistration2::find()->groupBy('semreg_acadYear_relId')->all(),'semreg_acadYear_relId',function($model){ return $model->semregAcadYearRel->sch_year_desc; }),
            ],
            [
                'attribute' => 'semreg_semester_relId',
                'value' => function($model){
                    return $model->semregSemesterRel->semester_name;
                },
                'filter' => ArrayHelper::map(\backend\models\SemesterRegistration2::find()->groupBy('semreg_semester_relId')->all(),'semreg_semester_relId',function($model){ return $model->semregSemesterRel->semester_name; }),
            ],
            [ 
                'attribute' => 'semreg_clslvl_relId',
                'format' => 'raw',
                'value' => function($model){
                    return $model->semregClslvlRel->getClslvlDisp();
                },
                // 'title' => function($model){
                //     return $model->semregClslvlRel->clslvlProgramrel->program_name;
                // },
                'filter' => ArrayHelper::map(\backend\models\SemesterRegistration2::find()->groupBy('semreg_clslvl_relId')->all(),'semreg_clslvl_relId',function($model){ return $model->semregClslvlRel->clslvl_name."-{$model->semregClslvlRel->clslvlProgramRel->program_name}"; }),
            ],

            [
                'attribute' => 'std_prog_relId',
                'format' => 'raw',
                'value' => function($model){
                    return $model->semregStdRel->stdProgRel->program_name;
                },
                'filter' => ArrayHelper::map(\backend\models\SemesterRegistration2::find()->innerJoin('student','student.id = semester_registration_2.semreg_std_relId')->groupBy('student.std_prog_relId')->all(),function($model){ return $model->semregStdRel->std_prog_relId; },function($model){ return $model->semregStdRel->stdProgRel->program_name; }),
            ],
            [
                'attribute' => 'std_dept_relId',
                'format' => 'raw',
                'value' => function($model){
                    $gset = $model->getStudentGroup();
                    if ($gset && count($gset) > 0) {
                        $gset = $gset->gasGsetRel;
                        return $gset->gsDeptRel->dept_name;
                    }else{
                        NULL;
                    }
                },
                'filter' => ArrayHelper::map(\backend\models\SemesterRegistration2::find()->innerJoin('student','student.id = semester_registration_2.semreg_std_relId')->groupBy('student.std_dept_relId')->all(),function($model){ return $model->semregStdRel->std_dept_relId; },function($model){ return $model->semregStdRel->stdDeptRel->dept_name; }),
            ],

            [
                'attribute' => 'semreg_SGPA',
                'format' => ['decimal',3],
                'value' => function($model){
                    if (Yii::$app->user->can('Student')) {
                        return $model->semreg_gradeCompleted ? ($model->evaluatedAll() ? $model->semreg_SGPA : NULL) : NULL;
                    }else{
                        return $model->semreg_gradeCompleted ? $model->semreg_SGPA : NULL;
                    }
                },
            ],

            [
                'attribute' => 'semreg_CGPA',
                'format' => ['decimal',3],
                'value' => function($model){
                    if (Yii::$app->user->can('Student')) {
                        return $model->semreg_gradeCompleted ? ($model->evaluatedAll() ? $model->semreg_CGPA : NULL) : NULL;
                    }else{
                        return $model->semreg_gradeCompleted ? $model->semreg_CGPA : NULL;
                    }
                },
            ],

            [
                'attribute' => 'semreg_last_ssn_relId',
                'value' => function($model){
                    return $model->semesterLastSsnRel ? $model->semesterLastSsnRel->sssn_name : '-';
                },
                'filter' => ArrayHelper::map(\backend\models\SemesterRegistration2::find()->groupBy('semreg_last_ssn_relId')->all(),'semreg_last_ssn_relId',function($model){ return $model->semesterLastSsnRel ? $model->semesterLastSsnRel->sssn_name : '-'; }),
            ],
            [
                'attribute' => 'semreg_gradeCompleted',
                'format' => 'raw',
                'value' => function($model){
                    return $model->semreg_gradeCompleted ?  "<span class='bg-success'>Ready<span>": NULL;
                },
            ],
        ],
    ]); ?>
    <?php Pjax::end(); 
}else{
    // Call clalender controller here.
    // $enrolActive = \backend\models\ScheduleController::RegistrationOpen();
?>

    <?php

 Modal::begin([
    'header'=>'<h4>Penality registration payment form</h4>',
    'id'=>'modal_payment',
    'size'=>'modal-lg',
 ]);
 echo "<div id='modalContent' class='jumbotron'>Loading payment form...</div>";
 Modal::end();

 ?>


    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Semester Registration
                    </div>
                    <div class="panel-body">
                        <div class="container-fluid" id="semRegContainer">
                            Loading your courses...<br><br><br><br><br>
                        </div>
                        <div class="container-fluid" style="background-color: #ace;">
                            Selected: <span id="courseCount" style="font-style: italic;"></span><br>
                            Total Credit: <span id="crdtSum" style="font-style: italic;"></span>&nbsp;&nbsp;&nbsp;&nbsp;
                            Min. Required Credit: <span id="minCrdt" style="font-style: italic;"></span>
                            Max. Required Credit: <span id="maxCrdt" style="font-style: italic;"></span>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <!--  background-image: url('web/uploads/z268119jkl_button.png'); -->
                        <button class="btn btn-sm btn-success" id="btnEntroll"
                            style="display: none;">
                            Enroll
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Semester Registration History
                    </div>
                    <div class="panel-body">
                        <div class="container-fluid" id="semRegHistoryContainer">
                            Loading your enrolled courses
                        </div>
                    </div>
                    <div class="panel-footer">

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
}

?>
</div>

<!-- semRegContainer -->


<?php
$this->registerJs(
    "
    window.loadPaymentForm = function(){
        //console.log(data);
        $('#modal_payment').modal('show')
            .find('#modalContent').css('textAlign','left');
            getFile();
    }
    window.getFile = function(){
        //data = '';
        $.post('/payment-request/indexx',{}).done(function(data){
            $('#modal_payment').modal('show')
            .find('#modalContent').html(data);
        });
        //return data;
    }
    $('#btnEntroll').click(function(e){
            e.preventDefault();
            courseListString = JSON.stringify(courseList); //JSON encode.
            coffListString = JSON.stringify(coffList); //JSON encode.
            // console.log(courseListString);
            // console.log(coffListString);
            // console.log(typeof(courseListString));

            $.post(
                '/semester-registration2/create',
                {
                    coList:courseListString,
                    coffList:coffListString, 
                    },
                function(data){
                    console.log(data);
                    if(data == 1){
                        loadCourse();
                        loadHistory();
                    }else{
                        // console.log(data);
                        $('#semRegHistoryContainer').html(data);
                        loadCourse();
                    }
                });
            // console.log('enrolling...');
        });

    courseList = [];
    coffList = [];
    totalCrdtHr = 0;
    window.addEnroll = function(courseId,coffId,crdtHr, maxCredit,minCredit){

        // Add/remove the Courseid from the Courselist
        if(jQuery.inArray(courseId,courseList) !== -1){ // if item is already selected, remove it because user deselected it.
            courseList.splice($.inArray(courseId,courseList),1);
            totalCrdtHr -= crdtHr;
        }else{//going to add the item to the list.
            courseList.push(courseId);
            totalCrdtHr += crdtHr;
        }

        // Add/remove the Courseid from the Cofflist
        if(jQuery.inArray(coffId,coffList) !== -1){ // if item is already selected, remove it because user deselected it.
            coffList.splice($.inArray(coffId,coffList),1);
        }else{//going to add the item to the list.
            coffList.push(coffId);
        }

        $('#courseCount').html(courseList.length);
        $('#btnEntroll').show();
        $('#crdtSum').html(totalCrdtHr);
        $('#minCrdt').html(minCredit);
        $('#maxCrdt').html(maxCredit);
    }
    window.loadHistory = function(){
        $.post(
            '/semester-registration2/getenrolledcourses',
            {},
            function(data){
                // Loading data from controller...
                $('#semRegHistoryContainer').html(data);
            });
    }
    window.loadCourse = function(){
        courseList = [];
        coffList = [];
        totalCrdtHr = 0;
        $.post(
        '/semester-registration2/getcourses',
        {},
        function(data){
            // Loading data from controller...
            $('#semRegContainer').html(data);
        });
    }
    loadCourse();
    loadHistory();

    $('#createGroup').on('click',function(e){
        e.preventDefault();
        
    });
    ",
    $this::POS_READY
);
?>