<?php

namespace frontend\controllers;

use Yii;
use frontend\models\SemesterRegistration2Detail;
use frontend\models\SemesterRegistration2DetailSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;

/**
 * SemesterRegistration2DetailController implements the CRUD actions for SemesterRegistration2Detail model.
 */
class SemesterRegistration2DetailController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all SemesterRegistration2Detail models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SemesterRegistration2DetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SemesterRegistration2Detail model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new SemesterRegistration2Detail model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SemesterRegistration2Detail();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionChangeApproveGradeStatusSingle(){//works for all single grade change calls.
        $srd2Id = isset($_POST['srd2Id']) && !empty($_POST['srd2Id']) ? intval($_POST['srd2Id']) : die('InvalId request');

        $option = isset($_POST['option']) && !empty($_POST['option']) ? intval($_POST['option']) : die('Invalid request, STATS_CHANGE_OPTION_IS_NOT_KNOWN');

        $option = $option == 1? 1:null;

        $model=$this->findModel($srd2Id);
       
        if (Yii::$app->user->can('DepartmentHead')) {
            if (($model->srd2_gradeApproved == $option) || ($model->srd2_gradeLocked == 1)) {
                return "No change made on grade approval status";
            }else{
                $model->srd2_gradeApproved = $option;
                $model->srd2_gradeLocked = $option == 1 ? 1 : $model->srd2_gradeLocked;
                $model->srd2_gradeApproved_by = $option == 1 ? Yii::$app->user->identity->id : NULL;
                $model->srd2_gradeApproved_at = $option == 1 ? date('Y-m-d h:i:s') : NULL;
            }
        }else if(Yii::$app->user->can('Registrar')){// && ($model->srd2_gradeApproved == 1)
            $option = $option == 1? 1:0;
            $model->srd2_gradeLocked= $option; 
        }

        if ($model->update()) {
            // $model = new SemesterRegistration2Detail();
            $model->getSemregRecord($model->srd2SemregRel->semreg_std_relId,$model->srd2GroupRel->gs_clslvl_relId,$model->srd2SemregRel->semreg_semester_relId,$model->srd2GroupRel,NULL,NULL);
            return 1;
        }else{
            return json_encode($model->getErrors());
        }
        
    }

    public function actionChangeApproveGradeStatusBatch(){//works for all batch grade approval change calls.
        $groupId = isset($_POST['groupId']) && !empty($_POST['groupId']) ? intval($_POST['groupId']) : die('InvalId request');
        $courseId = isset($_POST['courseId']) && !empty($_POST['courseId']) ? intval($_POST['courseId']) : die('InvalId request');

        $option = isset($_POST['option']) && !empty($_POST['option']) ? intval($_POST['option']) : die('Invalid request, STATS_CHANGE_OPTION_IS_NOT_KNOWN');

        $option = $option == 1? 1:null;
        $lkd = $option == 1 ? 1 : 0;
        $result = 0;
        if (Yii::$app->user->can('DepartmentHead')) {
            $result = Yii::$app->db->createCommand()
                ->update(
                    SemesterRegistration2Detail::tableName(),
                    [
                        'srd2_gradeApproved' => $option,
                        'srd2_gradeApproved_by'=>Yii::$app->user->identity->id,
                        'srd2_gradeApproved_at'=>date('Y-m-d h:i:s'),
                        'srd2_gradeLocked' => $lkd
                    ],
                    ('srd2_group_relId='.$groupId.' AND srd2_course_relId='.$courseId.' AND srd2_gradeLocked <> 1 AND srd2_grade_relId IS NOT NULL')
                )
                ->execute();
        }else if(Yii::$app->user->can('Registrar')){
            $option = $option == 1? 1:0;
            $result = Yii::$app->db->createCommand()
                ->update(
                    SemesterRegistration2Detail::tableName(),
                    [
                        'srd2_gradeLocked' => $option,
                    ],
                    ('srd2_group_relId='.$groupId.' AND srd2_course_relId='.$courseId.' AND srd2_gradeApproved = 1')
                )
                ->execute();
        }
        if ($result) {
            $model = new SemesterRegistration2Detail();
            $model->updateStatus($groupId);
            return 1;
        }else{
            return 'Grade approve status failed';
        }
        
    }

    /**
     * Updates an existing SemesterRegistration2Detail model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing SemesterRegistration2Detail model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (Yii::$app->user->can('Registrar')) {
            $this->findModel($id)->delete();
            return 1;
        }else{
            throw new ForbiddenHttpException("Error Processing Request", 1);
            
        }
    }

    /**
     * Finds the SemesterRegistration2Detail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SemesterRegistration2Detail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SemesterRegistration2Detail::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
