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
                                    if (Yii::$app->user->can('DepartmentHead')) {
                                        $dept = \backend\models\Instructor::find()->where(['inst_user_relId'=>Yii::$app->user->identity->id])->one();
                                    } 
                                ?>
                                <?=
                                $form->field($modelg, 'gs_dept_relId')->dropdownList(
                                        ArrayHelper::map(\backend\models\Department::find()->where(['id'=>$dept->inst_dep_relId])->all(), 'id', 'dept_name'), [
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
                                    'onChange' => 'loadGroup()',
                                        ]
                                )
                                ?>

                            </div>
                            <div class="col-md-2">

                                <?=
                                $form->field($modelg, 'gs_spec_relId')->dropdownList(
                                        ArrayHelper::map(\backend\models\Specialization::find()->all(), 'id', 'spec_name'), [
                                    'prompt' => 'Select Specialization',
                                        ]
                                )
                                ?>
                            </div>
                            <div class="col-md-2">
                                <div class="col-md-12">
                                    <?=
                                    $form->field($modelm, 'recommend')->dropdownList([0=>'Specialization',1=>'Section / Group'], [
                                        'prompt' => 'Select Your recommendation Type...',
                                        'onChange' => 'changerecomendationType($(this).val())',
                                            ]
                                    )
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Student / Group List to be assigned to new group for this semester.
                    </div>
                    <div class="panel-body">
                        <div class="container-fluid">
                            <div class="col-md-5">
                                <div class="row">
                                    <div class="container-fluid" id="prevEntry" style="display: none; background-color: #399">
                                        <div class="col-md-4">
                                            <?=
                                            $form->field($modelg, 'prevAcadYear')->dropdownList(ArrayHelper::map(\backend\models\SchoolYear::find()->all(), 'id', 'sch_year_desc'), [
                                                'prompt' => 'Select prev. Acad year.',
                                                    ]
                                            )->label('Prev. Acad. Yr.');
                                            // ->where([' <> ','sch_year_desc',Yii::$app->params['C']['Y']])
                                            ?>
                                        </div>
                                        <div class="col-md-4">
                                            <?=
                                            $form->field($modelg, 'prevSem')->dropdownList(ArrayHelper::map(\backend\models\Semester::find()->where([ ' <> ', 'semester_name', Yii::$app->params['C']['S']])->all(), 'id', 'semester_name'), [
                                                'prompt' => 'Select Prev. Sem',
                                                'onChange' => 'LoadPrevGroup($(this).val(),$("#wgroupsetup-prevacadyear").val(),$("#wgroupsetup-gs_adm_relid").val(),$("#wgroupsetup-gs_prog_relid").val(),$("#wgroupsetup-gs_dept_relid").val(),$("#wgroupsetup-gs_clslvl_relid").val())'
                                                    ]
                                            )->label('Prev.Sem.')
                                            ?>
                                        </div>
                                        <div class="col-md-4">
                                            <?=
                                            $form->field($modelg, 'prevGroup')->dropdownList([], [
                                                'prompt' => 'Select Prev. Group',
                                                    ]
                                            )->label('Prev.Group.')
                                            ?>
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <button class="btn btn-secondary btn-sm" id="loadStudents">Load Students</button>
                                        <hr>
                                        <div class="container-fluid" id="stdGrpContainer" style="background-color: #c99; font-size: 9pt;">
                                            Wainting for your selection...
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1" style="vertical-align: middle;">

                                <div class="row">
                                    <div style="display: none;" id="change_sec" class="col-md-12">
                                        <?php
                                            $instdept = NULL;
                                            if(Yii::$app->user->can('DepartmentHead')){
                                                $instdept = \backend\models\Instructor::find()->where(['inst_user_relId'=>Yii::$app->user->identity->id])->one()->inst_dep_relId;
                                            }?>
                                        <?=
                                        $form->field($modelm, 'ssr_rec_section')->textInput(['required'=>TRUE,])->label('Section / Group');
                                        ?>
                                    </div>
                                    <div style="display: none;" id="change_spec" class="col-md-12">
                                        <?php
                                            $instdept = NULL;
                                            if(Yii::$app->user->can('DepartmentHead')){
                                                $instdept = \backend\models\Instructor::find()->where(['inst_user_relId'=>Yii::$app->user->identity->id])->one()->inst_dep_relId;
                                            }?>
                                        <?=
                                        $form->field($modelm, 'ssr_rec_spec')->dropdownList(ArrayHelper::map(\backend\models\Specialization::find()->where(['spec_dep_relId'=>$instdept])->all(), 'id', 'spec_name'), [
                                             'prompt' => 'Select Specialization',
                                             'onChange' => 'loadGroupSetUpHistory($(this).val())',
                                            ]
                                        )->label('Specialization')
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Save') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'assignBtn']) ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="container-fluid" id="groupMembersContiner">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                    </div>
                </div>
            </div>
            <!-- <div class="col-md-1">
                <div class="container-fluid" style="background-color: #434; height: 100%;">
                    
                </div>
            </div> -->
            <!-- <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        
                    </div>
                    <div class="panel-body">
                        
                    </div>
                    <div class="panel-footer">
                        
                    </div>
                </div>
            </div> -->
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<!-- groupMembersContiner -->

