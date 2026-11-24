<?php
/** @var yii\web\View $this */
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
$this->title = Yii::t('rms','Semester Completion Check');
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin(); ?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">

        </div><hr>
        <div class="col-md-12">
        	<div class="panel panel-success">
            	<div class="panel-heading" style="text-align: center;">
            		Semester completion check for course enrolled students in this semester.
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
	
	",
	$this::POS_READY);
?>
