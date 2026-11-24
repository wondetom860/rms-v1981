<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\models\Program;
use backend\models\Admission;
use backend\models\ClassLevel;

/**
 *  
 *
 * @author wondeTom@DEC
 */
class ListController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'logout', 'login', 'create', 'update', 'delete', 'view'],
                'rules' => [
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['index', 'logout', 'create', 'update', 'delete', 'view'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 
     * @param type $id
     *  
     */
    public function actionProgramList($id) {
        $countProgram = Program::find()->count();
        $progs = Program::find()->all();
        if ($countProgram > 0) {
            foreach ($progs as $prog) {
                echo "<option value='" . $prog->id . "'>" . $prog->program_desc . "</option>";
            }
        } else {
            echo "<option> - </option>";
        }
    }

    /**
     * 
     * @param type $id
     * Get Program type List based on a given Program
     */
    /*

     * 
     * select all previous enroll data
     *  if enroll data exists
     *      take max(class_year):  //
     *   else return 1;
     * 
     * getSemister:
     *  if(MM in SEP(9) - Jan(2)) sem = I
     *  else if (MM in Feb(2) - Jun(6)) sem II
     *  else sem = uknown
     * 
     * 
     *      */

    //credit hour
    //
    public function actionProgramTypeList($id) {
        $countProgType = Admission::find()
                ->where(['admission_id' => $id])
                ->count();
        if ($countProgType > 0) {
            $progTypes = Admission::find()
                    ->where(['admission_id' => $id])
                    ->all();
            foreach ($progTypes as $progType) {
                echo "<option value='" . $admission->id . "'>" . $admission->admission_desc . "</option>";
            }
        } else {
            //echo "<option> - </option>";
            echo "<option value=''>" . Yii::t('rms', 'Select Admission') . "</option>";
        }
    }

    /**
     * 
     * @param type $id
     * Get Class Year List based on a given Program
     * takes program_id of selected program and returns the       level in that program, eg DEG: I,II,III, IV, * and V and for MASTER : I, and  II
     */
    public function actionClassLevelList($id) { 
        $countClassYear = Classlevel::find()
                ->where(['clslvl_program_relId' => $id])
                ->count();
        $classYrs = Classlevel::find()
                ->where(['clslvl_program_relId' => $id])
                ->all();
        if ($countClassYear > 0) {
            /*foreach ($classYrs as $classYr) {
                echo "<option value='" . $classYr->clslvl_program_relId . "'>" . $classYr->clslvl_desc . "</option>";
            }*/
            echo "found";
        } else {
            echo "<option> - </option>";
            //echo "<option value=''>" . Yii::t('rms', 'Select Class Year') . "</option>";
        }
    }

    public function actionCourseListForProgram($program_id, $class_year_id, $semister) {
        $countCourses = \frontend\models\CourseEnrollment::find()
                ->where(['program_id' => $program_id])
                ->andWhere(['class_year_id' => $class_year_id])
                ->andWhere(['semester_id' => $semister])
                ->count();

        if ($countCourses > 0) { // there is a list of enrolled courses available
            $Courses = \frontend\models\CourseEnrollment::find()
                    ->where(['program_id' => $program_id])
                    ->andWhere(['class_year_id' => $class_year_id])
                    ->andWhere(['semester_id' => $semister])
                    ->all();
            foreach ($Courses as $Course) {
                echo "<option value = '" . $Course->id . "'>" . $Course->enrolled_courses . "</option>";
            }
        } else {
            return "<option value=''>Semester: $semister, Class_Year_Id: $class_year_id, ProgramId: $program_id</option>";
        }
    }

    public function actionCourseListForSemester()
    {
        //return $this->hasOne(ClassLevel::className(), ['id' => 'coff_clslvl_relId']);
    }

}
