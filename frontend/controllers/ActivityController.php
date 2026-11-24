<?php

namespace frontend\controllers;
use \common\components\CommonControllerFunctions as CCF;
use yii\helpers\Html;
use yii\helpers\Url;

use backend\models\AcademicCalander;
use yii;

class ActivityController extends \yii\web\Controller
{

	public $acadYearId;
    public $semId;
    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

	public function actionSomeThing(){
		#do something here.
	}

    public function actionDashbordImages(){
        // $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

        $mainpath = 'uploads/recent_news_img/';
        $files=\yii\helpers\FileHelper::findFiles($mainpath,
            [
                'only'=>['*.PNG','*.png','*.JPG','*.jpg'],
                'recursive'=>FALSE
            ]);

        $max = 3;
        $count = 0;
        $filesContainer = [];
        if (isset($files[0])) {
            foreach ($files as $index => $file) {
                $nameFicheiro = substr($file, strrpos($file, '/') + 1);
                $count = $count+1;
                $filesContainer[] = '/'.$mainpath.$nameFicheiro;
                if ($count == $max) {
                    break;
                }
            }
            echo json_encode($filesContainer);die();
        } else {
            echo 0; die();
        }


        // $files = [];
        // $rii = \yii\helpers\FileHelper::findFiles('uploads\recent_news_img',['only'=>['*.PNG','*.JPG']]);

        // echo "<pre>";
        // var_dump($rii)
        // ;die();
        // foreach ($rii as $file)
        //     if (!$file->isDir())
        //         $files[] = $file->getPathname();

        // echo json_encode($files);die();
    }
    public function actionGuestScheduleList(){
        if (Yii::$app->user->isGuest) {
            $date = strtotime(date('Y-m-d'));
            $endDate = strtotime("+7 day",$date);
            // $todau = strtotime(date(''))

            // echo date('Y-m-d',$endDate); die();
            $edate = date('Y-m-d h:i:s',$endDate);
            $sceds = \backend\models\AcademicCalander::find()
                ->where(['OR',['>=',
                                    'acad_starttime',date('Y-m-d h:i:s')],
                                ['>=',
                                    'acad_finishtime',date('Y-m-d')]
                ])
                ->andWhere(['OR',['<=',
                                    'acad_starttime',date('Y-m-d h:i:s',$endDate)],
                            ['<=',
                                    'acad_finishtime',date('Y-m-d',$endDate)]
                ])
                ->groupBy('acad_action_relId')
                ->all();
            // $sceds2 = new (Yii::$app->db->Query())
            //     ->select()
            //     ->from('academic_calander')
            //     ->where('acad_starttime >= now() OR acad_starttime <= {$edate} OR acad_finishtime >= now() OR acad_finishtime <= {}$edate')
            //     ->all();

            // var_dump($sceds2); die();

            if ($sceds && count($sceds) > 0) {
                foreach ($sceds as $key) {
                    $not = $this->getCont($key->acad_action_relId);
                    $name = empty($not)?'Uknown':$not->aca_name;
                    $link = empty($not)?'':'/'.$not->aca_link.'/';

                    echo "<div class='alert alert-warning' style='text-align:left; font-size:9pt;'>
                                 <p>
                                     ". $name ."
                                 </p>
                                 <span class='' style='font-style:italic; font-size:7pt;'> Start: 
                                     ". date_format(date_create($key->acad_starttime),'D, M d, Y @ h:i A') ."
                                 </span><br>
                                 <span class='' style='font-style:italic; font-size:7pt;'> Ends: 
                                     ". date_format(date_create($key->acad_finishtime),'D, M d, Y @ h:i A') ."
                                 </span>
                             </div>";
                             continue;
                    // if (is_null($not->aca_link)) {
                        
                    // }else{
                    //     echo "<a href='". $link ."'>
                    //          <div class='alert alert-warning' style='cursor:pointer; text-align:left; font-size:11pt;'>
                    //              <p>
                    //                  ". $name ."
                    //              </p>
                    //              <span class='' style='font-style:italic; font-size:8pt;'> Start On: 
                    //                  ". date_format(date_create($key->acad_starttime),'D, M d, Y') ."
                    //              </span><br>
                    //              <span class='' style='font-style:italic; font-size:8pt;'> Ends On: 
                    //                  ". date_format(date_create($key->acad_finishtime),'D, M d, Y') ."
                    //              </span>
                    //          </div>
                    //      </a>";
                    // }
                    }
                    
                    // if ($date == date_format($key->acad_starttime,'Y-m-d')) {
                    //     echo "<div class='alert alert-warning'></div>";
                    // }
                }else{
                    echo "<div class='alert alert-danger'>Schedule Not available</div>";die();
                }
            }
    }

	protected function currentStudentGroup($stdObject){
      $this->__constructor();
      $gset = \backend\models\WGroupSetup::find()
      
        ->where([
          'gs_acadYear_relId'=>$this->acadYearId,
          'gs_sem_relId'=>$this->semId,
          'gs_adm_relId'=>$stdObject->std_admission_relId,
          'gs_prog_relId'=>$stdObject->std_prog_relId,
        ])
        ->innerJoin('w_group_assignment','w_group_setup.id = w_group_assignment.gas_gset_relId')
        ->andWhere(['w_group_assignment.gas_std_relId'=>$stdObject->id])
            ->with('wGroupAssignments')
        ->one();
      return $gset;
    }


    public function actionActive()
    {

    	if (Yii::$app->user->can('Student')) {
    		$Student = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);
    		$gset = $this->currentStudentGroup($Student);

    		if ($gset && count($gset) > 0) {
    			$actvs = AcademicCalander::find()
    			->where(['OR',['acad_adm_relId'=>$gset->gs_adm_relId],['acad_adm_relId'=>NULL]])
                ->andWhere(['OR',['acad_prog_relId'=>$gset->gs_prog_relId],['acad_prog_relId'=>NULL]])
                ->andWhere(['OR',['acad_clslvl_relId'=>$gset->gs_prog_relId],['acad_clslvl_relId'=>NULL]])
		    	->andWhere(['<=','acad_starttime',date('Y-m-d h:i:s')])
		    	->andWhere(['>=','acad_finishtime',date('Y-m-d h:i:s')])
                ->groupBy('acad_action_relId')
				->all();

			if (empty($actvs)) {
				echo "<p class='bg-warning'>No active Activity found.</p>";
			}else{
				// echo "<table class='table table-condensed table-sm table-striped'>";
				// foreach ($actvs as $actv) {

				// 	$not = $this->getCont($actv->acad_action_relId);
				// 	$name = empty($not)?'Uknown':$not->aca_name;
				// 	$link = empty($not)?'#':$not->aca_link;

				// 	echo "<tr><td><a href='/". $link ."/'>
				// 			<div class='container-fluid bg-info' style='cursor:pointer'>
				// 				<p>
				// 					". $name ."
				// 				</p>
				// 				<span class='bg-info' style='font-style:italic; float:right;'> Ends On: 
				// 					". date_format(date_create($actv->acad_finishtime),'D, M d, Y') ."
				// 				</span>
    //                             <img src='/logo/red.gif' height='20' width='20' alt='' />
				// 			</div>
				// 		</a></td></tr>
				// 	";
				// }
				// echo "</table>";
			}
		}
	}else{

	}
    	
    }

    // returns the Action of selected activity.
    protected function getCont($id){
    	$not = \backend\models\AcademicCalanderAction::findOne(['id'=>$id]);
    	return $not;
    }

}
