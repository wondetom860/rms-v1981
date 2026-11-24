<?php

namespace frontend\controllers;
use Yii;

class RequestSublimintaryExamGradeController extends \yii\web\Controller
{
    public function actionIndex()
    {

    	if (Yii::$app->user->can('Student')) {
    		$std = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);
    		if ($std && strtoupper($std->stdProgRel->program_name) == 'MASTERS') {
    			$modelSemregDet = new \frontend\models\SemesterRegistration2Detail();

                return $this->render('index_clearable',['clearableCourses'=>$modelSemregDet->getPGClearableCourses($std)]);
    		}
    		
    	}
        return $this->render('index');
    }

    public function actionCancelGradeLast(){
        $reqId = isset($_POST['reqId']) && !empty($_POST['reqId'])?intval($_POST['reqId']):die("Invalod inquery");
        if (Yii::$app->user->can('DepartmentHead')) {
            $request = \backend\models\SupplementaryExam::findOne($reqId);
            if ($request) {
                $transaction  = Yii::$app->db->beginTransaction();

                $result1 = Yii::$app->db->createCommand()
                    ->update('supplementary_exam',
                        [
                            'se_graded'=>NULL,
                            'se_newGrade_relId'=>NULL,
                        ],
                        ['id'=>$reqId,])
                    ->execute();
                if ($result1) {
                    $result2 = Yii::$app->db->createCommand()
                        ->update('semester_registration_2_detail',
                            [
                                'srd2_grade_relId'=>$request->se_prevGrade_relId,
                            ],
                            ['id'=>$request->se_srd2_relId])
                        ->execute();
                    if ($result2) {
                        $transaction->commit();
                        echo 1;
                        return;
                    }
                }

                $transaction->rollBack();
                echo "Operation Failed";
            }            
        }
    }

    public function actionSubmitGradeLast(){
        $reqId = isset($_POST['reqId']) && !empty($_POST['reqId'])?intval($_POST['reqId']):die("Invalod inquery");
        $gsId = isset($_POST['gsId']) && !empty($_POST['gsId'])?intval($_POST['gsId']):die("Invalod inquery");
        if (Yii::$app->user->can('DepartmentHead')) {
            $request = \backend\models\SupplementaryExam::findOne($reqId);

            if ($request && is_null($request->se_graded)) {

                $transaction  = Yii::$app->db->beginTransaction();
                $result1 = Yii::$app->db->createCommand()
                    ->update('supplementary_exam',
                        [
                            'se_graded'=>1,
                            'se_newGrade_relId'=>$gsId,
                        ],
                        ['id'=>$reqId,])
                    ->execute();
                if ($result1) {
                    $result2 = Yii::$app->db->createCommand()
                        ->update('semester_registration_2_detail',
                            [
                                'srd2_grade_relId'=>$gsId,
                                'srd2_gradeLocked'=>NULL,
                                'srd2_gradeApproved'=>NULL,
                                'srd2_gradeApproved_at'=>NULL,
                                'srd2_gradeApproved_by'=>NULL,
                            ],
                            ['id'=>$request->se_srd2_relId])
                        ->execute();
                    if ($result2) {
                        $transaction->commit();
                        echo 1;
                        return;
                    }
                }

                $transaction->rollBack();
                echo "Operation Failed";

            }else{
                return "already processed request!.";
            }
        }
    }
    public function actionSubmitGrade(){
        $modelSupExam = new \backend\models\SupplementaryExam();
        $modelGradeScale = new \backend\models\GradeScale();
        return $this->render('index_submit_grade',
            [
                'approvedRequests'=>$modelSupExam->getApprovedRequests(),
                'gradeScales'=>$modelGradeScale->getValidPGLetterGrades(),
        ]);
    }

    public function actionRequest(){
        $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']):die("Invalid Entry!");
        $stdObj = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);

        $srd2Obj = \frontend\models\SemesterRegistration2Detail::findOne($id);
        if ($srd2Obj) {
            if ($srd2Obj->srd2SemregRel->semregStdRel->id == $stdObj->id) {
                
                $modelSupExam = new \backend\models\SupplementaryExam();

                return $modelSupExam->saveW($srd2Obj);

            }else{
                die("<p class='bg-danger'>Requesting Sup. Ezam for whom????<p>");
            }
        }else{
            die("<p class='bg-danger'>Invlid Entry! RECORD_EMPTY<p>");
        }
    }

    public function actionGetRequestReport(){
        $supExamRequests  = NULL;
        if (Yii::$app->user->can('Student')) {
            $stdObj = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);
            $supExamRequests = \backend\models\SupplementaryExam::find()
                ->where(['se_std_relId'=>$stdObj->id])
                ->all();
        }else if(Yii::$app->user->can('DepartmentHead')){
            $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
            $supExamRequests = \backend\models\SupplementaryExam::find()
                ->innerJoin('semester_registration_2_detail','semester_registration_2_detail.id = supplementary_exam.se_srd2_relId')
                ->innerJoin('course','course.id = semester_registration_2_detail.srd2_course_relId')
                ->where(['course_dept_relId' => $inst->inst_dep_relId])
                ->all();
        }else{
            $supExamRequests = \backend\models\SupplementaryExam::find()->all();
        }

        return $this->renderPartial('se_reqests',['modelRequest'=>$supExamRequests]);
    }

    public function actionDeleteRequest($id){
        // return "Going to ID ".$id; 
        // exit();
        echo \backend\models\SupplementaryExam::findOne($id)->delete();
    }

    public function actionApproveRequest($id){
        $req = \backend\models\SupplementaryExam::findOne($id);
        if ($req) {
            if (Yii::$app->user->can('DepartmentHead')) {
                if (is_null($req->se_hodApproved) && $req->se_registrarApproved == 1) {
                    $transaction = Yii::$app->db->beginTransaction();
                    $res = Yii::$app->db->createCommand()
                        ->update('supplementary_exam',
                            [
                                'se_hodApproved'=>1,
                                'se_hodApproved_at'=> date("Y-m-d h:i:s") ,
                                'se_hodApproved_by'=>Yii::$app->user->identity->id,
                            ],
                            [
                                'id'=>$id,
                            ])
                        ->execute();
                    if ($res) {
                        $res2 = Yii::$app->db
                            ->createCommand()
                            ->update('semester_registration_2_detail',['srd2_grade_relId'=>NULL],['id'=>$req->se_srd2_relId])
                            ->execute();
                        if ($res2) {
                            $transaction->commit();
                            echo 1;
                        }else{
                            $transaction->rollBack();
                            echo "Operation failed";
                        }
                    }
                }else{
                    die("Error, Can NOT be processed!, already approved or pending for registrar approval");
                }
            }else if(Yii::$app->user->can('Registrar')){

                $transaction  =Yii::$app->db->beginTransaction();

                $result1 = Yii::$app->db->createCommand()
                    ->update('supplementary_exam',
                        [
                            'se_registrarApproved'=>1,
                            'se_registrarApproved_at'=> date("Y-m-d h:i:s") ,
                            'se_registrarApproved_by'=>Yii::$app->user->identity->id,
                        ],
                        [
                            'id'=>$id,
                        ])
                    ->execute();
                if ($result1) {
                    $result2 = Yii::$app->db->createCommand()
                        ->update('semester_registration_2_detail',
                            [
                                'srd2_gradeLocked'=>0,
                            ],
                            ['id'=>$req->se_srd2_relId])
                        ->execute();
                    $transaction->commit();
                    return 1;
                }
             $transaction->rollBack();
             echo "Operation failed.";
            }else{
                die("We dont know you!");
            }
        }else{
            die("Such request NOT found.");
        }
    }

    public function actionDeApproveRequest($id){
        $req = \backend\models\SupplementaryExam::findOne($id);
        if ($req) {
            if (Yii::$app->user->can('DepartmentHead')) {
                if (!is_null($req->se_hodApproved) && $req->se_registrarApproved == 1) {
                    $transaction = Yii::$app->db->beginTransaction();
                    $res = Yii::$app->db->createCommand()
                        ->update('supplementary_exam',
                            [
                                'se_hodApproved'=>NULL,
                                'se_hodApproved_at'=> NULL ,
                                'se_hodApproved_by'=>NULL,
                            ],
                            [
                                'id'=>$id,
                            ])
                        ->execute();
                    if ($res) {
                        $res2 = Yii::$app->db
                            ->createCommand()
                            ->update('semester_registration_2_detail',['srd2_grade_relId'=>$req->se_prevGrade_relId],['id'=>$req->se_srd2_relId])
                            ->execute();
                        $transaction->commit();
                        return 1;
                    }
                echo "{$res2}";die();

                }else{
                    die("Error, Can NOT be processed!, already de-approved or not yet approved by registrar");
                }
            }else if(Yii::$app->user->can('Registrar')){
                echo Yii::$app->db->createCommand()
                    ->update('supplementary_exam',
                        [
                            'se_registrarApproved'=>NULL,
                            'se_registrarApproved_at'=> NULL,
                            'se_registrarApproved_by'=>NULL,
                        ],
                        [
                            'id'=>$id,
                        ])
                    ->execute();
            }else{
                die("We dont know you!");
            }
        }
    }

}
