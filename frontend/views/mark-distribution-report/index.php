<?php
/** @var yii\web\View $this */

if (!isset($model)) {
	throw new \yii\web\NotFoundHttpException("Invalid request", 1);
}

$this->title = "This Semster Course Assessment Mark Distribution Report";
?>

<div class="container-fluid"> 
	<div class="row">
		<div class="col-md-12">
			<table class="table table-condensed">
				<tbody>
					<tr>
						<td>Course Detail</td>
						<td>
							Course Code : <i><?= $model->coff2detCourseRel->course_code ?></i><br>
							Course Title : <i><?= $model->coff2detCourseRel->course_title ?></i><br>
							Course Parent Department : <i><?= $model->coff2detCourseRel->courseDeptRel->dept_name ?></i><br>
						</td>
					</tr>
					<tr>
						<td>Course Offering Detail</td>
						<td>
							Academic Year - Semester : <i><?=  $model->coff2detCoff2Rel->coff2AcadYearRel->sch_year_desc.'-'.$model->coff2detCoff2Rel->coff2SemRel->semester_name?></i><br>
						</td>
					</tr>
					<tr>
						<td>Instructor Assignment</td>
						<td>
							<?php

								$coffdetails = $model->find()
								->innerJoin('course_offering2','course_offering2_detail.coff2det_coff2_relId = course_offering2.id')
								->where([
									'coff2_acadYear_relId'=>$model->coff2detCoff2Rel->coff2_acadYear_relId,
									'coff2_sem_relId'=>$model->coff2detCoff2Rel->coff2_sem_relId,
									'coff2det_course_relId'=>$model->coff2det_course_relId,
								])
								->orderBy('coff2det_coff2_relId')
								->all();

								if ($coffdetails && count($coffdetails) > 0) {
									echo "<table class='table table-condensed' style='font-size:8pt;'>";
									echo "<thead style='background-color:#8DF;'>
											<th>Group Name</th>
											<th>Instructor</th>
											<th>TA</th>
										</thead>";

									// public static function getCordinator($courseId,$acadYearId,$semId){//returns the INST_ID if the course is offered for multiple groups
								 //        $ccc = new \backend\models\CourseOffering2Detail();
								 //        if (!$ccc->checkRequired($courseId)) {//checi if offered for multiple
								 //            return NULL;
								 //        }
								 //        return CourseCordinator::find()
								 //            ->where([
								 //                'cc_acadYear_relId'=>$acadYearId,
								 //                'cc_sem_relId'=>$semId,
								 //            ])
								 //            ->andWhere([
								 //                'cc_course_relId'=>$courseId,
								 //            ])
								 //            ->one();
								 //    }


									// getCordinator($courseId,$acadYearId,$semId)
									$CourseCordinator = \backend\models\CourseCordinator::getCordinator($model->coff2det_course_relId,$model->coff2detCoff2Rel->coff2AcadYearRel->id,$model->coff2detCoff2Rel->coff2SemRel->id);
									$cc = '-';
									if ($CourseCordinator) {
										$cc = $CourseCordinator->ccInstRel->instUserRel->first_name.' '.$CourseCordinator->ccInstRel->instUserRel->middle_name;
										//echo "{$cc}";
									}
									foreach ($coffdetails as $key) {

										$inst = isset($key->coff2det_inst_relId) ? $key->coff2detInstRel->getFullName() : '-';
										$ta = isset($key->coff2det_TA_relId) ? $key->coff2detTARel->instUserRel->first_name .' '. $key->coff2detTARel->instUserRel->middle_name[0] : '-';
										$gs = $key->coff2detCoff2Rel->coff2GroupRel;
										echo "<tr>
												<td>{$gs->getGsDisp()}</td>
												<td>{$inst}</td>
												<td>{$ta}</td>
											</tr>"	;
									}
									echo "</table>";
								}

							?>
						</td>
					</tr>
					<tr>
						<td>Course Cordinator</td>
						<td><?= $cc ?></td>
					</tr>
					<tr>
						<td class="sdfsgfsdghj" colspan="2">
							<span id="sfgshjfsdf" data-coffdetid='<?= $model->id ?>' data-courseid = '<?= $model->coff2detCourseRel->id ?>'></span>
							....
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php
$this->registerJs(
	"
	window.loaddistr =  function(){
		id = $('#sfgshjfsdf').data('courseid');
		coffdetid = $('#sfgshjfsdf').data('coffdetid');
		$.post('/cont-assess-catagory2/get-distribution-report',{courseId:id,coffdetid:coffdetid}).done((data)=>{
			$('.sdfsgfsdghj').html(data);
		});
	}

	loaddistr();
	",
	$this::POS_READY);

?>