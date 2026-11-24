<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */

$this->title = "Course Add";
?>

<!-- <h2>course Add/index</h2> -->

<?php $form = ActiveForm::begin(); 
	
	if (Yii::$app->user->can('Student')) {

		?>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-6">
					<div class="panel panel-primary">
						<div class="panel-heading">
							New Course add request Form.
						</div>
						<div class="panel-body">
							<div class="container-fluid" id="courseAddErrorContainer">
								
							</div>
							<div class="conatiner-fluid" style="display: none;" id="courseAddFormContainer">
								<div class="col-md-12">
									<div class="panel panel-info">
										<div class="panel-heading">
											Select the course you want to add:
										</div>
										<div class="panel-body">
											<span id="courseCount" style="color:red; font-style: italic;"></span>

											 <div class="container-fluid" id="addWithGroup">
											 	<?= $form->field($model, 'ah_course_relId')->dropdownList([],
				                                [
				                                    'prompt'=>'Select Course',
				                                    'onChange'=>'aLoadDepts($(this).val())',
				                          
				                                ]
					                            )->label('Select Course.')
					                            ?>
					                            <?= $form->field($model, 'addWDept')->dropdownList(ArrayHelper::map(\backend\models\Department::find()->all(), 'id','dept_name'),
					                                [
					                                    'prompt'=>'Select Department',
					                                    'onChange'=>'aLoadClslvl()',
					                          
					                                ]
					                            )->label('Select Department')?>

					                            <?= $form->field($model, 'addWClslvl')->dropdownList(ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id','clslvl_name'),
					                                [
					                                    'prompt'=>'Select Class Year',
					                                    'onChange'=>'aLoadGroup()',
					                          
					                                ]
					                            )->label('Select Class Year')?>

					                            <?php echo Html::activeHiddenInput($model, "ah_coff_relId",['id'=>'ah_coff_relId_ID']);?>

												<?= $form->field($model, 'ah_addWithGroup_relId')->dropdownList([],
					                                [
					                                    'prompt'=>'Select group',
					                                    'onChange'=>'djgfjkghjgshdj($(this).val())',
					                          
					                                ]
					                            )->label('Select group.') ?>
											</div>
				                            <br>
										</div>
									<div class="panel-footer">
										<button class="btn btn-success btn-sm" id="saveAddBtn">Proccess</button><br>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel panel-primary">
					<div class="panel-heading">
						Course add requests made in this semester.
					</div>
					<div class="panel-body">
						<div class="container-fluid" id="addRequistContainer">
							
						</div>
					</div>
					<div class="panel-footer">
						
					</div>
				</div>
			</div>
				
			</div>
		</div>

		<?php
		
	}else{
		echo "<p class='bg-danger'>You have nothing to do here.</p>";die();
	}

?>

