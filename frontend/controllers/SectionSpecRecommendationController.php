<?php

namespace frontend\controllers;
use \backend\models\WGroupSetup;
use \backend\models\WGroupAssignment;
use \backend\models\SecSpecRecomend;
use yii;
use \common\components\CommonControllerFunctions as CCF;



/**
 * This is the model class for table "sec_spec_recomend".
 *
 * @property integer $id
 * @property integer $ssr_acadYear_relId
 * @property integer $ssr_sem_relId
 * @property integer $ssr_adm_relId
 * @property integer $ssr_prog_relId
 * @property integer $ssr_dept_relId
 * @property integer $ssr_clslvl_relId
 * @property integer $ssr_spec_relId
 * @property string $ssr_rec_spec
 * @property string $ssr_rec_section
 * @property integer $ssr_created_by
 * @property string $ssr_created_at
 * @property integer $ssr_updated_by
 * @property string $ssr_updated_at
 * @property string $ssr_stdList
 *
 * @property SchoolYear $ssrAcadYearRel
 * @property Admission $ssrAdmRel
 * @property ClassLevel $ssrClslvlRel
 * @property User $ssrCreatedBy
 * @property Department $ssrDeptRel
 * @property Program $ssrProgRel
 * @property Semester $ssrSemRel
 * @property Specialization $ssrSpecRel
 * @property User $ssrUpdatedBy
 */
class SectionSpecRecommendationController extends \yii\web\Controller
{

	public $acadYearId;
    public $semId;

