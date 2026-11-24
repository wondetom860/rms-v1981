<?php

namespace backend\models;
use yii;
use backend\models\SemesterEvaluation2;
use \common\components\CommonControllerFunctions as CCF;

class SemesterEvaluationReportController extends \yii\web\Controller
{

	public static $acadYearId;
    public static $semId;
    protected static function __constructor(){

        $cs = CCF::currentTime();
        static::$acadYearId = $cs['Y'];
        static::$semId = $cs['S'];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public static function getReport($modelId){
    	//return "calculating...";
    	// static::__constructor();
    	// $acadYearId = static::$acadYearId;
     //    $semId = static::$semId;

    	$exst = SemesterEvaluation2::findOne([
            'id'=>$modelId,
        ]);

        if ($exst) {

        	$acadYearId = $exst->semev_acadYear_relId;
        	$semId = $exst->semev_sem_relId;
        	// identify the evaluation type made.
        	$evalueee = $exst->semev_evaluee_relId;

        	// $evalType = \backend\models\SemesterEvaluationTypes::findOne(['id'=>$exst->semev_evalType_relId]);
        	$evalType = $exst->semevEvalTypeRel;

        	if ($evalType->semev_type_level == 5 || $evalType->semev_type_level == 4 || $evalType->semev_type_level == 12) { //INST / TA evaluation 
        			
	        	$gset = \backend\models\WGroupSetup::findOne(['id'=>$exst->semev_group_relId]);

		    	$semreg2 = \frontend\models\SemesterRegistration2::find()
			    	->innerJoin('semester_registration_2_detail','semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
			    	->where([
			                'semreg_acadYear_relId'=>$acadYearId,
			                'semreg_semester_relId'=>$semId,
			            ])
			    	->andWhere([
			    		'semester_registration_2_detail.srd2_group_relId' => $exst->semev_group_relId,
			    		'semester_registration_2_detail.srd2_course_relId'=>$exst->semev_course_relId,
			    	])
			    	->with('semesterRegistration2Detail')
			    	->all();
			    	// $semreg2 = $semreg2->all();

			    $totalEvaluators= [];
			    $stds = [];
		        if ($semreg2) {
		        	$stdddd = [];
		        	foreach ($semreg2 as $key) {
		        		array_push($stdddd, $key->semregStdRel->std_user_relId);
		        	}

			        // $stdUser = \backend\models\Student::find()->where(['id'=>$stds])->all();

			        
			        // if ($stdUser && count($stdUser) > 0) {
			        // 	foreach ($stdUser as $key) {
			        // 		array_push($stdddd, $key['std_user_relId']);
			        // 	}
			        // }

			        $totalEvaluators = \backend\models\User::find()->where(['id'=>$stdddd])->all();

		        }

	        	$evaluated = \backend\models\SemesterEvaluation2Detail::find()
			        	->where([
			        		'semev2det_semev_relId'=>$exst->id, 
			        		'semev2det_completed'=>1
			        	])
			        	->all();
			    $eUserIds = [];
				if ($evaluated && count($evaluated) > 0) {
					foreach ($evaluated as $key) {
						array_push($eUserIds, $key['semev2det_evaluator_relId']);
					}
				}

				$eUsers = \backend\models\User::find()->where(['id'=>$eUserIds])->all();

	        	return [$eUsers,$totalEvaluators,$evalType->id];

        	}else if ($evalType->semev_type_level == 7) { // TA evaluation by course held instructor. 
        		$user = \backend\models\User::findOne(['id'=>$exst->semev_evaluee_relId]);
        		$inst = \backend\models\Instructor::findOne(['inst_user_relId'=>$user->id]);

                $coff3 =  \backend\models\CourseOffering2::find()
                    ->innerJoin('course_offering2_detail',
                        'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
                    ->filterWhere([
                        'course_offering2.coff2_acadYear_relId'=>$acadYearId,
                    ])
                    ->andFilterWhere([
                        'course_offering2.coff2_sem_relId'=>$semId
                    ])
                    ->with('coff2CoffDet')
                    ->andFilterWhere([
                        'course_offering2_detail.coff2det_TA_relId'=>$inst->id
                    ])->all();

                    $instructors = [];
                if ($coff3 && count($coff3) > 0) {
                	foreach ($coff3 as $key) {
                		array_push($instructors, $key['coff2CoffDet']['coff2det_inst_relId']);
                	}
                }

                $insts = \backend\models\Instructor::find()->where(['id'=>$instructors])->all();
                
                $tUserIds = [];
                $evaluated = \backend\models\SemesterEvaluation2Detail::find()
				        	->where([
				        		'semev2det_semev_relId'=>$exst->id, 
				        		'semev2det_completed'=>1,
				        	])
				        	->all();

                if ($insts && count($insts) > 0) {
                	foreach ($insts as $key) {
                		if ($exst->semev_evaluee_relId = $evaluated['semev2det_evaluator_relId']) {
                			continue;
                		}
                		array_push($tUserIds, $key['inst_user_relId']);
                	}
                }

                $tUsers = \backend\models\User::find()->where(['id'=>$tUserIds])->all();
        		
				$eUserIds = [];
				if ($evaluated && count($evaluated) > 0) {
					foreach ($evaluated as $key) {
						array_push($eUserIds, $key['semev2det_evaluator_relId']);
					}
				}

				$eUsers = \backend\models\User::find()->where(['id'=>$eUserIds])->all();

				return [$eUsers,$tUsers,$evalType->id];
        	}else if ($evalType->semev_type_level == 6) { // Peer evaluation
        		

        		// Those who can evaluate Instructor as a Peer, 
        			//Those in the same dept.
        			//Those who assigned in ths semester.
        			//count, =>total

        		$evaluated = \backend\models\SemesterEvaluation2Detail::find()
				        	->where([
				        		'semev2det_semev_relId'=>$exst->id, 
				        		'semev2det_completed'=>1
				        	])
				        	->all();
				$eUsers = [];
				if ($evaluated && count($evaluated) > 0) {
					foreach ($evaluated as $key) {
						array_push($eUsers, $key['semev2det_evaluator_relId']);
					}
				}

				$evaluatedUsers = \backend\models\User::find()->where(['id'=>$eUsers])->all();
				        	// semev_evaluee_relId
				        	// // But don't count the evaluee him/her self as evaluator.
				$instructor =\backend\models\Instructor::findOne(['inst_user_relId'=>$exst->semev_evaluee_relId]);
				
				$peers = \backend\models\Instructor::find()
					->where(['inst_dep_relId'=>$instructor->inst_dep_relId])
					->andWhere(['<>','id',$instructor->id])
					->all();
	           	
	           	$peersArray = [];
	           	if ($peers && count($peers) > 0) {
	           		foreach ($peers as $p) {
	           			array_push($peersArray, $p['id']);
	           		}
	           	}

	           	$coff = \backend\models\CourseOffering2Detail::find()
	                    ->innerJoin('course_offering2',
	                        'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
	                    ->filterWhere([
	                        'course_offering2.coff2_acadYear_relId'=>$acadYearId,
	                    ])
	                    ->andFilterWhere(['course_offering2.coff2_sem_relId'=>$semId])
	                    ->with('coff2detCoff2Rel');

	                    // The peers also have to include the TAs assigned for this semsetr.
	                    $coff = $coff->andFilterWhere(['OR',
	                    		['IN','course_offering2_detail.coff2det_inst_relId',$peersArray],
	                    		['IN','course_offering2_detail.coff2det_TA_relId',$peersArray]
	                    	]);
	                    $coff = $coff->distinct('id');

	                    $coff = $coff->groupBy('course_offering2_detail.coff2det_course_relId')->all();

	                    $instList = [];
	                    if ($coff && count($coff) > 0) {
	                    	foreach ($coff as $inst) {
	                    		array_push($instList,$inst['coff2det_inst_relId']);
	                    		if (!is_null($inst['coff2det_TA_relId']) && !array_key_exists($inst['coff2det_TA_relId'], $instList)) {
	                    			array_push($instList,$inst['coff2det_TA_relId']);
	                    		}
	                    	}
	                    }

	                    $instructors = \backend\models\Instructor::find()->where(['id'=>$instList])->all();
	                    $users = [];
	                    if ($instructors && count($instructors) > 0) {
	                    	foreach ($instructors as $key) {
	                    		if ($evalueee == $key['inst_user_relId']) {
	                    			continue;
	                    		}
	                    		array_push($users, $key['inst_user_relId']);
	                    	}
	                    }

	                    $total = \backend\models\User::find()->where(['id'=>$users])->all();
            

				// = 0;

				return [$evaluatedUsers,$total,$evalType->id];
        	}else if ($evalType->semev_type_level == 8 || $evalType->semev_type_level == 9) { //Suppervisiion types by HoD

        		$evaluated = \backend\models\SemesterEvaluation2Detail::find()
				        	->where([
				        		'semev2det_semev_relId'=>$exst->id, 
				        		'semev2det_completed'=>1
				        	])
				        	->all();
				// $users = \backend\models\User::findByRole('DepartmentHead');

				// Get evaluees dept...
				//$instructor = ;
				$instDept = \backend\models\Instructor::findOne(['inst_user_relId'=>$exst->semev_evaluee_relId])->inst_dep_relId;
				// if
				// Get active HoD of this inst Dept 
				$hod = \backend\models\DepartmentHead::findOne([
					'dh_status'=>1,
					'dh_dept_relId'=>$instDept
				]);

				$user = \backend\models\Instructor::findOne(['id'=>$hod->dh_inst_relId])->inst_user_relId;

        		// $tUsers = [];
        		// if ($users && count($users) >0) {
        		// 	foreach ($users as $key) {
        		// 		if () { //check if the HoD is of evaluees dept.
        		// 			array_push($tUsers, $key['id']);
        		// 		}
        		// 	}
        		// }


				$eUsers = [];
				if ($evaluated && count($evaluated) > 0) {
					foreach ($evaluated as $key) {
						array_push($eUsers, $key['semev2det_evaluator_relId']);
					}
				}
				$evaluatedUsers = \backend\models\User::find()->where(['id'=>$eUsers])->all();

				$user = \backend\models\User::find()->where(['id'=>$user])->all();
        		return [$evaluatedUsers,$user,$evalType->id] ;

        	}else if ($evalType->semev_type_level == 13) { //Supervission of PG instructor by PG-Cordinator..
        		$users = \backend\models\User::findByRole('PGCoordinator');

        		$tUsers = [];
        		if ($users) {
        			foreach ($users as $key) {
        				array_push($tUsers, $key['id']);
        			}
        		}
        		$evaluated = \backend\models\SemesterEvaluation2Detail::find()
				        	->where([
				        		'semev2det_semev_relId'=>$exst->id, 
				        		'semev2det_completed'=>1
				        	])
				        	->all();
				$eUsers = [];
				if ($evaluated) {
					foreach ($evaluated as $key) {
						array_push($eUsers, $key['semev2det_evaluator_relId']);
					}
				}
				$evaluatedUsers = \backend\models\User::find()->where(['id'=>$eUsers])->all();

				$user = \backend\models\User::find()->where(['id'=>$tUsers])->all();
        		return [$evaluatedUsers,$user,$evalType->id] ;


        	}
        	else{
        		return 'cal..';
        	}
   		}
    }

}
