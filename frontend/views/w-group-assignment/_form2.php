<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\GroupAssignment */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin(); ?>

<div class="group-assignment-form">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-3">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Group student in section for this semester
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <?= $form->field($modelg, 'gs_adm_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\Admission::find()->all(), 'id', 'admission_name'),
                                    [
                                        'prompt'=>'Select Admission',
                                        
                                    ]
                                ) ?>

                            </div>
                            <div class="col-md-3">
                                <?= $form->field($modelg, 'gs_prog_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'),
                                    [
                                        'prompt'=>'Select Program',
                                        'onChange'=>' 
                                            $.post( "/program/clslvllists",
                                                {
                                                    progId:$(this).val()
                                                }, 
                                            function( data ) {
                                                //console.log("Returned: "+data);
                                                $( "#wgroupsetup-gs_clslvl_relid" ).html( data );
                                                $("#wgroupsetup-gs_dept_relid").trigger("change");
                                        });
                                       ',
                                    ]
                                ) ?>
                            </div>
                            <div class="col-md-3">
                            <?= $form->field($modelg, 'gs_dept_relId')->dropdownList(
                                ArrayHelper::map(\backend\models\Department::find()->all(), 'id', 'dept_name'),
                                [
                                    'prompt'=>'Select Department',
                                    'onChange'=>' 
                                                $.post( "'.Yii::$app->urlManager->createUrl('specialization/listspec').'",
                                                    {
                                                        progId:$("#wgroupsetup-gs_prog_relid").val(),
                                                        deptId:$(this).val()
                                                    }, 
                                                    function( data ) {
                                                    //console.log("Returned: "+data);
                                                  $( "#wgroupsetup-gs_spec_relid").html( data );
                                                });
                                                // coffLoad();
                                            ',
                                ]
                            ) ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($modelg, 'gs_clslvl_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'),
                                    [
                                        'prompt'=>'Select Class Level',
                                        'onChange'=>'loadGroup()',
                                    ]
                                ) ?>

                            </div>
                            <div class="col-md-3">
                                  
                                <?= $form->field($modelg, 'gs_spec_relId')->dropdownList(
                                        ArrayHelper::map(\backend\models\Specialization::find()->all(), 'id', 'spec_name'),
                                        [
                                            'prompt'=>'Select Specialization',
                                            
                                            
                                        ]
                                 ) ?>
                            </div>
                            <div class="col-md-3">
                                
                            <?= $form->field($modelg, 'grpStatus')->dropdownList([0=>'New Entry Student',1=>'Previously grouped'],
                                    [
                                        'prompt'=>'Select Entry type',
                                        'onChange'=>'EntryStatus($(this).val())'
                              
                                    ]
                                )->label('Group Entry Type') ?>


                            </div>
                            <div class="col-md-3">
                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="container-fluid" id="prevEntry" style="display: none; background-color: #399">
                                <?= $form->field($modelg, 'prevAcadYear')->dropdownList(ArrayHelper::map(\backend\models\SchoolYear::find()->all(), 'id','sch_year_desc'),
                                [
                                    'prompt'=>'Select prev. Acad year.',
                          
                                ]
                            )->label('Prev. Acad. Yr.');
                                // ->where([' <> ','sch_year_desc',Yii::$app->params['C']['Y']])
                             ?>
                            <?= $form->field($modelg, 'prevSem')->dropdownList(ArrayHelper::map(\backend\models\Semester::find()->where([ ' <> ' ,'semester_name',Yii::$app->params['C']['S']])->all(), 'id','semester_name'),
                                [
                                    'prompt'=>'Select Prev. Sem',
                          
                                ]
                            )->label('Prev.Sem.') ?>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                    	<button class="btn btn-secondary btn-sm" id="loadStudents">Load Students</button>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
            	<div class="panel panel-primary">
            		<div class="panel-heading">
            			Student / Group List to be assigned to new group for this semester.
            		</div>
            		<div class="panel-body">
                        <div class="container-fluid">
                            <div class="col-md-6">
                                <?= $form->field($model, 'gas_gset_relId')->dropdownList(ArrayHelper::map(\backend\models\WGroup::find()->all(), 'id','group_name'),
                                [
                                    'prompt'=>'Select group',
                                    'onChange'=>'loadGroupSetUpHistory($(this).val())',
                          
                                ]
                            )?>
                            </div>
                        </div>
            			<div class="container-fluid row" id="stdGrpContainer" style="background-color: #c99; font-size: 9pt;">
            				Wainting for your selection...
            			</div>
            		</div>
            		<div class="panel-footer">
            			<div class="form-group">
					        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Assign') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','id'=>'assignBtn']) ?>
					    </div>
            		</div>
            	</div>
            </div>
            <!-- <div class="col-md-1">
                <div class="container-fluid" style="background-color: #434; height: 100%;">
                    
                </div>
            </div> -->
            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        
                    </div>
                    <div class="panel-body">
                        <div class="container-fluid" id="groupMembersContiner">
                            Waiting for your group selection.
                        </div>
                    </div>
                    <div class="panel-footer">
                        
                    </div>
                </div>
            </div>
		</div>
	</div>

    <?php ActiveForm::end(); ?>

