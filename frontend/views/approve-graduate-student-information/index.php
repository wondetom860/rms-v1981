<?php
/** @var yii\web\View $this */
$this->title  = "Graduate Students' Information";
$this->params['breadcrumbs'][] = $this->title;
?>
<h3 style="border-bottom: 1px solid black;"><?= 'Approve '.$this->title ?></h3>
<div class="row">
	<div class="col-md-12">
		<div class="container-fluid" id="selectContainer">
			<?= $this->render('includes/__selectForm') ?>
		</div>
		<div class="container-fluid" id="listContainer" style="font-size: 11px;">
			
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		
	</div>
</div>
<?php
$this->registerJs(
	"

	window.pointHistory = (recordId)=>{
		gstdList = [];
		gstdList.push(recordId);
        myWindow = window.open(
          '/graduate-student/get-point-history?sfsgfgsfjk=sfjkhsjfdgh&fdfhsgh='+JSON.stringify(gstdList)+'&hfggdjhjhd=fsghd', 
          'title=Graduate Student Point History', 
          'height=600,width=1200');
	}
	$.post('/graduate-student/get-student-list-for-approval',
        {}, 
        function( data ) {
            $('#listContainer').html( data );
    }); 
	",
	$this::POS_READY);

?>