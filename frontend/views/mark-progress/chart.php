<?php

echo "<div class='container vertical rounded'>";

// echo "<pre>";
// var_dump($courses);
foreach ($courses as $key) {
	echo "
	    	<div class='progress-bar'>
	    	<span style='font-size:6pt;'>{$key['course_code']}</span>
			    <div class='progress-track'>
			      <div class='progress-fill'>
			        <span>{$key['Mark']}%</span>
			        <h5 style='display:none;'>{$key['Course_Title']} - [{$key['title']}] </h5>
			      </div>
			    </div>
			  </div>
		";
}
echo "</div>";

$this->registerJs(
	"
	$('.horizontal .progress-fill span').each(function(){
	  var percent = $(this).html();
	  $(this).parent().css('width', percent);
	});

	function getInterval(totalMark){
	 	
		if (totalMark <=100 && totalMark >= 84.99) {
			// console.log('Success');
			return '#27AE60';
		}else if(totalMark <=84.98 && totalMark >= 79.99){
			// console.log('default');
			return '#F55041';
		}else if(totalMark <=74.98 && totalMark >= 69.99){
			// console.log('default');
			return '#F40040';
		}else if(totalMark <=64.98 && totalMark >= 44.99){
			// console.log('default');
			return '#F10041';
		}else if(totalMark <=44.98){
			// console.log('Wanrning');
			return '#BA4A00';
		}else{
			console.log('None' + totalMark);
		}
	 	
	 }

	$('.vertical .progress-fill span').each(function(){
	  var percent = $(this).html();
	  var tpp = percent.slice(0, percent.length - 1);
	  var pTop = 100 - ( percent.slice(0, percent.length - 1) ) + '%';
	  title = $(this).parent().find('h5').html();
	  console.log(title);
	  $(this).parent().parent().prop('title',title);
	  $(this).parent().css({
	    'height' : percent,
	    'top' : pTop,
	    'backgroundColor':getInterval(tpp)
	  });
	});

	// 
	",
	$this::POS_READY);
?>