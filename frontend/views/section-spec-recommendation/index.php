<?php
/** @var yii\web\View $this */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
$this->title = Yii::t('rms','Section / Group / Specialization reommendations made by HoDs.');
?>
<?php $form = ActiveForm::begin(); ?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Group / Specialization  Assignment recommendation -recommend un-grouped students to a group /specialization for this semester
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-2">
                            <?=
                            $form->field($modelg, 'gs_adm_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\Admission::find()->all(), 'id', 'admission_name'), [
                                'prompt' => 'Select Admission',
                                    ]
                            )
                            ?>

                        </div>
                        <div class="col-md-2">
                            <?=
                            $form->field($modelg, 'gs_prog_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'), [
                                'prompt' => 'Select Program',
                                'onChange' => ' 
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
                            )
                            ?>
                        </div>
                        <div class="col-md-2">
                            <?php 
                            $dept = NULL;
                                if (Yii::$app->user->can('DepartmentHead')) {
                                    $dept = \backend\models\Instructor::find()->where(['inst_user_relId'=>Yii::$app->user->identity->id])->one();
                                    $dd = \backend\models\Department::find()->where(['id'=>$dept->inst_dep_relId])->all();
                                } else{
                                	$dd = \backend\models\Department::find()->all();
                                }
                            ?>
                            <?=
                            $form->field($modelg, 'gs_dept_relId')->dropdownList(
                                    ArrayHelper::map($dd, 'id', 'dept_name'), [
                                        'prompt'=>'Select Department...',
                                'onChange' => ' 
                                    $.post( "' . Yii::$app->urlManager->createUrl('specialization/listspec') . '",
                                        {
                                            progId:$("#wgroupsetup-gs_prog_relid").val(),
                                            deptId:$(this).val()
                                        }, 
                                        function( data ) {
                                        //console.log("Returned: "+data);
                                      $( "#wgroupsetup-gs_spec_relid").html( data ).trigger("change");
                                      $( "#secspecrecomend-ssr_spec_relid").html( data );
                                    });
                                    // coffLoad();
                                ',
                            ]
                            )
                            ?>
                        </div>
                        <div class="col-md-2">
                            <?=
                            $form->field($modelg, 'gs_clslvl_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'), [
                                'prompt' => 'Select Class Level',
                                // 'onChange' => 'loadGroup()',
                                    ]
                            )
                            ?>

                        </div>
                        <div class="col-md-2">
                            <div class="col-md-12">
                                <?=
                                $form->field($modelm, 'recommend')->dropdownList([0=>'Specialization',1=>'Section / Group'], [
                                    'prompt' => 'Select recommendation Type...',
                                    'onChange' => 'changerecomendationType($(this).val())',
                                        ]
                                )->label('Recommended')
                                ?>
                            </div>
                        </div>
                        <div class="col-md-2"><br>
                        	<button class="btn btn-sm btn-primary" onclick="changerecomendationType($('#secspecrecomend-recommend').val());return false;">Load</button>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div><hr>
        <div class="col-md-12">
        	<div class="panel panel-success">
            	<div class="panel-heading" style="text-align: center;">
            		Section / Group / Specialization recommendations made by HoDs.
            	</div>
            	<div class="panel-body">
            		<div class="container-fluid" id="groupMembersContiner">
            			
            		</div>
            	</div>
            </div>
        </div>
	</div>
</div>



<?php

$this->registerJs(
	"
	window.changerecomendationType = function(recType){
		console.log('Doing....');
		admId = $('#wgroupsetup-gs_adm_relid').val();
        progId = $('#wgroupsetup-gs_prog_relid').val();
        deptId = $('#wgroupsetup-gs_dept_relid').val();
        clslvlId = $('#wgroupsetup-gs_clslvl_relid').val();
        specId = $('#wgroupsetup-gs_spec_relid').val();

        recType = $('#secspecrecomend-recommend').val();

        recType == 0?'0':recType;
        $.post('/section-spec-recommendation/load-recommendation-history',
            {
                admId:admId,
                progId:progId,
                deptId:deptId,
                clslvlId:clslvlId,
                specId:specId,
                recType:recType,

            }).done(function(data){
            $('#groupMembersContiner').html(data);
        });
	}
	",
	$this::POS_READY);
?>