<?php
$this->registerJs(
	"
	$('#saveAddBtn').click(function(e){
		e.preventDefault();
		addCourse1 = $('#addhistory-ah_course_relid').val();
		addWithDept = $('#addhistory-addwdept').val();
		addWithGroup = $('#addhistory-ah_addwithgroup_relid').val();
		addWithClslvl = $('#addhistory-addwclslvl').val();
		addWithCoff = $('#ah_coff_relId_ID').val();

		// console.log(addCourse1,addWithDept,addWithClslvl, addWithGroup);

		// Write to database table.
		if(confirm('Are you sure to add this course?')){
			if(!isEmpty(addCourse1) && !isEmpty(addWithGroup) && !isEmpty(addWithClslvl)  && !isEmpty(addWithCoff)){
				$.post(
					'/course-add/create',
					{
						addCourse1:addCourse1,
						addWithGroup:addWithGroup,
						addWithClslvl:addWithClslvl,
						addWithCoff:addWithCoff

					},
					function(data){
						// if(data == 1){
						// 	loadCoursesToAdd();
						// }else{
						// 	$('#addRequistContainer').html(data);
						// }
						loadCoursesToAdd();
				});

				}else{
					alert('Invalid selection, please correct it.');
				}
		}

		});

	// in case if the student want to cancel the request:
	window.deleteAddRequest = function(id){
		console.log(id);
		if(confirm('You want to cancel this add request?')){
			$.post('/course-add/delete-add-request',{id:id}).done(function(data){
				if(data == 1){
					loadCoursesToAdd();
				}else{
					$('#addRequistContainer').html(data);
				}
			});
		}
	}
	window.isEmpty = function(val){
        return val == 'undefined'||val == ''||val == ' ';
    }

    window.djgfjkghjgshdj = function(){
		$('#ah_coff_relId_ID').val($('#addhistory-ah_addwithgroup_relid :selected').data('coff'));
	}

	
	window.aLoadGroup = function(){
		deptId = $('#addhistory-addwdept').val();
		clslvlId = $('#addhistory-addwclslvl').val();
		courseId = $('#addhistory-ah_course_relid').val()

		$.post(
			'/course-add/getgroups',
			{
				deptId:deptId,
				clslvlId:clslvlId,
				courseId:courseId
				},
			function(data){
				$('#addhistory-ah_addwithgroup_relid').html(data);
			}
		);

	}
	// window.addCourse = function(courseId, courseTitle){
	// 	opt= '<option value=\''+ courseId+'\'>'+courseTitle+'</option>';
	// 	$('#addhistory-ah_course_relid').html(opt);
	// 	aLoadDepts(courseId);
	// 	//console.log(courseId,courseTitle);
	// }
	window.aLoadDepts = function(courseId){
		// Auto load depts that offer the course in this semester.
		$.post(
			'/course-add/getdepts',
			{courseId:courseId},
			function(data){
				// console.log(data);
				$('#addhistory-addwdept').html(data);

				if($('#addhistory-addwdept').val() !== 'undefined'){
					// console.log('Dept Id: '+$('#addhistory-addwdept').val());
					// console.log('Course Id: '+$('#addhistory-ah_course_relid').val());
					aLoadClslvl();
				}
			}
		);
	}
	window.aLoadClslvl = function(){
		// Load clslvl and group which takes this course for selected department.
		$.post(
			'/course-add/getclslvl',
			{
				deptId:$('#addhistory-addwdept').val(),
				courseId:$('#addhistory-ah_course_relid').val()
			},
			function(data){
				// console.log('Returned data: '+data);
				dataP = JSON.parse(data);
				$('#addhistory-ah_addwithgroup_relid').html(dataP['group']);
				$('#addhistory-addwclslvl').html(dataP['clslvl']);
			}
		);
	}

	window.loadAddRequests = function(){

	}
	window.loadCoursesToAdd = function(){
		//console.log('Loading courses ofered in this semester by selected Dept.');
		$.post(
			'/course-add/get-course-for-adding',
			{
			},
			function(data){
				//console.log(data);
				if(isJson(data)){
					dataPp = JSON.parse(data);
					$('#addRequistContainer').html(dataPp['table']);
					$('#courseAddFormContainer').show();
					// 
					$('#addhistory-ah_course_relid').html(dataPp['courseDrop']);
					if($('#addhistory-ah_course_relid option').length <= 1){
						$('#courseAddFormContainer').show();
						$('#courseAddFormContainer').html('No course available, please check later.');
						$('#courseCount').text('No course available, please check later.');
						$('#addWithFromContainer').hide();
						$('#addhistory-ah_course_relid').parent().hide();
						}
						jQuery('<p/>', {
							    class: 'bg-info',
							    title: 'Available course to add!'
							}).html(dataPp['message2']).appendTo('#addRequistContainer');
				}else{
					$('#courseAddErrorContainer').html(data);
				}
			});
	}

	window.isJson = function(str) {
	    try {
	        JSON.parse(str);
	    } catch (e) {
	        return false;
	    }
	    return true;
	}

	loadCoursesToAdd();
	loadAddRequests();
	",
	$this::POS_READY
);
?>