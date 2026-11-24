<?php

namespace frontend\controllers;

use Yii;
use backend\models\SelfSponsoredPaymentHistory;
use backend\models\SelfSponsoredPaymentHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \common\components\CommonControllerFunctions as CCF;

/**
 * SelfSponsoredPaymentHistoryController implements the CRUD actions for SelfSponsoredPaymentHistory model.
 */
class SelfSponsoredPaymentHistoryController extends Controller
{

    public $acadYearId;
    public $semId;
    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionApprovepayment(){
        $reqId = isset($_POST['reqId']) && !empty($_POST['reqId'])?$_POST['reqId']:die('Uknown request.');
        $stdId = isset($_POST['stdId']) && !empty($_POST['stdId'])?$_POST['stdId']:die('Uknown Student.');
        $actionId = isset($_POST['actionId']) && !empty($_POST['actionId'])?$_POST['actionId']:die('Uknown Approval.');

        $actionId = $actionId == 2?0:$actionId;

        $exusts = SelfSponsoredPaymentHistory::findOne([
            'id'=>$reqId,
            'ssph_std_relId'=>$stdId,
            'ssph_approved'=>NULL
        ]);

        if ($exusts && count($exusts) > 0) {
            
            // Update query here
            $result = Yii::$app->db->createCommand()
                        ->update('self_sponsored_payment_history',
                            [
                                'ssph_approved'=>$actionId,
                                'ssph_approved_by'=>Yii::$app->user->identity->id,
                                'ssph_approved_at'=>date('Y-m-d h:i:s'),
                             ],
                            'id='.$reqId)  
                        ->execute(); 
            echo $result;
            die();

        }else{
            echo "<p class='bg-danger'>No such request, any modification made to requsset?</p>";die();
        }
    }

    public function actionGetapprovedrequests(){
        $model = SelfSponsoredPaymentHistory::find()
        ->where([
            'IS NOT','ssph_approved',NULL,
        ])->andWhere([
            'ssph_approved_by'=>Yii::$app->user->identity->id,
        ])->all();
        
        if ($model && count($model) > 0) {
            
            echo "<table style='font-size:10pt;' class='table table-striped table-condensed table-xs table-bordered'>";
            echo "<caption><p class='bg-success'> <i class='bg-danger'>". count($model) ."</i> Approved Request(s).</p></caption>";
            echo "<thead>
            <th>RegId</th>
            <th>Full Name</th>
            <th>Reason</th>
            <th>Sem.</th>
            <th>Acad. Yr</th>
            <th>Txn #</th>
            <th>Status</th>
            </thead><tbody>";
        foreach ($model as $reports) {
            $rsn = !is_null($reports['ssph_pymntRsn_relId'])?\backend\models\PaymentReason::findOne(['id'=>$reports['ssph_pymntRsn_relId']])->pr_desc:'N/A';
            $student = \backend\models\Student::findOne(['id'=>$reports->ssph_std_relId]);
            $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
            $reqStatus = is_null($reports['ssph_approved'])?'Pending':($reports['ssph_approved'] == 1?'Approved':'Rejected');
            echo "<tr>
            <td>". $student->std_regId ."</td>
            <td>". $usr->first_name.' '. $usr->middle_name ."</td>
            <td>". $rsn ."</td>
            <td>". \backend\models\Semester::findOne(['id'=>$reports['ssph_sem_relId']])->semester_name ."</td>
            <td>". \backend\models\SchoolYear::findOne(['id'=>$reports['ssph_acadYear_relId']])->sch_year_desc ."</td>
            <td>". $reports['ssph_txn_num'] ."</td>
            <td>". $reqStatus ."</td>
            </tr>";
        }
        echo "</tbody></table>";
        }else{
            echo "<p class='bg-danger'>You have'nt approved any payment request yet.</p>";
        }
        die();
    }

    public function actionGetrequests(){
        $model = SelfSponsoredPaymentHistory::find()->where([
            'ssph_approved'=>NULL,
        ])
        ->all();
        if ($model && count($model) > 0) {
            
            echo "<table  class='table table-striped table-condensed table-xs table-bordered'>";
            echo "<caption><p class='bg-success'> <i class='bg-danger'>". count($model) ."</i> Request(s).</p></caption>";
            echo "<thead>
            <th>RegId</th>
            <th>Full Name</th>
            <th>Reason</th>
            <th>Sem.</th>
            <th>Acad. Yr</th>
            <th>Txn #</th>
            <th>Opt.</th>
            </thead><tbody>";
        foreach ($model as $reports) {
            $student = \backend\models\Student::findOne(['id'=>$reports->ssph_std_relId]);
            $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);

            echo "<tr>
            <td>". $student->std_regId ."</td>
            <td>". $usr->first_name.' '. $usr->middle_name ."</td>
            <td>". \backend\models\PaymentReason::findOne(['id'=>$reports['ssph_pymntRsn_relId']])->pr_desc ."</td>
            <td>". \backend\models\Semester::findOne(['id'=>$reports['ssph_sem_relId']])->semester_name ."</td>
            <td>". \backend\models\SchoolYear::findOne(['id'=>$reports['ssph_acadYear_relId']])->sch_year_desc ."</td>
            <td>". $reports['ssph_txn_num'] ."</td>
            <td><select title='select action' onchange='changeStatus(". $student['id'] .",$(this).val(),". $reports['id'].")'>
                    <option value=' '> </option>
                    <option value='1'>Approve</option>
                    <option value='0'>Disapprove</option>
                </select></td>
            </tr>";
        }
        echo "</tbody></table>";
        }else{
            echo "<p class='bg-danger'>No pending payment approval request.</p>";
        }
        die();
    }

    /**
     * Lists all SelfSponsoredPaymentHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SelfSponsoredPaymentHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SelfSponsoredPaymentHistory model.
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
     * Creates a new SelfSponsoredPaymentHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SelfSponsoredPaymentHistory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SelfSponsoredPaymentHistory model.
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
     * Deletes an existing SelfSponsoredPaymentHistory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SelfSponsoredPaymentHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SelfSponsoredPaymentHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SelfSponsoredPaymentHistory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
