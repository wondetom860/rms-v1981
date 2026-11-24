<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use \common\controllers\CommonControllerFunctions as CCF;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseOffering2Detail */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="course-offering2-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="conatiner-fluid" style="padding: 2px; font-size: 10pt;">
    	<div class="row">
    		<div class="panel panel-primary">
    			<div class="panel-heading">
    				Courses Offered for EXTENSION STUDENTS <b> FOR REGISTRAR</b>
    			</div>
    			<div class="panel-body">
    				<div class="conatiner-fluid">
    					<div class="row">
    						<div class="col-md-12">
    							<div class="conatiner-fluid"   id="cat_history">
    								
    							</div>
    						</div>
    						<div class="col-md-12">
    							<div class="conatiner-fluid"   id="coff_history">
    								
    							</div>
    					</div>
    					</div>
    				</div>
    			</div>
    			<div class="panel-footer">
    				
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
    	window.offerCourses = function(){
    		$('#coff_history').html('Offering courses....');

    		$.post('/course-offering2-ext/offer-courses',
    		{
    			courseList:JSON.stringify(courseList),
    			catList:JSON.stringify(catList),
    			totalCrdtHr:totalCrdtHr,
    			grp:$('#courseoffering2-coff2_group_relid :selected').val(),
    			adm : $('#courseoffering2-admission').val(),
	        	prog : $('#courseoffering2-program').val(),
	        	dept : $('#courseoffering2-department').val(),
	        	clslvl : $('#courseoffering2-clslvl').val()
    		}
    		).done(function(data){
    			if(data == 1){
    				loadCocat();
				}else{
					$('#coff_history').html(data);
				}
				
    		});
    	}

    	
    	window.approveOffer = function(id,action){
    		if(confirm('Approve Offering?')){
    			$.post('/course-offering2-ext/approve-offering',
	    		{
	    			id:id,
	    			action:action
	    		}
	    		).done(function(data){
	    			if(data != 0){
	    				loadCocat();
	    			}else{
	    				$('#coff_history').html(data);
	    			}
	    		});
    		}
    	}

    	window.loadCocat = function(){
    		$('#cat_history').html('Loading....');
	        $.post(
	            '/course-offering2-ext/get-courses-for-registrar-approval',
	            {
	              
	            },
	            function(data){
	                //console.log(data);
	                $('#cat_history').html(data);
	                // loadGroupCourses();
	            }
	        );
    	}
    	loadCocat();
    	",
    	$this::POS_READY
    );

    ?>