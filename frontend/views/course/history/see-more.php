<?php

if (isset($course)) {
?>
<h5 style="background-color: #DDF;">Course catalog inforamtion - <?= $course->course_title ?></h5>
<?= $this->render('more-de-catalog',['course' => $course]) ?>
<h5 style="background-color: #DDF;">Course offering history -
    <?= "{$course->course_title}(<i>{$course->getCourseCredits()}</i>)" ?></h5>
<?= $this->render('more-de-offering',['course' => $course]) ?>
<?php }else{
	echo "No such entry found.";
	return;
}

?>