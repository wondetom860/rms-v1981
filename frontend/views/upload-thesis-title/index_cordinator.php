<?php

$this->title = "Submit Thesis title";
?>
<h3><?=  $this->title ?></h3>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-6">
			<div class="container-fluid">
				<?= $response ?>
			</div>
			<div class="container-fluid" id="studentListCntainer" >
				
			</div>
		</div>
		<div class="col-md-6" style="border-left: 1px solid dashed">
			<div class="container-fluid" id="tTitlesContainer">
				
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	window.loadSubmittedTitles = (stdId)=>{
		$('#tTitlesContainer').html('Loading Submitted Thesis Titles...');
		$.post('/upload-thesis-title/submitted-thesis-titles',{stdId:stdId}).done(function(data){
			$('#tTitlesContainer').html(data);
		});
	}
	_coffDetKey = '';
	window.changeStatus = (tkeyId,option) => {
		if (confirm('Are you sure to change thesis title status?')) {
			$.post('/thesis-title/change-status',{tkeyId:tkeyId,option}).done((resp) => { loadStudent(_coffDetKey); });
		}
	}
	window.loadStudent = function(coffDetKey){
		_coffDetKey = coffDetKey;
		$('#studentListCntainer').html('Loading .... ');
    	$('#tTitlesContainer').html('Loading .... ');
    	
    	$.post('/submit-grade2/enrstudlists2',
    		{
    			instId:coffDetKey, 
    			ad:1,
    		},
    		function(data){
    			$('#studentListCntainer').html(data);
    			loadSubmittedTitles(null);
    		});
	}
</script>