    protected function __constructor() {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    public function actionIndex()
    {
    	$this->__constructor();
    		$modelg = new WGroupSetup();
    		$model = new WGroupAssignment();
    		$modelm = new SecSpecRecomend();
    	if (Yii::$app->user->can('DepartmentHead')) {
    		return $this->render('index_deptHead',[
    			'model'=>$model,
    			'modelg'=>$modelg,
    			'modelm'=>$modelm,
    		]);
    	}
        return $this->render('index',[
			'model'=>$model,
			'modelg'=>$modelg,
			'modelm'=>$modelm,
		]);
    }

    public function actionLoadRecommendationHistory(){
    	if (Yii::$app->user->can('DepartmentHead')) {
    		$table = "";

    		$deptId = Yii::$app->request->post('deptId');
	        $progId = Yii::$app->request->post('progId');
	        $clslvl = Yii::$app->request->post('clslvlId');
	        $admId = Yii::$app->request->post('admId');
	        $specId =Yii::$app->request->post('specId');

	        $specId = $specId==0?NULL:$specId;

	        $entryType = Yii::$app->request->post('entryId');
	        $recType = Yii::$app->request->post('recType');
	        $recType == '0'?0:intval($recType);
	        // 0=>specialization
	        // 1=>section / group
	        $prevSemId = Yii::$app->request->post('prevSemId');
	        $prevAcadYearId = Yii::$app->request->post('prevAcadYearId');

    		$this->__constructor();
    		$recs = SecSpecRecomend::find()->where([
	    		'ssr_acadYear_relId'=>$this->acadYearId,
	    		'ssr_sem_relId'=>$this->semId,
	    		'ssr_dept_relId'=>$deptId,
	    		'ssr_adm_relId'=>$admId,
	    		'ssr_prog_relId'=>$progId,
	    		'ssr_clslvl_relId'=>$clslvl,
	    		'ssr_created_by'=>Yii::$app->user->identity->id
	    		])->all();
    		if ($recs && count($recs) > 0) {
    			$table .= "";
    			$table .= "<table class='table table-striped table-condensed table-sm table-bordered' style='font-size:9pt'>";
	            $table .= "<caption>Your Recommendations</caption>";
	            $table .= "<thead style='background-color:#8D0;'>
		            <th>#</th>
		            <th>Reg. ID.</th>
		            <th>Student Name</th>
		            <th title='recommended'>Group / Section / Specialization</th>
		            </thead><tbody>";
		         $count = 0;
    			foreach ($recs as $key) {
    				$stds = json_decode($key->ssr_stdList);

    				$table .= "<tr style='text-align:center; background-color: #850; color:white;'><td colspan=4>Recommendation ".++$count."</td></tr>";
    				$count1 = 0;
    				foreach ($stds as $std) {
    					$rec = $key->ssr_rec_section == NULL?\backend\models\Specialization::findOne($key->ssr_rec_spec)->spec_name:$key->ssr_rec_section;
    					$std = \backend\models\Student::findOne($std);
    					$usr = \backend\models\User::findOne($std->std_user_relId);
    					$table .= "<tr>
			                <td>".++$count1."</td>
			                <td>". $std['std_regId'] ."</td>
			                <td>". $usr->first_name .' '.$usr->middle_name ."</td>
			                <td>{$rec}</td>
		                </tr>";
    				}
    			}
    			$table .="</tbody></table>";
    			echo $table;
    			die();
    		}else{
    			echo "<p class='bg-danger'>No recommendation Made yet.</p>";die();
    		}
    	}else if(Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')){
    			$table = "";

	    		$deptId = Yii::$app->request->post('deptId');
		        $progId = Yii::$app->request->post('progId');
		        $clslvl = Yii::$app->request->post('clslvlId');
		        $admId = Yii::$app->request->post('admId');
		        $specId =Yii::$app->request->post('specId');

		        $specId = $specId==0?NULL:$specId;

		        // $entryType = Yii::$app->request->post('entryId');
		        $recType = Yii::$app->request->post('recType');
		        $recType == '0'?0:intval($recType);
		        // 0=>specialization
		        // 1=>section / group
		        $prevSemId = Yii::$app->request->post('prevSemId');
		        $prevAcadYearId = Yii::$app->request->post('prevAcadYearId');

	    		$this->__constructor();
	    		$recs = NULL;
	    		if ($recType == 0) {
	    			$recs = SecSpecRecomend::find()->where([
			    		'ssr_acadYear_relId'=>$this->acadYearId,
			    		'ssr_sem_relId'=>$this->semId,
			    		'ssr_dept_relId'=>$deptId,
			    		'ssr_adm_relId'=>$admId,
			    		'ssr_prog_relId'=>$progId,
			    		'ssr_clslvl_relId'=>$clslvl,
			    		])->andWhere(['IS NOT','ssr_rec_spec',NULL])->all();
	    		}else{
	    			$recs = SecSpecRecomend::find()->where([
			    		'ssr_acadYear_relId'=>$this->acadYearId,
			    		'ssr_sem_relId'=>$this->semId,
			    		'ssr_dept_relId'=>$deptId,
			    		'ssr_adm_relId'=>$admId,
			    		'ssr_prog_relId'=>$progId,
			    		'ssr_clslvl_relId'=>$clslvl,
			    		])->andWhere(['IS NOT','ssr_rec_section',NULL])->all();
	    		}
	    		if ($recs && count($recs) > 0) {
	    			$table .= "";
	    			$user = $recs[0]['ssr_created_by'];
	    			$user= \backend\models\User::findOne($user);
	    			$inst = \backend\models\Instructor::find()->where(['inst_user_relId'=>$user->id])->one();
	    			$dept = \backend\models\Department::findOne($inst->inst_dep_relId)->dept_name;
	    			$name = $user->first_name .' '.$user->middle_name[0];
	    			$table .= "<table class='table table-striped table-condensed table-sm table-bordered' style='font-size:9pt'>";
		            $table .= "<caption style='text-align:center;'><b>Recomendations made by {$name}, HoD of {$dept}</b></caption>";
		            $table .= "<thead style='background-color:#8D0;'>
			            <th>#</th>
			            <th>Reg. ID.</th>
			            <th>Student Name</th>
			            <th title='recommended'>Group / Section / Specialization</th>
			            </thead><tbody>";
			         $count = 0;
	    			foreach ($recs as $key) {
	    				$stds = json_decode($key->ssr_stdList);

	    				$table .= "<tr style='text-align:center; background-color: #850; color:white;'><td colspan=4>Recommendation ".++$count."</td></tr>";
	    				$count1 = 0;
	    				foreach ($stds as $std) {
	    					$rec = $key->ssr_rec_section == NULL?\backend\models\Specialization::findOne($key->ssr_rec_spec)->spec_name:$key->ssr_rec_section;
	    					$std = \backend\models\Student::findOne($std);
	    					$usr = \backend\models\User::findOne($std->std_user_relId);
	    					$table .= "<tr>
				                <td>".++$count1."</td>
				                <td>". $std['std_regId'] ."</td>
				                <td>". $usr->first_name .' '.$usr->middle_name ."</td>
				                <td>{$rec}</td>
			                </tr>";
	    				}
	    			}
	    			$table .="</tbody></table>";
	    			echo $table;
	    			die();
	    		}else{
	    			echo "<p class='bg-danger'>No recommendation Made yet.</p>";die();
	    		}
    	}else{
    		echo "<p class='bg-danger'>Invalid Access.</p>";die();
    	}    	
    }

    public function actionCreate(){
    	if (Yii::$app->user->can('DepartmentHead')) {
    		$this->__constructor();
    		$stdList = isset($_POST['stdList']) && !empty($_POST['stdList'])?$_POST['stdList']:die('Student list empty.');
	    	// $stdList = isset($_POST['stdList']) && !empty($_POST['stdList'])?$_POST['stdList']:die('Student list empty.');

	    	$deptId = Yii::$app->request->post('deptId');
	        $progId = Yii::$app->request->post('progId');
	        $clslvl = Yii::$app->request->post('clslvlId');
	        $admId = Yii::$app->request->post('admId');
	        $specId =Yii::$app->request->post('specId');

	        $specId = $specId==0?NULL:$specId;

	        $entryType = Yii::$app->request->post('entryId');
	        $recType = Yii::$app->request->post('recType');
	        $recType == '0'?0:intval($recType);
	        // 0=>specialization
	        // 1=>section / group
	        $prevSemId = Yii::$app->request->post('prevSemId');
	        $prevAcadYearId = Yii::$app->request->post('prevAcadYearId');

	        $model = new SecSpecRecomend();

	        $model->ssr_acadYear_relId = $this->acadYearId;
	        $model->ssr_sem_relId = $this->semId;
	        $model->ssr_adm_relId = $admId;
	        $model->ssr_prog_relId = $progId;
	        $model->ssr_dept_relId = $deptId;
	        $model->ssr_clslvl_relId = $clslvl;
	        $model->ssr_spec_relId = $specId;
	        $model->ssr_created_by = Yii::$app->user->identity->id;
	        $model->ssr_created_at = date('Y-m-d h:i:s');
	        $model->ssr_stdList = $stdList;

	        $transaction = Yii::$app->db->beginTransaction();
	        $exists = SecSpecRecomend::find()
		        ->where([
		        	'ssr_acadYear_relId'=>$this->acadYearId,
		    		'ssr_sem_relId'=>$this->semId,
		    		'ssr_dept_relId'=>$deptId,
		    		'ssr_adm_relId'=>$admId,
		    		'ssr_prog_relId'=>$progId,
		    		'ssr_clslvl_relId'=>$clslvl,
		        	]);
	        $model->recommend = $recType;
	        if ($recType == 0) {
	        	$model->ssr_rec_section = NULL;
	        	$model->ssr_rec_spec = intval($_POST['gid']);
	        	$exists = $exists->andWhere(['ssr_rec_spec'=>$model->ssr_rec_spec]);
	        }else{
	        	$model->ssr_rec_spec = NULL;
	        	$model->ssr_rec_section = $_POST['gid'];
	        	$exists = $exists->andWhere(['ssr_rec_section'=>$model->ssr_rec_section]);
	        }

	        // check if similar copy exists...
	        $exists = $exists->one();

	        if ($exists && count($exists) > 0) {

	        	$stdList2 = [];
	        	foreach (json_decode($exists->ssr_stdList) as $keysdd) {
	        		$stdList2[] = $keysdd;
	        	}
	        	foreach (json_decode($stdList) as $keysd) {
	        		$stdList2[] = $keysd;
	        	}

	        	$rezsult = Yii::$app->db->createCommand()
	        		->update('sec_spec_recomend',
	        			[
	        				'ssr_stdList'=>json_encode($stdList2),
	        				'ssr_updated_at'=>date('Y-m-d h:i:s'),
	        				'ssr_updated_by'=>Yii::$app->user->identity->id,
	        			],
	        			['id'=>$exists->id])
	        		->execute();

	        	if ($rezsult) {
	        		$transaction->commit();
	        		echo 1;
	        		die();
	        	}else{
	        		echo "<p class='bg-danger'>Operation Failed.</p>";die();
	        	}
	        }else{
	        	if ($model->save()) {
			        	$transaction->commit();
			        	echo 1;
			        	die();
			        }else{
			        	var_dump($model->errors);die();
			        }
	        }
    	}else{
    		echo "<p class='bg-danger'>Invalid access!.</p>";die();
    	}
    }
}
