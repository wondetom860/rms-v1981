<?php

namespace frontend\controllers;
use yii;


class MoveAttendanceController extends \yii\web\Controller
{
    public function actionIndex()
    {
    	$model = \backend\models\Attendance::find()->all();
        return $this->render('index',['model'=>$model]);
    }

	public function actionDelRoot (){
		$homeId = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']):die('Invalid record entry');
		return \backend\models\Attendancedetail::deleteAll(['ad_attendance_relId' => $homeId]);
	}

	public function actionMoveAllAll(){
		$ids = isset($_POST['id']) && !empty($_POST['id']) ? $_POST['id'] : die('Invalid Entry');


		// $ids = json_decode($ids);

		$result = [];
		foreach ($ids as $key => $value) {
			$result[] = $this->__moveAll($value);
		}
		return 1;
	}

	public function actionMoveAll (){
		$homeId = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']):die('Invalid record entry');
		return $this->__moveAll($homeId);
		
	}

	private function __moveAll($homeId){
		$homemodel = \backend\models\Attendance::findOne($homeId);
		$details = \backend\models\Attendancedetail::find()->where(['ad_attendance_relId' => $homeId])->all();

		$stdIds = [];
		if ($details && count($details) > 0) {
			foreach ($details as $key) {
				$stdIds[] = $key->ad_student_relId;
			}

			$stdIds = json_encode($stdIds);

			$result = Yii::$app->db->createCommand()
				->update('attendance',
					['a_presentStudentsList' => $stdIds],
					['id'=>$homeId])
				->execute();
			return \backend\models\Attendancedetail::deleteAll(['ad_attendance_relId' => $homeId]);
		}else{
			return "Entry Error!";
		}
	}

}
