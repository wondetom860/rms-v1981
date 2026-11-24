<?php 

$this->title = 'New admission form';

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="container-fluid">
	<h3><?= $this->title ?></h3>
	<?php 
	if ($prevAdmissionEntry && count($prevAdmissionEntry) > 0) {
		$indexed = \yii\helpers\ArrayHelper::index($prevAdmissionEntry,'std_prog_relId');
		$prgs = array_keys($indexed);
	}else{
		$prgs = [];
	}

	$scheduleEntry = Yii::$app->Admission->ScheduleOpen();
    if ($scheduleEntry && count($scheduleEntry) > 0) {//check if schedule for admission is open
        foreach ($scheduleEntry as $keySched) {
        	$activeProgram = $keySched->activeProgramRel;
            if ($activeProgram->program_relId) {
            	if (in_array($activeProgram->program_relId, $prgs)) {
            		continue;
            	}
                echo "
                    <a onclick='applicationForm1({$activeProgram->program_relId},$keySched->id)'>{$keySched->activeProgramRel->getClassLevelHeader()}</a>
                    <br><small class='bg-warning highlighted pull-right'>Active : <i>{$keySched->getScheduleInterval()}</i></small>
                <hr>";
            }else{
               echo "Schedule NOT open for admission";
	        }
	    }
	}else{
        echo "Schedule NOT open for admission";
    }

	?>
	<div class="container-fluid" id="new_admission_form_container" style="border: 1px solid black;">
		<small>New admission form</small>
	</div>
</div>

<?php 

$this->registerJs(
	"
	window.applicationForm1 = (id,settingId) =>{
        $('#new_admission_form_container').html('Loading form....');
        $.post('/admit-student/self-admission-form2',{id:id,v2:1,settingId : settingId}).done((data)=>{
        	console.log('Loadded');
            $('#new_admission_form_container').html(data);
            $('#user1-first_name').focus();
        });
    }
	",
	$this::POS_READY)

 ?>
