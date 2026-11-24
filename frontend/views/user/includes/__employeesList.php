<?php
	if (isset($model)) {
		$persons = $model->find()->where(['person_user_relId' => NULL])->orderBy(['first_name' => SORT_ASC])->all();

		if ($persons && count($persons)) {
			echo "<table class='table table-condensed table-bordered'>
				<thead style='background-color:#FFD;'>
					<th>#</th>
					<th>". Yii::t('hrms','Full Name') ."</th>
					<th>". Yii::t('hrms','Proffession Category') ."</th>
					<th>". Yii::t('hrms','Options') ."</th>
				</thead><tbody>
					";
			$count = 0;
			// $model = new \frontend\modules\AcademicRankHistory\models\AcademicRankHistory();
			foreach ($persons as $person) {
				$fname = $person->getFullName();
				$btn = "<button class='btn btn-xs btn-primary' onclick='signUp({$person->id},". json_encode($fname) .");return false;'>Sign Up</button>";

				echo "<tr>
						<td>". ++$count ."</td>
						<td>{$fname}</td>
						<td>". Yii::t('hrms',$person->profession_type) ."</td>
						<td>{$btn}</td>
					</tr>";
			}
			echo "</tbody>
			</table>";
			
		}
	}else{
		echo "<p class='bg-warning'>INVALID_PARAMETERS_LIST</p>";
	}
?>