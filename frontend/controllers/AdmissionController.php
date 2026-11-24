<?php
namespace frontend\controllers;
use Yii;
use backend\models\Admission;
use backend\models\AdmissionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdmissionController implements the CRUD actions for Admission model.
 */
class AdmissionController extends Controller
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
                       'actions' => ['index','create', 'update', 'delete','clslvllists'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
                   [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                       'actions' => ['clslvllists','clslvllistss','export-pdf'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => [],
                   ],
               ]
           ]
        ];
    }

    public function actionExportPdf(){
        return $this->render('export_pdf');
    }

    public function actionGetad(){
        $ads = Admission::find()->all();

        if ($ads) {
            foreach ($ads as $ad) {
                echo "<option value='". $ad['id'] ."'>". $ad['admission_name'] ."</option>";
            }
             echo "<option value='0' title='Select this if pre-requisiste applies for all types of admissions'>All</option>";
        }
    }

    // returns the list of admissions
    public function actionGetAdmission(){
        $adms = Admission::find()->all();

        if (!empty($adms)) {
            echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
            echo "<caption><p class='bg-success'> <i class='bg-danger'>". count($adms) ."</i> Admission(s) Available.</p></caption>";
            echo "<thead>
            <th>#</th>
            <th>Admission Name</th>
            </thead><tbody>";
            $count = 0;
            foreach ($adms as $adm) {
               echo "<tr>
                    <td>".++$count."</td>
                    <td>". $adm['admission_name']."</td>
               </tr>";
            }
            echo "</tbody></table>";
        }else{
            echo "<p class='bg-warning'>No admission available yet.</p>";
        }
    }

    /**
     * Lists all Admission models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AdmissionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new Admission();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model'=>$model,
        ]);
    }

    // Returns back list of programs under the selected admission
    public function actionProgramlists(){
        if (isset($_POST['admId']) && !empty($_POST['admId'])) {
            $admId = $_POST['admId'];

            $progs = \backend\models\Program::find()->where(['id'=>$admId])->all();

            if (!empty($progs)) {
                foreach ($progs as $prog) {
                    echo "<option value='". $prog->id ."'>". $prog->program_name ."</option>";
                }
            }else{
                echo "No programs found in this admission.";
            }
        }else{
            echo "Admission Not known.";
        }
    }

     //returns back list of programs in the selected admission as a dropdown options.
    public function actionListAdmission(){

            $admCount = \backend\models\Admission::find()->count();

            if ($admCount >0) {
                $adms = \backend\models\Program::find()->all();
                foreach ($adms as $prog) {
                    echo "<option value='". $prog->id ."'>". $prog->admission_name ."</option>";
                }
            }else{
                echo "<option> - </option>";
            }
    }

    /**
     * Displays a single Admission model.
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
     * Creates a new Admission model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Admission();

        if ($model->load(Yii::$app->request->post())) {

            $model->admission_created_by = Yii::$app->user->identity->id;
            $model->admission_updated_by = $model->admission_created_by;
            $model->admission_updated_at = date('Y-m-d');
            $model->admission_created_at = $model->admission_updated_at;

            $model->admission_status = 1; //defaultly the status of any admission type is active.

            if ($model->save()) {
                return $this->redirect(['index']);
            }else{
                print_r($model->errors);die();
            }
            // 

            // admission_created_by
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Admission model.
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
     * Deletes an existing Admission model.
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
     * Finds the Admission model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Admission the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Admission::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
