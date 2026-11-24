<?php
namespace common\components;
use yii;
use frontend\modules\AcademicCalendar\models\AcActivePrograms;
// use \common\components\CommonControllerFunctions as CCF;


/**
 * 
 */
class AcademicCalendar extends yii\base\Component
{

	public $_activePrograms;
	public $_activeProgramModels;
	public $_activeAcademicYearModels;
	/**
     * {@inheritdoc}
     */

	public function getActivePrograms($withlink=1){
		if (!isset($this->_activePrograms)) {
        	if (!Yii::$app->user->isGuest) {
                $modelUser = \common\models\User::findOne(Yii::$app->user->id);
                $this->_activePrograms = $modelUser->getActiveProgram($withlink);
            }else{
                $modelActiveProgram = new AcActivePrograms();
                $this->_activePrograms = $modelActiveProgram->getActiveAcademicYear($withlink);
            }
        }


        return $this->_activePrograms;
	}

    public function getCollegeAddress(){
        // get activeAcademic year:
        $ay = $this->getActiveProgramModels();
        // var_dump($ay);die();
        if ($ay) {
            return $ay[0]->getCollegeAddress();
        }

        return '';
    }

	public function getActiveProgramModels(){
		if (!isset($this->_activeAcademicYearModels)) {
        	$modelActiveProgram = new AcActivePrograms();
            $this->_activeAcademicYearModels= $modelActiveProgram->getActiveAcademicYear(null);
        }


        return $this->_activeAcademicYearModels;
	}
}
?>