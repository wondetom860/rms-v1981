<?php
/** @var yii\web\View $this */
$this->title='Semester Courses Assigned'
?>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Your Courses for this semester.
				</div>
				<div class="panel-body" style="font-size: 9pt;">
					<div class="container-fluid" id="courseListContainer">
						Loading....
					</div>
				</div>
				<div class="panel-footer">
					
				</div>
			</div>
		</div>
		<div class="col-md-6" id="studentListContainer" style="display: none;">
			<div class="panel panel-info">
				<div class="panel-heading">
					Enrolled Students
					<i id='printIcon' style='cursor: pointer; float: right; color: blue;' title='Print' onclick='printCont()'>Print<span class='glyphicon glyphicon-print'></span></i>
				</div>
				<div class="panel-body" style="font-size: 9pt;">
					<div class="container-fluid table-responsive" id="enrStdsListContainer">
						Loading....
					</div>
				</div>
				<div class="panel-footer">
					
				</div>
			</div>
		</div>
	</div>
</div>
<!-- courseListContainer -->
<!-- Enrolled Students list enrStdsListContainer -->
<!-- studentListContainer   Bid DIV-->


<?=
$this->registerJs(
    "

    coffdetID1  ='';
    courseId1  ='';
    window.printCont = function(){

    	params = {
				'coffdetId':coffdetID1,
				'courseId':courseId1
			};
			// PrintEnrStudList => print-enr-stud-list
			 myWindow = window.open(
	          '/course2/print-enr-stud-list?params='+JSON.stringify(params), 
	          'Print Students Grade Report', 
	          'height=600,width=700');

	        myWindow.print();
	        // myWindow.close();
	        return false;

    	alert('Print NOT ready, Please wait');
    	
    }
    window.loadStudent  = function(coffdetID,courseId){
    	$('#studentListContainer').show();
    	$('#enrStdsListContainer').html('Loading students...');
    	coffdetID1 = coffdetID;
    	courseId1 = courseId;
    	$.post('/course2/enrstudlists',{instId:coffdetID,courseId:courseId},function(data){
    		$('#enrStdsListContainer').html(data);
    		});
    	// console.log(coffdetID, courseId);
    }
    // console.log('Generated Url: '+url);
    $.post('/course2/courselists',{},function(data){
    	//console.log(data);
    	$('#courseListContainer').html(data);
    	});
    ",
    $this::POS_READY
);
?>