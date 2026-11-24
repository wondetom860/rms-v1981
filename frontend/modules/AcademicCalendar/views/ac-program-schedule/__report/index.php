<?php 
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

?>

<div class="row container-fluid" id="activity-form">
	<?php $form = ActiveForm::begin(['action' => '/AcademicCalendar/ac-program-schedule/create']); ?>

		<div class="row" style="background-color: #DAC;">
			<div class="col-md-4">
				<?= $form->field($model, 'action_relId')->dropdownList(ArrayHelper::map(\backend\models\AcademicCalanderAction::find()->all(),'id','aca_name'),
					[
						'prompt' => '',
						'onChange' => '
							// when change on activity is there, reload the contents of the programs as scheduled or not.

							reloadPrograms($(this).val());

						',
					]) ?>
			</div>
			<div class="col-md-3">
				<?= $form->field($model, 'start_date')->textInput(['type'=>'date','format' => 'd-m-Y']) ?>
			</div>
			<div class="col-md-3">
				<?= $form->field($model, 'end_date')->textInput(['type'=>'date','format' => 'd-m-Y']) ?>
			</div>

			<div class="col-md-2">
				<br>
				<?= "<label for='0011All' class='pull-right'>Use this for all programs</label><input type='checkBox' onChange='selectAll(this);return;' title='Use this schedule for all non-scheduled programs listed below.' name='selectionBox' class='selectBox pull-right' id='__0011All'>" ?>
			</div>
		</div>

		<div class="row">
			<!-- start of academic calendar  -->
			<div class="col-md-12" id="programs-container" style="font-family: cursive; font-size: 9pt;">
				<?= $this->render('programs',['records' => $records,'model' => $model]) ?>
			</div>
			<!-- end of programs for academic calendar -->

			<div class="hidden">
				<?= $form->field($model, '__selected_programs')->textInput(['readOnly' => true]) ?>
			</div>
		    <div class="col-md-12">
		    	<div class="form-group pull-right">
			        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
			    </div>
		    </div>
		</div>

    <?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
	window.reloadPrograms = (activityId) => {
		selectedPrograms = [];
		// console.log(__acadYearId);
		$("#programs-container").html("Reloading programs...");

		$.post('/AcademicCalendar/ac-program-schedule/get-program-schedule-per-activity',{activityId:activityId,ayId:__acadYearId}).done((resp) => { $("#programs-container").html(resp); })
	}
	window.selectAll = function(source){
        total = $('.selectionBox').length;
        selectedPrograms = [];
        if($('.selectionBox').length){
            $.each($('.selectionBox'),function(c){
                id = this['id'];
                status = source.checked?addtoList(Number(id)):source.checked;
                $('#'+id).prop({'checked':source.checked});
            });
        }
        $('#acprogramschedule-__selected_programs').val(JSON.stringify(selectedPrograms));
    }

    window.selectOne = (programId) => {
    	// $id = $("#"+programId).attr('id');
    	status = addtoList(Number(programId));
        $('#'+programId).prop({'checked':(status == 1)});

        if (status == 0) { $("#__0011All").prop({'checked' : false}); }
    }

    selectedPrograms = [];
    window.addtoList=function(programId){
        r = 0;
        if(jQuery.inArray(programId,selectedPrograms) !== -1){ 
        	// if item is already selected, remove it because user deselected it.
            selectedPrograms.splice($.inArray(programId,selectedPrograms),1);
            r= 0;
        }else{//going to add the item to the list.
            selectedPrograms.push(programId);
            r= 1;
        }
        $('#acprogramschedule-__selected_programs').val(JSON.stringify(selectedPrograms));
        return r;
    }

	function checkall(source){

        checkbox=document.querySelectorAll('input[type="checkbox"]');
        for (var i = 0; i <checkbox.length; i++) {
            if(checkbox[i]!=source){
                checkbox[i].checked=source.checked;
            }
        }
    }
</script>