</div>

<!-- groupMembersContiner -->

<?php
$this->registerJs(
    "
    window.deGroup = function(stdId,grpId,grpSetId){
        //e.preventDefault();
        console.log(stdId,grpId,grpSetId);
            $.post(
            '/w-group-assignment/degroup',
            {
                stdId:stdId,
                gid:grpId,
                gsetId:grpSetId
            },
            function(data){
                $('#groupMembersContiner').html(data);
                $('#loadStudents').trigger('click');
                });
        return false;
    }

    window.loadGroupSetUpHistory = function(gid){
        $.post(
            '/w-group-assignment/liststud',
            {
                grpId:gid
            },
            function(data){
                $('#groupMembersContiner').html(data);
            });
    }

    //loadGroupSetUpHistory();
    $('#assignBtn').click(function(e){
        e.preventDefault();
        stdListS = JSON.stringify(stdList);
        gid = $('#wgroupassignment-gas_gset_relid').val();

        $.post(
        '/w-group-assignment/create',
        {
            stdList:stdListS,
            gid:gid
        },
        function(data){
            $('#groupMembersContiner').html(data);
            $('#loadStudents').trigger('click');
            });
        });

    stdList = [];
    window.addToGroup = function(stdId){
        console.log(stdId);
        if(jQuery.inArray(stdId,stdList) !== -1){ // if item is already selected, remove it because user deselected it.
            stdList.splice($.inArray(stdId,stdList),1);
        }else{//going to add the item to the list.
            stdList.push(stdId);
        }
        console.log(stdList);
    }


    entryId0 = '';
    window.EntryStatus = function(entryId){
    	// 0 =>new entryId
    	// 1=>prev. exisisting group
        entryId0 = entryId;
    	
    	entryId == 1?$('#prevEntry').show():$('#prevEntry').hide();
    
    	// 
    }

    // Load groups dynamically
        window.loadGroup = function(){

            admId = $('#wgroupsetup-gs_adm_relid').val();
            progId = $('#wgroupsetup-gs_prog_relid').val();
            deptId = $('#wgroupsetup-gs_dept_relid').val();
            clslvlId = $('#wgroupsetup-gs_clslvl_relid').val();
            specId = $('#wgroupsetup-gs_spec_relid').val();
            
            $.post(
            '/w-group-setup/get-group-lists',
            {
                admId:admId,
                progId:progId,
                clslvlId:clslvlId,
                specId:specId,
                deptId:deptId

            },
            function(data){
                // wgroupassignment-gas_gset_relid
                $('#wgroupassignment-gas_gset_relid').html(data);
                });
        }

        window.isEmpty = function(val){
            return val == 'undefined'||val == ''||val == ' ';
        }

    $('#loadStudents').click(function(w){
        stdList = [];
    	w.preventDefault();
    	console.log('Getting selection entries.');

    	admId = $('#wgroupsetup-gs_adm_relid').val();
    	progId = $('#wgroupsetup-gs_prog_relid').val();
    	deptId = $('#wgroupsetup-gs_dept_relid').val();
    	clslvlId = $('#wgroupsetup-gs_clslvl_relid').val();
    	specId = $('#wgroupsetup-gs_spec_relid').val();

    	entryId = entryId0; //
    	prevAcadYearId = $('#groupsetup-prevacadyear').val();
    	prevSemId = $('#groupsetup-prevsem').val();

    	if(entryId == 1 && (isEmpty(prevAcadYearId) ||isEmpty(prevSemId))){
    		alert('Please complete Prev. Semester and Academic year detail, because you are updateing previously existing group.');
    	}else{
    		
    		$.post(
    			'/w-group-setup/get-group-list',
    			{
    				admId:admId,
    				progId:progId,
    				deptId:deptId,
    				clslvlId:clslvlId,
    				specId:specId,
    				entryId:entryId,
    				prevSemId:prevSemId,
    				prevAcadYearId:prevAcadYearId
    			},
    			function(data){
    				$('#stdGrpContainer').html(data);
                    loadGroup();
                    //loadGroupSetUpHistory($('#wgroupassignment-gas_gset_relid').val());
    			});
    	   }
        });
    ",
    $this::POS_READY
);
?>