<?php
$this->registerJs(
        "


    prevSemId = '';
    prevAcadYearId = '';
    window.LoadPrevGroup  = function(semId,acadYearId,admId,progId,deptId,clslvlId){
        console.log(semId,acadYearId,admId,progId,deptId,clslvlId);
        prevAcadYearId = acadYearId;
        prevSemId = semId;
        // /admin
        $.post('/w-group-setup/get-group-lists-peryear2',
            {
                acadYearId:acadYearId,
                semId:semId,
                admId:admId,
                progId:progId,
                deptId:deptId,
                clslvlId:clslvlId

            }).done(function(data){
            $('#wgroupsetup-prevgroup').html(data).trigger('change');
            // console.log(data);
        });
    }
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

    window.changerecomendationType = function(type){
        if(type == 0){
            $('#change_spec').show();
            $('#change_sec').hide();
        }else if(type == 1){
            $('#change_sec').show();
            $('#change_sec').focus();
            $('#change_spec').hide();
        }else{
            alert('Invalid selection of type.');
            return false;
        }
        EntryStatus(1);
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
        admId = $('#wgroupsetup-gs_adm_relid').val();
        progId = $('#wgroupsetup-gs_prog_relid').val();
        deptId = $('#wgroupsetup-gs_dept_relid').val();
        clslvlId = $('#wgroupsetup-gs_clslvl_relid').val();
        specId = $('#wgroupsetup-gs_spec_relid').val();

        recType = $('#secspecrecomend-recommend').val();

        if(recType == 0){
            gid = $('#secspecrecomend-ssr_rec_spec').val();
        }else{
            gid = $('#secspecrecomend-ssr_rec_section').val();
        }
        recType == 0?'0':recType;

        entryId = entryId0; //
        prevAcadYearId = $('#wgroupsetup-prevacadyear').val();
        prevSemId = $('#wgroupsetup-prevsem').val();
        prevGroup = $('#wgroupsetup-prevgroup').val();

        $.post(
        '/section-spec-recommendation/create',
        {
            stdList:stdListS,
            gid:gid,
            admId:admId,
            progId:progId,
            deptId:deptId,
            clslvlId:clslvlId,
            specId:specId,
            entryId:entryId,
            recType:recType,
            prevSemId:prevSemId,
            prevAcadYearId:prevAcadYearId,
            prevGroup:prevGroup
        },
        function(data){
            if(data == 1){
                // $('#groupMembersContiner').html(data);
                $('#loadStudents').trigger('click');
            }else{
                $('#groupMembersContiner').html(data);
            }
            
            });
        });

    stdList = [];
    window.addToGroup = function(stdId){
        // console.log(stdId);
        if(jQuery.inArray(stdId,stdList) !== -1){ // if item is already selected, remove it because user deselected it.
            stdList.splice($.inArray(stdId,stdList),1);
        }else{//going to add the item to the list.
            stdList.push(stdId);
        }
        // console.log(stdList);
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
            '/w-group-setup/get-group-lists22',
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

        window.loadRecommendationHistory = function(){
            admId = $('#wgroupsetup-gs_adm_relid').val();
            progId = $('#wgroupsetup-gs_prog_relid').val();
            deptId = $('#wgroupsetup-gs_dept_relid').val();
            clslvlId = $('#wgroupsetup-gs_clslvl_relid').val();
            specId = $('#wgroupsetup-gs_spec_relid').val();

            recType = $('#secspecrecomend-recommend').val();

            recType == 0?'0':recType;

            entryId = entryId0; //
            prevAcadYearId = $('#wgroupsetup-prevacadyear').val();
            prevSemId = $('#wgroupsetup-prevsem').val();
            prevGroup = $('#wgroupsetup-prevgroup').val();
            $.post('/section-spec-recommendation/load-recommendation-history',
                {
                    admId:admId,
                    progId:progId,
                    deptId:deptId,
                    clslvlId:clslvlId,
                    specId:specId,
                    entryId:entryId,
                    recType:recType,
                    prevSemId:prevSemId,
                    prevAcadYearId:prevAcadYearId,
                    prevGroup:prevGroup

                }).done(function(data){
                $('#groupMembersContiner').html(data);
            });
        }

        window.isEmpty = function(val){
            return val == 'undefined'||val == ''||val == ' ';
        }

    $('#loadStudents').click(function(w){
        stdList = [];
        w.preventDefault();
        // console.log('Getting selection entries.');

        admId = $('#wgroupsetup-gs_adm_relid').val();
        progId = $('#wgroupsetup-gs_prog_relid').val();
        deptId = $('#wgroupsetup-gs_dept_relid').val();
        clslvlId = $('#wgroupsetup-gs_clslvl_relid').val();
        specId = $('#wgroupsetup-gs_spec_relid').val();

        recType = $('#secspecrecomend-recommend').val();

        recType == 0?'0':recType;

        entryId = entryId0; //
        prevAcadYearId = $('#wgroupsetup-prevacadyear').val();
        prevSemId = $('#wgroupsetup-prevsem').val();
        prevGroup = $('#wgroupsetup-prevgroup').val();

        if(entryId == 1 && (isEmpty(prevAcadYearId) ||isEmpty(prevSemId))){
            alert('Please complete Prev. Semester and Academic year detail, because you are updateing previously existing group.');
        }else{
            
            $.post(
                '/w-group-setup/get-group-list22',
                {
                    admId:admId,
                    progId:progId,
                    deptId:deptId,
                    clslvlId:clslvlId,
                    specId:specId,
                    entryId:entryId,
                    recType:recType,
                    prevSemId:prevSemId,
                    prevAcadYearId:prevAcadYearId,
                    prevGroup:prevGroup
                },
                function(data){
                    $('#stdGrpContainer').html(data);
                    loadGroup();

                    // load priviously made recommendation if any.
                    loadRecommendationHistory();

                    //loadGroupSetUpHistory($('#wgroupassignment-gas_gset_relid').val());
                });
           }
        });
    ", $this::POS_READY
);
?>