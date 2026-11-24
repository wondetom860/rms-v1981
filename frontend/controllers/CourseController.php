<?php

namespace frontend\controllers;

use Yii;
use backend\models\Course;
use backend\models\CourseSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CourseController implements the CRUD actions for Course model.
 */
class CourseController extends Controller
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
                        'actions' => ['index', 'add-equivalent', 'remove-equivalent', 'create', 'view', 'list-all', 'update', 'delete', 'clslvllists', 'course-history', 'show-more', 'coursetitles', 'change-type', '_set-course-type', 'get-course-types'],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['Admin', 'Registrar'],
                    ],
                    [
                        'actions' => ['course-history', 'show-more'],
                        'allow' => true,
                        'roles' => ['AcademicDean', 'PGCoordinator'],
                    ],
                    [
                        'actions' => ['clslvllists', 'clslvllistss',],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => [],
                    ],
                ]
            ]
        ];
    }

    public function actionGetCourseTypes()
    {
        $courseId = Yii::$app->request->get('courseId');
        $model = Course::findOne($courseId);
        return $this->renderPartial('_set-course-type', [
            'model' => $model,
        ]);
    }

    public function actionChangeType()
    {
        // var_dump($_POST);exit;
        if (
            isset($_POST['id']) && !empty($_POST['id'])
            && isset($_POST['course_type']) && !empty($_POST['course_type'])
        ) {
            $courseId = $_POST['id'];
            $courseType = $_POST['course_type'];
            $model = Course::findOne($courseId);
            if ($model) {
                return $model->setCourseType($courseType);
            } else {
                return "Invalid course selected.";
            }
        } else {
            echo "No data provided.";
        }
    }

    public function actionRemoveEquivalent()
    {
        $courseId = Yii::$app->request->post('courseId');
        $equivalentCourseId = Yii::$app->request->post('equivalentCourseId');
        $course = Course::findOne($courseId);
        $equivalents = $course->course_equivalet === null ? [] : json_decode($course->course_equivalet, true);
        if (($key = array_search($equivalentCourseId, $equivalents)) !== false) {
            unset($equivalents[$key]);
        }
        $course->course_equivalet = json_encode($equivalents);
        $course->course_updated_by = Yii::$app->user->identity->id;
        $course->course_updated_at = date('Y-m-d g:i:s');
        if ($course->save()) {
            $eCourse = Course::findOne($equivalentCourseId);
            $eCourse->removeEqivalentCourse($courseId);
            return $this->redirect(['view', 'id' => $courseId]);
        } else {
            echo 'failed, Error: ' . json_encode($course->errors);
        }
    }

    public function actionAddEquivalent()
    {
        $courseId = Yii::$app->request->post('courseId');
        $list = Yii::$app->request->post('list');
        $course = Course::findOne($courseId);
        $equivalents = $course->course_equivalet === null ? [] : json_decode($course->course_equivalet, true);
        foreach ($list as $item) {
            if (!in_array($item, $equivalents)) {
                $equivalents[] = $item;
            }
        }

        if (!empty($equivalents)) {
            $equivalents = array_unique($equivalents);
            $course->course_equivalet = json_encode($equivalents);

            $course->course_updated_by = Yii::$app->user->identity->id;
            $course->course_updated_at = date('Y-m-d g:i:s');
            // $course->getDat

            if ($course->save()) {
                $course->cascadeEquivalence();
                // a=b must hold for both sides, i.e a=b => b=a  ... (? a=[b,c] => (b=a & c=a & b=c) ?)
                Yii::$app->session->setFlash('success', 'Course equivalent added successfully');
                return $this->redirect(['view', 'id' => $courseId]);
            } else {
                echo 'failed, Error: ' . json_encode($course->errors);
            }
        }
    }

    public function actionListAll()
    {
        $courseId = Yii::$app->request->post('courseId');
        return $this->renderPartial('history/all-list', [
            'courses' => Course::find()->where(
                ' id <>  ' . $courseId . ' AND course_entry_type = 1 '
            )->orderBy(['course_dept_relId' => SORT_ASC, 'course_code' => SORT_ASC])
                ->all(),
            'main_course_id' => $courseId,
        ]);
    }

    /**
     * Lists all Course models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->can('Admin')) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $searchModel = new CourseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new Course();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    public function actionShowMore()
    {
        $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : die('INVALID_REQUEST');

        $model = $this->findModel($id);

        return $this->renderPartial('history/see-more', ['course' => $model]);
    }

    /**
     * Displays a single Course model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        if (!Yii::$app->user->can('Admin')) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Displays a single Course model.
     * @param integer $id
     * @return mixed
     */
    public function actionCourseHistory()
    {
        $searchModel = new CourseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new Course();
        return $this->render('history/course-history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
        // return $this->render('history/course-history', [
        //     'model' => new Course(),
        // ]);
    }

    // filters courses depending on the selected main-course - course pre-requisit setting..
    public function actionCoursetitles() //coursetitles
    {
        if (isset($_POST['courseId']) && !empty($_POST['courseId'])) {
            $mCourseId = $_POST['courseId'];
            $rep = "";
            $pCourses = \backend\models\Course::find()->where(['<>', 'id', $mCourseId])->all();
            foreach ($pCourses as $pCourse) {
                $rep . "<option value='" . $pCourse['id'] . "'>" . $pCourse['course_title'] . "</option>";
            }

            return $rep;
        }
    }

    /**
     * Creates a new Course model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->user->can('Admin')) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $model = new Course();

        if ($model->load(Yii::$app->request->post())) {
            $model->course_created_by = Yii::$app->user->identity->id;
            $model->course_updated_by = $model->course_created_by;
            $model->course_created_at = date('Y-m-d');
            $model->course_updated_at = $model->course_created_at;
            if ($model->save()) {
                return $this->redirect(['index']);
            } else {
                print_r($model->errors);
                die();
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Course model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        if (!Yii::$app->user->can('Admin')) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->course_updated_by = Yii::$app->user->identity->id;
            $model->course_updated_at = date('Y-m-d g:i:s');
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Course updated successfully');
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                print_r($model->errors);
                die();
            }
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Course model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        if (!Yii::$app->user->can('Admin')) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Course model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Course the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Course::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
