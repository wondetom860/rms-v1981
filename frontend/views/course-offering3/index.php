<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use \common\controllers\CommonControllerFunctions as CCF;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseOffering2Detail */
/* @var $form yii\widgets\ActiveForm */

$this->title = "Course Offering V.2";
// $this->title = Yii::t('rms', 'Course Offering and Enrollment Detail');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-offering2-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="conatiner-fluid" style="font-size: 10pt;">
    	<div class="row">
    		<div class="panel panel-primary">
    			<div class="panel-heading">
    				New course Offering form
    			</div>
    			<div class="panel-body">
    				<div class="panel panel-info">
    					<div class="panel-body">
    						<div class="col-md-3">
				    			<?= $form->field($model, 'coff2_acadYear_relId')->dropdownList(
				    				ArrayHelper::map(\backend\models\SchoolYear::find()->all(),'id','sch_year_desc'),
				    				[
				    					'prompt'=>'Select Academic Year.'
				    				]) ?>
				    		</div>

				    		<?php  //['id'=>CCF::currentTime()['S']] ?>
				    		<div class="col-md-3">
				    			<?= $form->field($model, 'coff2_sem_relId')->dropdownList(
				    				ArrayHelper::map(\backend\models\Semester::find()->all(),'id','semester_name'),
				    				[
				    					'prompt'=>'Select Semester.'
				    				]) ?>
				    		</div>
				    		<div class="col-md-3">
		    					<?= $form->field($model, 'admission')->dropdownList(
		                            ArrayHelper::map(\backend\models\Admission::find()->where(['<>','admission_name','Extension'])->all(), 'id', 'admission_name'),
		                            [
		                                'prompt'=>'Select Admission',
		                                
		                            ]
		                        ) ?>
				    		</div>

				    		<div class="col-md-3">
				    			<?= $form->field($model, 'program')->dropdownList(
		                            ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'),
		                            [
		                                'prompt'=>'Select Program',
		                                'onChange'=>' $.post("/program/clslvllists",
                                                {
                                                    progId:$(this).val()
                                                }, 
                                                function( data ) {
                                                //console.log("Returned: "+data);
                                              $( "#courseoffering2-clslvl" ).html( data );
                                              $("#courseoffering2-department").trigger("change");
                                            });
                                            // coffLoad();',
		                            ]
		                        ) ?>
				    		</div>
    					</div>
    				</div>

		    		<div class="col-md-3">
		    			<?= $form->field($model, 'department')->dropdownList(
                            $model->HoDDept,
                            [
                                'prompt'=>'Select Department',
                                'onChange'=>' 
	                                $.post( "'.Yii::$app->urlManager->createUrl('specialization/listspec-cp').'",
	                                    {
	                                        deptId:$(this).val(),
	                                        progId:$("#courseoffering2-program").val(),
	                                        admId:$("#courseoffering2-admission").val()
	                                    }, 
	                                    function( data ) {
	                                    //console.log("Returned: "+data);
		                                  $( "#courseoffering2-spec").html( data );
		                                  // Load group here
		                                });
	                                // coffLoad();
	                            ',
                            ]
                        ) ?>
		    		</div>

		    		<div class="col-md-3">
		    			<?= $form->field($model, 'clslvl')->dropdownList(
                            ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'),
                            [
                                'prompt'=>'Select Class Level',
                                'onChange'=>'loadCocat()',
                            ]
                        )->label('Class Level') ?>
		    		</div>

		    		<div class="col-md-3">
		    			<?= $form->field($model, 'spec')->dropdownList(
                            ArrayHelper::map(\backend\models\Specialization::find()->all(), 'id', 'spec_name'),
                            [
                                'prompt'=>'Select Specialization',
                                'onChange'=>'loadCocat()',
                                
                                
                            ]
                        )->label('Specialization') ?>
		    		</div>
		    		<div class="conatiner-fluid">
    					<button class="btn btn-primary btn-sm" id="loadCoursesbtn">Load Courses</button>
    					
    				</div>
    			</div>
    			<div class="panel-footer">
    				<div class="conatiner-fluid">
    					<div class="row">
    						<div class="col-md-5">
    							<div class="conatiner-fluid"   id="cat_history">
    								
    							</div>
    						</div>
    						<div class="col-md-1">
    							<button class="btn btn-primary btn-sm btn-block" onclick="offerCourses();return false;">
    								Offer
    							</button>
    							<p style="font-size: 8pt;">
    								Courses <span id="courseCount" style="font-size: 7pt; font-weight: bold; font-style: italic;">0</span> <br> Total Crdt : <span id="crdtSum" style="font-size: 7pt; font-weight: bold; font-style: italic;">0</span>
    							</p>
    						</div>
    						<div class="col-md-6">
    							<div class="conatiner-fluid"   id="coff_history">
    								
    							</div>
    					</div>
    					</div>
    				</div>
    			</div>
    		</div>
    	</div>
    </div>
    	
