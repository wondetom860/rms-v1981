<?php

use backend\models\Student;
use yii\helpers\Html;

$this->title = Yii::t('rms', 'Department Courses');
$this->params['breadcrumbs'][] = $this->title;
$std = Student::find()->where(['std_user_relId' => Yii::$app->user->id])->one();
?><br>
<div class="department-course-form">
	<h3><?= Html::encode($this->title) ?></h3>
	<div class="container-fluid">

		<div class="panel panel-info">
			<div class="panel-heading" style="text-align: center;">
				<h4>Department Courses: <?= $std->stdDeptRel->dept_name . "<strong>({$std->stdAdmissionRel->admission_name} {$std->stdProgRel->program_name})</strong>" ?></h4>
			</div>
			<div class="panel-body">
				<div class="col-md-12 table-responsive">
					<?= $this->render('dept_course_list', ['stdModel' => $std]) ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- departmentCoursesContainer -->
<!-- selectionFormContainer -->



<?=
$this->registerJs(
	"
    // window.loaadCourses = function(){

    //   admId = $('#admiddd').val();
    //   progId = $('#progIdd').val();
    //   deptId = $('#deptidddd').val(); 
    //   clslvlId = $('#clslvlId').val();
    //   specId = $('#specdddd').val(); 
    //   semId  =$('#semddd').val();

    //   $.post('/department-courses/get-courses',{

    //     admId:admId,
    //     progId:progId,
    //     deptId:deptId,
    //     clslvlId:clslvlId,
    //     specId:specId,
    //     semId:semId
        
    //     }).done(function(data){
    //       $('#departmentCoursesContainer').html(data);
    //     });

    // }

    // window.loadClslvl = function(progId){
    //   $.post('/program/clslvllists',{progId:progId}).done(function(data){
    //     console.log(data);
    //     $('#clslvlId').html(data);
    //     });
    // }

    // window.loadSpec = function(deptId,progId){
    //   // console.log(progId);
    //   admId = $('#admiddd').val();
    //   $.post('/specialization/listspec-cp',{

    //     deptId:deptId,
    //     progId:progId,
    //     admId:admId,

    //     }).done(function(data){
    //     //console.log(data);
    //     $('#specdddd').html(data);
    //     });
    // }

    // window.loadForm = function(){

    //   $('#selectionFormContainer').html('Loading selection form for you.');
    //   $.post('/department-courses/get-selection-form',
    //   {}).done(function(data){
    //       $('#selectionFormContainer').html(data);
    //     });

    //   // studentsemesterstatushistory-admission

    // }

    // loadForm();
    // // console.log($('#progIdd').val());
    // // loadClslvl($('#progIdd').val());
    ",
	$this::POS_READY
);
?>