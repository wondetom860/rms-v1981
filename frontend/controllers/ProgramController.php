<?php

namespace frontend\controllers;

use Yii;
use backend\models\Program;
use backend\models\ClassLevel;
use backend\models\ProgramSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProgramController implements the CRUD actions for Program model.
 */
class ProgramController extends Controller
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
                       'actions' => ['index','create', 'update', 'delete','clslvllists','getprogs','view'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
                    [
                       'actions' => ['clslvllists','clslvllistss'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => [],
                   ],
               ]
           ]
        ];
    }

    /**
     * Lists all Program models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProgramSearch();
        $model = new Program();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model'=>$model,
        ]);
    }

    // returns list of programs for preq. setting.
    public function actionGetprogs(){
        $progs = Program::find()->all();
        $rep = '';
        if ($progs) {
            foreach ($progs as $prog) {
                $rep .= "<option value='". $prog['id'] ."'>". $prog['program_name'] ."</option>";
            }
            $rep .= "<option value='0' title='Select this option if the preq. applies for all program types..'>All</option>";
        }
        return $rep;
    }
    // Returns the array of class levels depending on the program selected.
    public function actionClslvllists(){
        if (isset($_POST['progId']) && !empty($_POST['progId'])) {
            $progId = $_POST['progId'];
            echo "<option value=''>All</option>";
            $clslvls = ClassLevel::find()->where(['clslvl_program_relid'=>$progId])->all();
            if (!empty($clslvls)) {
                foreach ($clslvls as $clslvl) {
                    echo "<option value='". $clslvl['id'] ."'>". $clslvl['clslvl_name'] ."</option>";
                }
            }
        }
    }

    // Returns the array of class levels depending on the program selected for academiccalandersetup.
    public function actionClslvllistss(){
        if (isset($_POST['progId']) && !empty($_POST['progId'])) {
            $progId = $_POST['progId'];

            echo "<option value='' title='Select this option if the scedule is for all class level students.'>All</option>";
            $clslvls = Classlevel::find()->where(['clslvl_program_relid'=>$progId])->all();
            if (!empty($clslvls)) {
                foreach ($clslvls as $clslvl) {
                    echo "<option value='". $clslvl['id'] ."'>". $clslvl['clslvl_name'] ."</option>";
                }
            }
        }
    }

    /**
     * Displays a single Program model.
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
     * Creates a new Program model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Program();

        if ($model->load(Yii::$app->request->post())) {
            $model->program_created_by = $model->program_updated_by = Yii::$app->user->identity->id;
            $model->program_created_at = $model->program_updated_at = date('Y-m-d h:i:s');
            if ($model->save()) {
               return $this->redirect('index'); 
            }else{
                print_r($model->errors);die();
            }
            // 
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Program model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Program model.
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
     * Finds the Program model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Program the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Program::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