</div>

<?php

    $this->registerJs(
    	"
    	courseList = [];
    	catList = [];
    	totalCrdtHr = 0;
    	window.deleteOffering = function(id){
    		if(confirm('You want to remove this offering?')){
    			$.post('/course-offering3/delete-offering',{id:id}).done(function(data){
    				if(data == 1){
	    				loadCocat();
					}else{
						$('#coff_history').html(data);
					}
    			});
    		}
    	}
    	window.offerCourses = function(){
    		if(catList.length){
    			$('#coff_history').html('Offering courses....');

	    		$.post('/course-offering3/offer-courses',
	    		{
	    			courseList:JSON.stringify(courseList),
	    			catList:JSON.stringify(catList),
	    			totalCrdtHr:totalCrdtHr,
	    			admId : $('#courseoffering2-admission').val(),
		        	progId : $('#courseoffering2-program').val(),
		        	deptId : $('#courseoffering2-department').val(),
		        	clslvlId : $('#courseoffering2-clslvl').val(),
		        	acadYearId : $('#courseoffering2-coff2_acadyear_relid').val(),
	        		semId : $('#courseoffering2-coff2_sem_relid').val(),
	        		specId : $('#courseoffering2-spec').val()
	    		}
	    		).done(function(data){
	    			if(data == 1){
	    				loadCocat();
					}else{
						$('#coff_history').html(data);
					}
					
	    		});
    		}else{
    			$('#coff_history').html('<span style=\"background-color:red;\">Course Not Selected.</span>');
    		}
    	}

    	window.addToList = function(catId,courseId,crdtHr){
    		admId = $('#courseoffering2-admission').val();
	        progId = $('#courseoffering2-program').val();
	        deptId = $('#courseoffering2-department').val();
	        clslvlId = $('#courseoffering2-clslvl').val();
	        specId = $('#courseoffering2-spec').val();

	        groupId = $('#courseoffering2-coff2_group_relid :selected').val();

	        // Add/remove the Courseid from the Courselist
	        if(jQuery.inArray(courseId,courseList) !== -1){ // if item is already selected, remove it because user deselected it.
	            courseList.splice($.inArray(courseId,courseList),1);
	            totalCrdtHr -= crdtHr;
	        }else{//going to add the item to the list.
	            courseList.push(courseId);
	            totalCrdtHr += crdtHr;
	        }

	        // Add/remove the Courseid from the Cofflist
	        if(jQuery.inArray(catId,catList) !== -1){ // if item is already selected, remove it because user deselected it.
	            catList.splice($.inArray(catId,catList),1);
	        }else{//going to add the item to the list.
	            catList.push(catId);
	        }

	        $('#courseCount').text(courseList.length);
	        $('#crdtSum').text(totalCrdtHr);

	        console.log(courseList);
    	}

    	window.loadCocat = function(){
    		courseList = [];
	    	catList = [];
	    	totalCrdtHr = 0;
    		$('#cat_history').html('Loading....');
	        admId = $('#courseoffering2-admission').val();
	        progId = $('#courseoffering2-program').val();
	        acadYearId = $('#courseoffering2-coff2_acadyear_relid').val();
	        semId = $('#courseoffering2-coff2_sem_relid').val();
	        deptId = $('#courseoffering2-department').val();
	        clslvlId = $('#courseoffering2-clslvl').val();
	        specId = $('#courseoffering2-spec').val();

	        // console.log(admId,progId,deptId,clslvlId,specId,semId);

	        $.post(
	            '/course-offering3/get-catalog-courses',
	            {
	                admId:admId,
	                progId:progId,
	                deptId:deptId,
	                clslvlId:clslvlId,
	                specId:specId == 0?null:specId,
	                semId:semId,
	                acadYearId:acadYearId,
	                ext:1
	            },
	            function(data){
	                //console.log(data);
	                $('#cat_history').html(data);
	                $.post('/course-offering3/get-offered-courses',
		    		{
		    			admId:admId,
		                progId:progId,
		                deptId:deptId,
		                clslvlId:clslvlId,
		                specId:specId == 0?null:specId,
		                semId:semId,
		                acadYearId:acadYearId
		    		}
		    		).done(function(data){
		    			$('#coff_history').html(data);
		    		});
	            }
	        );
    	}
    	$('#loadCoursesbtn').click(function(e){
    		e.preventDefault();
    		loadCocat();
    	});

    	",
    	$this::POS_READY
    );

    ?>