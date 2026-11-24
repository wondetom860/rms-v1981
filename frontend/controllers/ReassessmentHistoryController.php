<?php

namespace frontend\controllers;

use Yii;
use backend\models\ReassessmentHistory;
use backend\models\ReassessmentHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ReassessmentHistoryController implements the CRUD actions for ReassessmentHistory model.
 */
class ReassessmentHistoryController extends Controller
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
            'access' => [
               'class' => \yii\filters\AccessControl::class,
               // We will override the default rule config with the new AccessRule class
               'ruleConfig' => [
                   'class' => \yii\filters\AccessRule::class,
               ],
               'rules' => [
                   [
                       'actions' => [],
                       'allow' => true,
                       'roles' => ['@',],//require login to access all actions/functions under this class
                   ],
               ]
           ]
        ];
    } 

    /**
     * Lists all ReassessmentHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReassessmentHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ReassessmentHistory model.
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
     * Creates a new ReassessmentHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ReassessmentHistory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }
    public function actionUpdateMark(){
        $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : die('INAVLED REQUEST');
        $afterMark = isset($_POST['afterMark']) && !empty($_POST['afterMark']) ? $_POST['afterMark'] : die('INAVLED REQUEST');
        $model = $this->findModel($id);
        if (Yii::$app->user->can('DepartmentHead')) {
            $transaction = Yii::$app->db->beginTransaction();
            // var_dump($model);die();
            if ($model->ras_registrarApprove_status == 1) {
                // incase HoD can insert mark in form of 25/50
                $model->ras_afterMark = $afterMark;
                $expd = explode('/', $model->ras_afterMark);
                $model->ras_afterMark = is_array($expd) ? $expd[0] : $model->ras_afterMark;
                $model->ras_updated_by = Yii::$app->user->identity->id;
                $model->ras_updated_at = date("Y-m-d h:i:s");

                // var_dump($model->ras_afterMark);die();
                if ($model->save()) {
                    $rep = $model->propagateOperation();
                    if ( is_bool($rep) && $rep == true) {
                        $transaction->commit();
                        Yii::$app->session->setFlash('type','success');
                        Yii::$app->session->setFlash('message','Mark Submission seccessful.');
                        return $this->redirect('index');
                    }else{
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('type','error');
                        Yii::$app->session->setFlash('message',$rep);
                        return $this->redirect('index');
                    }
                }else{
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('type','error');
                    Yii::$app->session->setFlash('message',json_encode($model->getErrors()));
                    return $this->redirect('index');
                }
            }else{
                $transaction->rollBack();
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message','Request NOT approved by Registrar, Please contact.');
                return $this->redirect('index');
            }
        }
    }

    public function actionChangeStatus(){
        $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : die('INAVLED REQUEST');
        $operation = isset($_POST['operation']) && !empty($_POST['operation']) ? intval($_POST['operation']) : die('INAVLED REQUEST');

        if (Yii::$app->user->can('Registrar')) {
            // operation[
            //     1   =>approve
            //     2   =>cancel approval
            //     3   =>lock process
            // ]

            $model = $this->findModel($id);
            $ap = $operation == 1 ? 1 : ($operation == 2 ? 0 : 3); 
            $model->ras_registrarApprove_status = $ap;
            $model->ras_updated_by = Yii::$app->user->identity->id;
            $model->ras_updated_at = date("Y-m-d h:i:s");

            if ($model->update()) {
                if ($ap == 3) {// it is lock operation
                    $srd2 = $model->rasSrd2Rel;
                    $srd2->srd2_gradeLocked = 1;
                    $srd2->srd2_gradeApproved = 1;
                    $srd2->update();
                }
                Yii::$app->session->setFlash('type','success');
                Yii::$app->session->setFlash('message','Status Change seccessful.');
                return $this->redirect('index');
            }else{
                return json_encode($model->getErrors());
            }
        }
    }
    /**
     * Updates an existing ReassessmentHistory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->user->can('DepartmentHead') && $model->hisDeptCourse()) {
            if ($model->ras_registrarApprove_status != 1) {
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message','You can NOT process un-approved re-assessment request');
                return $this->redirect('index');
            }
            if (Yii::$app->request->isAjax || null != Yii::$app->request->post()) {
                if($model->load(Yii::$app->request->post())){
                    $transaction = Yii::$app->db->beginTransaction();
                    var_dump($model);die(); 
                    if ($model->ras_registrarApprove_status == 1) {
                        // incase HoD can insert mark in form of 25/50
                        $expd = explode('/', $model->ras_afterMark);
                        $model->ras_afterMark = is_array($expd) ? $expd[0] : $model->ras_afterMark;
                        $model->ras_updated_by = Yii::$app->user->identity->id;
                        $model->ras_updated_at = date("Y-m-d h:i:s");

                        var_dump($model->ras_afterMark);die();
                        if ($model->save()) {
                            $rep = $model->propagateOperation();
                            if ( is_bool($rep) && $rep == true) {
                                $transaction->commit();
                                Yii::$app->session->setFlash('type','success');
                                Yii::$app->session->setFlash('message','Mark Submission seccessful.');
                                return $this->redirect('index');
                            }else{
                                $transaction->rollBack();
                                Yii::$app->session->setFlash('type','error');
                                Yii::$app->session->setFlash('message',$rep);
                                return $this->redirect('index');
                            }
                        }else{
                            $transaction->rollBack();
                            Yii::$app->session->setFlash('type','error');
                            Yii::$app->session->setFlash('message',json_encode($model->getErrors()));
                            return $this->redirect('index');
                        }
                    }else{
                        $transaction->rollBack();
                        Yii::$app->session->setFlash('type','error');
                        Yii::$app->session->setFlash('message','Request NOT approved by Registrar, Please contact.');
                        return $this->redirect('index');
                    }
                }
                return $this->renderAjax('update', [
                    'model' => $model,
                ]);
            }
        }

        if ($model->ras_status != NULL) {
            Yii::$app->session->setFlash('type','error');
            Yii::$app->session->setFlash('message','You can NOT update approved re-assessment request');
            return $this->redirect('index');
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect('index');
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ReassessmentHistory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->ras_status != NULL) {
            Yii::$app->session->setFlash('type','error');
            Yii::$app->session->setFlash('message','You can NOT delete approved re-assessment request');
            return $this->redirect('index');
        }

        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the ReassessmentHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ReassessmentHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ReassessmentHistory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
