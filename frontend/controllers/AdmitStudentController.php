<?php

namespace frontend\controllers;

use Yii;
use backend\models\Student1;
use common\models\User1;
use backend\models\EmergencyContact;
use backend\models\AddressInfo;
use backend\models\PostSecondaryEducation;
use backend\models\EducationInformation;
use backend\models\SecondarySchoolAttended;
use backend\models\PgSecondarySchoolsAttended;
use backend\models\EnteranceExamMark;
use frontend\models\Model;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\base\ErrorException;
use yii\web\UploadedFile;
use yii\web\Session;

use backend\models\PgEducationInfo;
use backend\models\PgPostSecondarySchool;
use \common\components\CommonControllerFunctions as CCF;


class AdmitStudentController extends \yii\web\Controller
{

    public $acadYearId;
    public $semId;

    protected function __constructor()
    {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    private function checIfExists($model)
    {
        return User1::find()
            ->where([
                'first_name' => $model->first_name,
                'middle_name' => $model->middle_name,
                'last_name' => $model->last_name,
            ])
            ->count() > 0;
    }


    public $sessionForm = [];

    public function actionSelfAdmissionPg()
    {
        $sessionForm = isset($_POST['sessionForm']) && !empty($_POST['sessionForm']) ? $_POST['sessionForm'] : die('Application Form Error!');

        $params = [];
        if (is_array($sessionForm)) {
            // echo "Models<br>";
            foreach ($sessionForm as $model) {
                // 0=>CSRF
                // 1=>MODEL NAME
                parse_str($model, $params);
                if (is_array($params) && isset(array_keys($params)[1])) {
                    $modelName = array_keys($params)[1];
                    switch ($modelName) {
                        case 'User1':
                            // $this->sessionForm['modelUser'] = new User1();
                            $this->sessionForm['modelUser'] = $params[$modelName];
                            // $this->sessionForm['modelUser'] = $params[$modelName];
                            break;
                        case 'Student1':
                            // $this->sessionForm['modelStudent'] = $params[$modelName];
                            // $this->sessionForm['modelStudent'] = new Student1();
                            $this->sessionForm['modelStudent'] = $params[$modelName];
                            break;
                        case 'EmergencyContact':
                            // $this->sessionForm['modelEC'] = $params[$modelName];
                            // $this->sessionForm['modelEC'] = new EmergencyContact();
                            $this->sessionForm['modelEC'] = $params[$modelName];
                            break;
                        case 'AddressInfo':
                            // address indicator -
                            //  1=> place of birth
                            //  2=> current place of residential
                            //  1=> place of birth of Emergency contact

                            if (isset($params[$modelName]['_indicator']) && $params[$modelName]['_indicator'] == '1') {
                                // $this->sessionForm['modelAddressBirth'] = $params[$modelName];
                                // $this->sessionForm['modelAddressBirth'] = new AddressInfo();
                                $this->sessionForm['modelAddressBirth'] = $params[$modelName];
                            } elseif (isset($params[$modelName]['_indicator']) && $params[$modelName]['_indicator'] == '2') {
                                // $this->sessionForm['modelAddressRedencial'] = $params[$modelName];
                                // $this->sessionForm['modelAddressRedencial'] = new AddressInfo();
                                $this->sessionForm['modelAddressRedencial'] = $params[$modelName];
                            } else if (isset($params[$modelName]['_indicator']) && $params[$modelName]['_indicator'] == '3') {
                                // $this->sessionForm['modelAddressEC'] = $params[$modelName];
                                // $this->sessionForm['modelAddressEC'] = new AddressInfo();
                                $this->sessionForm['modelAddressEC'] = $params[$modelName];
                            }

                            break;
                        case 'PgEducationInfo':
                            // $this->sessionForm['models']['modelEI'] = $params[$modelName];
                            // $this->sessionForm['models']['modelEI'] = new EducationInformation();
                            $this->sessionForm['models']['modelEI'] = $params[$modelName];
                            break;
                        case 'PgPostSecondarySchool':
                            // $this->sessionForm['models']['modelPSE'] = $params[$modelName];
                            // $this->sessionForm['models']['modelPSE'] = [new PostSecondaryEducation()];
                            // var_dump($params[$modelName]);die();
                            foreach ($params[$modelName] as $PSEkey) {
                                $this->sessionForm['models']['modelPSE'][] = $PSEkey;
                            }
                            // echo "<pre>";
                            // var_dump($this->sessionForm['models']['modelPSE']);
                            // die();
                            // $this->sessionForm['models']['modelPSE'] = $params[$modelName];
                            break;
                        case 'PgSecondarySchoolsAttended':
                            // $this->sessionForm['models']['modelSSA'] = $params[$modelName];
                            foreach ($params[$modelName] as $SSAkey) {
                                $this->sessionForm['models']['modelSSA'][] = $SSAkey;
                            }
                            break;
                        case 'EnteranceExamMark':
                            // $this->sessionForm['models']['modelEEM'] = $params[$modelName];
                            // var_dump($params[$modelName]);die();
                            foreach ($params[$modelName] as $EEMkey) {
                                $this->sessionForm['models']['modelEEM'][] = $EEMkey;
                            }
                            break;

                        default:
                            // continue;
                            break;
                    }
                } else {
                    continue;
                }
            }

            // echo "<pre>";
            // var_dump($this->sessionForm);
            // die();
            // start actual sae operation here!
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $modelUser = new User1();
                // !Yii::$app->user->isGuest && 
                if (isset($this->sessionForm['modelUser']['id'])) {
                    $modelUser->id = intval($this->sessionForm['modelUser']['id']);
                } else {
                    // save modelUser
                    $modelUser = new User1();
                    $modelUser->first_name = ucfirst(strtolower(trim($this->sessionForm['modelUser']['first_name'])));
                    $modelUser->middle_name = ucfirst(strtolower(trim($this->sessionForm['modelUser']['middle_name'])));
                    $modelUser->last_name = ucfirst(strtolower(trim($this->sessionForm['modelUser']['last_name'])));
                    $modelUser->dob = $this->sessionForm['modelUser']['dob'];
                    $modelUser->dob_ET = $this->sessionForm['modelUser']['dob_ET'];
                    $modelUser->email = $this->sessionForm['modelUser']['email'];
                    $modelUser->nationality = $this->sessionForm['modelUser']['nationality'];
                    $modelUser->phone_mobile = $this->sessionForm['modelUser']['phone_mobile'];
                    $modelUser->user_type = $this->sessionForm['modelUser']['user_type'];
                    $modelUser->gender = $this->sessionForm['modelUser']['gender'];

                    $modelUser->place_of_birth_address_relId = NULL;
                    $modelUser->place_of_residence_address_relId = NULL;

                    // $userAccount = new User();
                    $modelUser->OTP = $modelUser->getOTP();
                    // $modelUser->
                    $modelUser->status = $modelUser::STATUS_UNAPPROVED;
                    // $model->created_by = Yii::$app->user->identity->id;
                    $modelUser->created_at = date('Y-m-d h:i:s');

                    $modelUser->username = md5($modelUser->OTP);

                    // $model->username = $this->getUsername();

                    $modelUser->setPassword($modelUser->getPassword());
                    $modelUser->generateAuthKey();

                    if ($this->checIfExists($modelUser)) {
                        echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                        echo "Personal Information Creating failed, Please check under Personal Information tab! (SUCH USER INFORMTION ALREADY AVAILABLE, PLEASE CONTACT REGISTRAR IF THIS IS MISTAKE!)";
                        echo "</pre>";
                        echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                        echo "<p class='bg-warning'>User information already registered, Please login with your user_name and password if you have, or contact registrar.</p>";
                        echo "</pre></div>";
                        throw new ErrorException("Operatin halted", 1);
                    }

                    if ($modelUser->save()) {
                        $auth = Yii::$app->authManager;
                        $role = $auth->getRole('Student');
                        $auth->assign($role, $modelUser->id);
                        echo "<p class = 'bg-success'>Personal Information Saved - Success</p>";
                    } else {
                        echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                        echo "Personal Information Creating failed, Please check under Personal Information tab!";
                        echo "</pre>";
                        echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                        var_dump($modelUser->getErrors());
                        echo "</pre></div>";
                        throw new ErrorException("Operatin halted", 1);
                    }
                }

                // save Education infor
                $modelEI2 = isset($this->sessionForm['models']['modelEI']) ? $this->sessionForm['models']['modelEI'] : NULL;

                // $modelEI = NULL;
                // var_dump($modelEI2);die();
                $eiAvailable = is_null($modelEI2);
                if ($modelEI2) {
                    $modelEI = new PgEducationInfo();
                    $modelEI->pgei_UG_university_name = $modelEI2['pgei_UG_university_name'];
                    $modelEI->pgei_CGPA = $modelEI2['pgei_CGPA'];
                    $modelEI->pgei_field_of_study = $modelEI2['pgei_field_of_study'];
                    $modelEI->pgei_major = $modelEI2['pgei_major'];
                    $modelEI->pgei_minor = $modelEI2['pgei_minor'];


                    $modelEI->pgei_work_experience = ' - ';
                    $modelEI->pgei_awards = ' - ';
                    $modelEI->pgei_reserches = ' - ';
                    // $modelEI->ei_created_by = Yii::$app->user->identity->id;

                    if ($modelEI->save()) {
                        echo "<p class = 'bg-success'>Education Information Saved - Success</p>";
                    } else {
                        echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                        echo "Education Background Information Creating failed, Please check under Education Information tab!";
                        echo "</pre>";
                        echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                        var_dump($modelEI->getErrors());
                        echo "</pre></div>";
                        throw new ErrorException("Operatin halted", 1);
                    }
                } else {
                    echo "<p class = 'bg-default'>Education Information NOT provided</p>";
                }

                // save secondary Schools attended
                $attended12 = true;
                $ssaSuccess = true;
                $modelSSAs = $this->sessionForm['models']['modelSSA'];

                if ($modelSSAs && is_array($modelSSAs) && $modelEI) {
                    foreach ($modelSSAs as $SSAkey) {
                        $modelSSA = new PgSecondarySchoolsAttended();

                        $modelSSA->pssa_pei_relId = $modelEI->id;
                        $modelSSA->pgssa_name_of_school = $SSAkey['pgssa_name_of_school'];
                        $modelSSA->pgssa_location = $SSAkey['pgssa_location'];
                        // $modelSSA->ssa_town = $SSAkey['ssa_town'];
                        // $modelSSA->eem_reference_number = $SSAkey['eem_reference_number'];
                        $modelSSA->pgssa_years_from = intval($SSAkey['pgssa_years_from']);
                        $modelSSA->pgssa_years_to = intval($SSAkey['pgssa_years_to']);
                        if (strtotime($modelSSA->pgssa_years_to) === false || strtotime($modelSSA->pgssa_years_from) === false) {
                            throw new ErrorException("Year of Secondary School Attended is Invalid, Please check Under Secondary Schools attended tab.", 1);
                        }
                        // var_dump($SSAkey['ssa_gradeLevels']);

                        if ($modelSSA->save()) {
                            continue;
                        } else {
                            $ssaSuccess = false;
                            echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                            echo "Secondary Schools Information Creating failed, Please check under Education Information tab!";
                            echo "</pre>";
                            echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                            var_dump($modelSSA->getErrors());
                            echo "</pre></div>";
                            throw new ErrorException("Operatin halted", 1);
                        }
                    }
                    if (!$attended12) { //information about g12 or 10+2 is required for student copy
                        echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                        echo "Secondary Schools Information Creating failed, Grade levels [12 or 10+2] missing";
                        echo "</pre>";
                        echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                        var_dump($modelSSA->getErrors());
                        echo "</pre></div>";
                        throw new ErrorException("Operatin halted", 1);
                    }
                } else {
                    echo "<p class = 'bg-default'>Secondary Schools Information NOT available</p>";
                }
                if ($ssaSuccess && $attended12) {
                    echo "<p class = 'bg-success'>Secondary Schools Information Saved - Success</p>";
                }

                // save post secondary schools attended::
                $psseSuccess = TRUE;
                $psseRecordAvailable = false;
                $modelPSEs = isset($this->sessionForm['models']['modelPSE']) ? $this->sessionForm['models']['modelPSE'] : NULL;

                // var_dump($modelPSEs);die();

                if ($modelPSEs && is_array($modelPSEs) &&  $modelEI) {


                    $psseRecordAvailable = true;
                    foreach ($modelPSEs as $PSEkey) {
                        if (!is_array($PSEkey)) {
                            continue;
                        }
                        $modelPSE = new PgPostSecondarySchool();

                        $modelPSE->ppss_pei_relId = $modelEI->id;
                        $modelPSE->pgpss_institute_name = $PSEkey['pgpss_institute_name'];
                        $modelPSE->pgpss_location = $PSEkey['pgpss_location'];
                        $modelPSE->pgpss_deploma_awarded = $PSEkey['pgpss_deploma_awarded'];
                        // $modelPSE->pse_CGPA_obtained = $PSEkey['pse_CGPA_obtained'];
                        $modelPSE->pgpss_date_of_award = $PSEkey['pgpss_date_of_award'];
                        if (strtotime($modelPSE->pgpss_date_of_award) === false || strtotime($modelPSE->pgpss_date_of_award) === false) {
                            throw new ErrorException("Year Invalid, Please check Under Post Secondary Schools attended tab.", 1);
                        }
                        // $modelPSE->eem_markObtained = $PSEkey['eem_markObtained'];
                        // $modelPSE->pse_created_at = date("Y-m-d h:i:s");

                        if ($modelPSE->save()) {
                            continue;
                        } else {
                            $psseSuccess = false;
                            echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                            echo "Post secondary eduacation Information Creating failed, Please check under Education Background tab!";
                            echo "</pre>";
                            echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                            var_dump($modelPSE->getErrors());
                            echo "</pre></div>";
                            throw new ErrorException("Operatin halted", 1);
                        }
                    }
                }
                if ($psseSuccess && $psseRecordAvailable) {
                    echo "<p class = 'bg-success'>Post Secodary Education Information Saved - Success</p>";
                } else {
                    echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                    echo "Post secondary school Information NOT provided , Please check under Education Background tab!";
                    echo "</pre>";
                    echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                    var_dump($modelPSE->getErrors());
                    echo "</pre></div>";
                    throw new ErrorException("Operatin halted", 1);
                }

                // save addressEC

                $addressEC = $this->sessionForm['modelAddressEC'];
                $modelAddressEC = new AddressInfo();

                $modelAddressEC->address_country_relId = $this->sessionForm['modelAddressEC']['address_country_relId'];
                $modelAddressEC->address_region_relId = $this->sessionForm['modelAddressEC']['address_region_relId'];
                $modelAddressEC->address_woreda = $this->sessionForm['modelAddressEC']['address_woreda'];
                $modelAddressEC->address_kebele = $this->sessionForm['modelAddressEC']['address_kebele'];
                $modelAddressEC->address_hourse_no = $this->sessionForm['modelAddressEC']['address_hourse_no'];
                $modelAddressEC->address_town = $this->sessionForm['modelAddressEC']['address_town'];

                if ($modelAddressEC->save()) {
                    echo "<p class = 'bg-success'>Emergency Contact Address Information Saved - Success</p>";
                } else {
                    echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                    echo "Emergency Contact address Information saving failed, Please check under Emergency Contact Address Information !";
                    echo "</pre>";
                    echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                    var_dump($modelAddressEC->getErrors());
                    echo "</pre></div>";
                    throw new ErrorException("Operatin halted", 1);
                }

                // save modelEC
                $modelEC = $this->sessionForm['modelEC'];
                if ($modelEC && is_array($modelEC)) {

                    $modelEC = new EmergencyContact();

                    $modelEC->ec_first_name = $this->sessionForm['modelEC']['ec_first_name'];
                    $modelEC->ec_last_name = $this->sessionForm['modelEC']['ec_last_name'];
                    $modelEC->ec_relationShip = $this->sessionForm['modelEC']['ec_relationShip'];
                    $modelEC->ec_phone_home = $this->sessionForm['modelEC']['ec_phone_home'];
                    $modelEC->ec_phone_mobile = $this->sessionForm['modelEC']['ec_phone_mobile'];
                    $modelEC->ec_address_relId = $modelAddressEC->id;
                    $modelEC->ec_email = $this->sessionForm['modelEC']['ec_email'];

                    if ($modelEC->save()) {
                        echo "<p class = 'bg-success'>Emergency Contact Information Saved - Success</p>";
                    } else {
                        echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                        echo "Emergency Contact Information saving failed, Please check under Emergency Contact Information !";
                        echo "</pre>";
                        echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                        var_dump($modelEC->getErrors());
                        echo "</pre></div>";
                        throw new ErrorException("Operatin halted", 1);
                    }
                }
                // adl'alsfksd

                // student record at the last

                $sModel = $this->sessionForm['modelStudent'];
                // var_dump($sModel);die();
                if ($sModel && is_array($sModel)) {

                    $modelStudent = new Student1();
                    $modelStudent->std_user_relId = $modelUser->id;
                    $modelStudent->std_admission_relId = $sModel['std_admission_relId'];
                    $modelStudent->std_prog_relId = $sModel['std_prog_relId'];
                    $modelStudent->std_dept_relId = $sModel['std_dept_relId'];
                    $modelStudent->std_semester_status = "NOT APPROVED";
                    $this->__constructor();
                    $modelStudent->std_acadYear_relId = $sModel['std_acadYear_relId'];
                    $modelStudent->std_sem_relId = $sModel['std_sem_relId'];
                    $modelStudent->std_schlrshpType_relId = $sModel['std_schlrshpType_relId'];
                    $modelStudent->std_deptChoice = isset($sModel['std_deptChoice']) ? $sModel['std_deptChoice'] : json_encode([]);
                    $modelStudent->st_educ_info_relId =  $modelEI ? $modelEI->id : NULL;
                    $modelStudent->std_emergency_contact_relId = $modelEC->id;
                    $modelStudent->std_type = $sModel['std_type'];
                    $modelStudent->std_marital_status = $sModel['std_marital_status'];
                    $modelStudent->std_spec_relId = $sModel['std_spec_relId'];

                    // $admission,$program,$department,$specialization = NULL
                    $modelStudent->std_regId = $modelStudent->getStdRegId($modelStudent->std_admission_relId, $modelStudent->std_prog_relId, $modelStudent->std_dept_relId, $modelStudent->std_spec_relId);

                    // $modelStudent->std_clslvl_relId = NULL;
                    $c = \backend\models\ClassLevel::find()
                        ->where([
                            'clslvl_program_relId' => $modelStudent->std_prog_relId
                        ])
                        ->orderBy('clslvl_num')
                        ->all();
                    $modelStudent->std_clslvl_relId = $c[0]->id;

                    $modelStudent->std_created_at = date('Y-m-d h:i:s');
                    // $model->std_created_by = Yii::$app->user->identity->id;
                    if ($modelStudent->save()) {

                        $_SESSION['WORK_EXPERIENCE'] = $modelEI2['pgei_work_experience'];
                        $_SESSION['RESEARCHES'] = $modelEI2['pgei_reserches'];
                        $_SESSION['AWARDS'] = $modelEI2['pgei_awards'];

                        if ($modelUser->UploadAttachments($modelStudent, $modelStudent->st_educ_info_relId)) {
                            $transaction->commit();
                            echo "<p class = 'bg-success'>Student Account Created - Success</p>";
                            echo "<div class = 'container-fluid bg-success wonde_message'> 
                                    <div class='panel panel-success'>
                                        <div class='panel-heading'><h2>Admission Form Accepted ! </h2></div>
                                        <div class='panel-body'>
                                            <center>
                                                Your Admission form submitted successfully, </br>
                                                Please follow up your account activation status.<br>
                                                You can check your account status by providing the OTP code below.<br>
                                                <p class='bg-code'><b>{$modelUser->OTP}</b></p>
                                                Please Keep the code untill you get valid user name and password of your account.
                                                <p class='bg-warning'>Don't Loose your OTP!</p>
                                            </center>
                                            <div class='container bg-info'>
                                                <p>Please click the OK button below to complete the operation!</p>
                                                <button class='btn btn-warning btn-sm' onclick='refreshFrom()'>OK</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>";
                            return;
                        } else {
                            echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                            echo "Work Experience, Awards and Research Works are required! Please check under Academic Infirmation Tab!";
                            echo "</pre>";
                            echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                            var_dump($modelStudent->getErrors());
                            echo "</pre></div>";
                            throw new ErrorException("Operatin halted", 1);
                        }
                    } else {
                        echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                        echo "Academic Information Creating failed, Please check under Academic Information tab!";
                        echo "</pre>";
                        echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                        var_dump($modelStudent->getErrors());
                        echo "</pre></div>";
                        throw new ErrorException("Operatin halted", 1);
                    }
                }
            } catch (ErrorException $e) {
                $transaction->rollBack();
                echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                var_dump($e->getMessage());
                echo "</pre>";
                echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                var_dump($e);
                echo "</pre></div>";
                return;
            }
        } else {
            die("Form Error");
        }
    }

    public function actionSelfAdmission()
    {
        $sessionForm = isset($_POST['sessionForm']) && !empty($_POST['sessionForm']) ? $_POST['sessionForm'] : die('Application Form Error!');

        $params = [];
        if (is_array($sessionForm)) {
            // echo "Models<br>";
            foreach ($sessionForm as $model) {
                // 0=>CSRF
                // 1=>MODEL NAME
                parse_str($model, $params);
                if (is_array($params) && isset(array_keys($params)[1])) {
                    $modelName = array_keys($params)[1];
                    switch ($modelName) {
                        case 'User1':
                            // $this->sessionForm['modelUser'] = new User1();
                            $this->sessionForm['modelUser'] = $params[$modelName];
                            // $this->sessionForm['modelUser'] = $params[$modelName];
                            break;
                        case 'Student1':
                            // $this->sessionForm['modelStudent'] = $params[$modelName];
                            // $this->sessionForm['modelStudent'] = new Student1();
                            $this->sessionForm['modelStudent'] = $params[$modelName];
                            break;
                        case 'EmergencyContact':
                            // $this->sessionForm['modelEC'] = $params[$modelName];
                            // $this->sessionForm['modelEC'] = new EmergencyContact();
                            $this->sessionForm['modelEC'] = $params[$modelName];
                            break;
                        case 'AddressInfo':
                            // address indicator -
                            //  1=> place of birth
                            //  2=> current place of residential
                            //  1=> place of birth of Emergency contact

                            if (isset($params[$modelName]['_indicator']) && $params[$modelName]['_indicator'] == '1') {
                                // $this->sessionForm['modelAddressBirth'] = $params[$modelName];
                                // $this->sessionForm['modelAddressBirth'] = new AddressInfo();
                                $this->sessionForm['modelAddressBirth'] = $params[$modelName];
                            } elseif (isset($params[$modelName]['_indicator']) && $params[$modelName]['_indicator'] == '2') {
                                // $this->sessionForm['modelAddressRedencial'] = $params[$modelName];
                                // $this->sessionForm['modelAddressRedencial'] = new AddressInfo();
                                $this->sessionForm['modelAddressRedencial'] = $params[$modelName];
                            } else if (isset($params[$modelName]['_indicator']) && $params[$modelName]['_indicator'] == '3') {
                                // $this->sessionForm['modelAddressEC'] = $params[$modelName];
                                // $this->sessionForm['modelAddressEC'] = new AddressInfo();
                                $this->sessionForm['modelAddressEC'] = $params[$modelName];
                            }

                            break;
                        case 'EducationInformation':
                            // $this->sessionForm['models']['modelEI'] = $params[$modelName];
                            // $this->sessionForm['models']['modelEI'] = new EducationInformation();
                            $this->sessionForm['models']['modelEI'] = $params[$modelName];
                            break;
                        case 'PostSecondaryEducation':
                            // $this->sessionForm['models']['modelPSE'] = $params[$modelName];
                            // $this->sessionForm['models']['modelPSE'] = [new PostSecondaryEducation()];
                            // var_dump($params[$modelName]);die();
                            foreach ($params[$modelName] as $PSEkey) {
                                $this->sessionForm['models']['modelPSE'][] = $PSEkey;
                            }
                            // echo "<pre>";
                            // var_dump($this->sessionForm['models']['modelPSE']);
                            // die();
                            // $this->sessionForm['models']['modelPSE'] = $params[$modelName];
                            break;
                        case 'SecondarySchoolAttended':
                            // $this->sessionForm['models']['modelSSA'] = $params[$modelName];
                            foreach ($params[$modelName] as $SSAkey) {
                                $this->sessionForm['models']['modelSSA'][] = $SSAkey;
                            }
                            break;
                        case 'EnteranceExamMark':
                            // $this->sessionForm['models']['modelEEM'] = $params[$modelName];
                            // var_dump($params[$modelName]);die();
                            foreach ($params[$modelName] as $EEMkey) {
                                $this->sessionForm['models']['modelEEM'][] = $EEMkey;
                            }
                            break;

                        default:
                            // continue;
                            break;
                    }
                } else {
                    continue;
                }
            }

            $settingEntry = \frontend\modules\AcademicCalendar\models\AcProgramSchedule::findOne($this->sessionForm['modelStudent']['std_acProgSched_relId']);
            $activeProgram = $settingEntry->activeProgramRel;

            $user = User1::findOne($this->sessionForm['modelUser']['id']);
            if (
                $user->getStudents1()
                ->andFilterWhere([
                    'std_acadYear_relId' => $activeProgram->academicYearRel->schoolYearRel->id,
                    'std_sem_relId' => $activeProgram->academicYearRel->semesterRel->id
                ])->count() > 0
            ) {
                Yii::$app->session->setFlash('type', 'warning');
                Yii::$app->session->setFlash('message', 'Admission Request accepted for this academic year schedule');
                return $this->redirect(['site/index']);
                // return "Admission already requested for this academic year schedule";
            }

            // start actual save operation here!
            $transaction = Yii::$app->db->beginTransaction();
            try {

                // save addressBirth models
                $modelAddressBirth = new AddressInfo();

                $modelAddressBirth->address_country_relId = $this->sessionForm['modelAddressBirth']['address_country_relId'];
                $modelAddressBirth->address_region_relId = $this->sessionForm['modelAddressBirth']['address_region_relId'];
                $modelAddressBirth->address_woreda = $this->sessionForm['modelAddressBirth']['address_woreda'];
                $modelAddressBirth->address_kebele = $this->sessionForm['modelAddressBirth']['address_kebele'];
                $modelAddressBirth->address_hourse_no = $this->sessionForm['modelAddressBirth']['address_hourse_no'];
                $modelAddressBirth->address_town = $this->sessionForm['modelAddressBirth']['address_town'];
                $modelAddressBirth->address_zone = $this->sessionForm['modelAddressBirth']['address_zone'];

                if ($modelAddressBirth->save()) {
                    echo "<p class = 'bg-success'>Birth Place Information Saved - Success</p>";
                } else {
                    echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                    echo "Birth Place Creating failed, Please check under Address Information >> address Birth Place!";
                    echo "</pre>";
                    echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                    var_dump($modelAddressBirth->getErrors());
                    echo "</pre></div>";
                    throw new ErrorException("Operatin halted", 1);
                }

                // save addressBirth models
                $modelAddressRedencial = new AddressInfo();

                $modelAddressRedencial->address_country_relId = $this->sessionForm['modelAddressRedencial']['address_country_relId'];
                $modelAddressRedencial->address_region_relId = $this->sessionForm['modelAddressRedencial']['address_region_relId'];
                $modelAddressRedencial->address_woreda = $this->sessionForm['modelAddressRedencial']['address_woreda'];
                $modelAddressRedencial->address_kebele = $this->sessionForm['modelAddressRedencial']['address_kebele'];
                $modelAddressRedencial->address_hourse_no = $this->sessionForm['modelAddressRedencial']['address_hourse_no'];
                $modelAddressRedencial->address_town = $this->sessionForm['modelAddressRedencial']['address_town'];
                $modelAddressRedencial->address_zone = $this->sessionForm['modelAddressRedencial']['address_zone'];


                if ($modelAddressRedencial->save()) {
                    echo "<p class = 'bg-success'>Residential Place Information Saved - Success</p>";
                } else {
                    echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                    echo "Residential Place Creating failed, Please check under Address Information >> address Birth Place!";
                    echo "</pre>";
                    echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                    var_dump($modelAddressRedencial->getErrors());
                    echo "</pre></div>";
                    throw new ErrorException("Operatin halted", 1);
                }

                // save modelUser
                $modelUser = new User1();
                // !Yii::$app->user->isGuest && 
                if (isset($this->sessionForm['modelUser']['id'])) {
                    // $modelUser->id = intval($this->sessionForm['modelUser']['id']);
                    $modelUser = User1::findOne(intval($this->sessionForm['modelUser']['id']));
                    $modelUser->email = $this->sessionForm['modelUser']['email'];
                    $modelUser->phone_mobile = $this->sessionForm['modelUser']['phone_mobile'];
                    $modelUser->place_of_birth_address_relId = $modelAddressBirth->id;
                    $modelUser->place_of_residence_address_relId = $modelAddressRedencial->id;
                    $modelUser->first_name = ucfirst(strtolower($this->sessionForm['modelUser']['first_name']));
                    $modelUser->middle_name = ucfirst(strtolower($this->sessionForm['modelUser']['middle_name']));
                    $modelUser->last_name = ucfirst(strtolower($this->sessionForm['modelUser']['last_name']));
                    $modelUser->dob = $this->sessionForm['modelUser']['dob'];
                    $modelUser->dob_ET = $this->sessionForm['modelUser']['dob_ET'];

                    $modelUser->update(false);
                } else {
                    // save modelUser
                    $modelUser = new User1();
                    $modelUser->first_name = ucfirst(strtolower($this->sessionForm['modelUser']['first_name']));
                    $modelUser->middle_name = ucfirst(strtolower($this->sessionForm['modelUser']['middle_name']));
                    $modelUser->last_name = ucfirst(strtolower($this->sessionForm['modelUser']['last_name']));
                    $modelUser->dob = $this->sessionForm['modelUser']['dob'];
                    $modelUser->dob_ET = $this->sessionForm['modelUser']['dob_ET'];
                    $modelUser->email = $this->sessionForm['modelUser']['email'];
                    $modelUser->nationality = $this->sessionForm['modelUser']['nationality'];
                    $modelUser->phone_mobile = $this->sessionForm['modelUser']['phone_mobile'];
                    $modelUser->user_type = $this->sessionForm['modelUser']['user_type'];
                    $modelUser->gender = $this->sessionForm['modelUser']['gender'];

                    $modelUser->place_of_birth_address_relId = $modelAddressBirth->id;
                    $modelUser->place_of_residence_address_relId = $modelAddressRedencial->id;

                    // $userAccount = new User();
                    $modelUser->OTP = $modelUser->getOTP();
                    // $modelUser->
                    $modelUser->status = $modelUser::STATUS_UNAPPROVED;
                    // $model->created_by = Yii::$app->user->identity->id;
                    $modelUser->created_at = date('Y-m-d h:i:s');

                    $modelUser->username = md5($modelUser->OTP);

                    // $model->username = $this->getUsername();

                    $modelUser->setPassword($modelUser->getPassword());
                    $modelUser->generateAuthKey();

                    if ($this->checIfExists($modelUser)) {
                        echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                        echo "Personal Information Creating failed, Please check under Personal Information tab! (SUCH USER INFORMTION ALREADY AVAILABLE, PLEASE CONTACT REGISTRAR IF THIS IS MISTAKE!)";
                        echo "</pre>";
                        echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                        echo "<p class='bg-warning'>User infprmation already registered, Please login with your user_name and password if you have, or contact registrar.</p>";
                        echo "</pre></div>";
                        throw new ErrorException("Operatin halted", 1);
                    }

                    if ($modelUser->save()) {
                        $auth = Yii::$app->authManager;
                        $role = $auth->getRole('Student');
                        $auth->assign($role, $modelUser->id);
                        echo "<p class = 'bg-success'>Personal Information Saved - Success</p>";
                    } else {
                        echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                        echo "Personal Information Creating failed, Please check under Personal Information tab!";
                        echo "</pre>";
                        echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                        var_dump($modelUser->getErrors());
                        echo "</pre></div>";
                        throw new ErrorException("Operatin halted", 1);
                    }
                }

                // var_dump($this->sessionForm['models']['modelEI']);die();
                // save Education infor
                $modelEI2 = isset($this->sessionForm['models']['modelEI']) ? $this->sessionForm['models']['modelEI'] : NULL;

                // $modelEI = NULL;
                // var_dump($modelEI2);die();
                $eiAvailable = is_null($modelEI2);
                if ($modelEI2) {
                    $modelEI = new EducationInformation();
                    $modelEI->ei_enteranceMarkPoint = intval($modelEI2['ei_enteranceMarkPoint']);
                    $modelEI->ei_created_at = date('Y-m-d h:i:d');
                    $modelEI->ei_prog_relId = $activeProgram->program_relId;
                    // $modelEI->ei_created_by = Yii::$app->user->identity->id;

                    if ($modelEI->save()) {
                        echo "<p class = 'bg-success'>Education Information Saved - Success</p>";
                    } else {
                        echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                        echo "Education Background Information Creating failed, Please check under Education Information tab!";
                        echo "</pre>";
                        echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                        var_dump($modelEI->getErrors());
                        echo "</pre></div>";
                        throw new ErrorException("Operatin halted", 1);
                    }
                } else {
                    echo "<p class = 'bg-default'>Education Information NOT provided</p>";
                }



                // save enterance exam marks::
                $eiSuccess = TRUE;
                $modelEEMs = $this->sessionForm['models']['modelEEM'];

                if ($modelEEMs && is_array($modelEEMs) && $modelEI->ei_enteranceMarkPoint != 0) {
                    $count = 0;
                    foreach ($modelEEMs as $EEMkey) {
                        $modelEEM = new EnteranceExamMark();

                        if (!is_array($EEMkey)) {
                            continue;
                            // $eiSuccess = false;
                            // echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                            // echo "Enterance Examination Information Creating failed, Please check under Education Information tab! There is error with your form, Please refill it!";
                            // echo "</pre>";
                            // echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                            // var_dump($EEMkey);
                            // echo "</pre></div>";
                            // throw new ErrorException("Operatin halted", 1);
                        }
                        $modelEEM->eem_ei_relId = $modelEI->id;
                        $modelEEM->eem_subject = $EEMkey['eem_subject'];
                        $modelEEM->eem_reference_number = $EEMkey['eem_reference_number'];
                        $modelEEM->eem_year = intval($EEMkey['eem_year']);
                        if (strtotime($modelEEM->eem_year) === false) {
                            throw new ErrorException("Year of Enterance Exam Invalid, Please check Under Enterance Exam tab.", 1);
                        }
                        $modelEEM->eem_markObtained = $EEMkey['eem_markObtained'];
                        $modelEEM->eem_created_at = date("Y-m-d h:i:s");

                        if ($modelEEM->save()) {
                            unset($modelEEM);
                            continue;
                        } else {
                            $eiSuccess = false;
                            echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                            echo "Enterance Examination Information Creating failed, Please check under Education Information tab!";
                            echo "</pre>";
                            echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                            var_dump($modelEEM->getErrors());
                            echo "</pre></div>";
                            throw new ErrorException("Operatin halted", 1);
                        }
                    }
                } else {
                    echo "<p class = 'bg-default'>Enterance Examination Information NOT available</p>";
                }
                if ($eiSuccess) {
                    echo "<p class = 'bg-success'>Enterance Examination Information Saved - Success</p>";
                }


                // save secondary Schools attended
                $attended12 = false;
                $ssaSuccess = true;
                $modelSSAs = $this->sessionForm['models']['modelSSA'];

                if ($modelSSAs && is_array($modelSSAs) && $modelEI) {
                    foreach ($modelSSAs as $SSAkey) {
                        $modelSSA = new SecondarySchoolAttended();

                        $modelSSA->ssa_ei_relId = $modelEI->id;
                        $modelSSA->ssa_school_name = $SSAkey['ssa_school_name'];
                        $modelSSA->ssa_town = $SSAkey['ssa_town'];
                        // $modelSSA->ssa_town = $SSAkey['ssa_town'];
                        // $modelSSA->eem_reference_number = $SSAkey['eem_reference_number'];
                        $modelSSA->ssa_year = intval($SSAkey['ssa_year']);
                        if (strtotime($modelSSA->ssa_year) === false) {
                            throw new ErrorException("Year of Secondary School Attended is Invalid, Please check Under Secondary Schools attended tab.", 1);
                        }
                        $modelSSA->ssa_gradeLevels = $SSAkey['ssa_gradeLevels'];
                        $modelSSA->ssa_created_at = date("Y-m-d h:i:s");


                        // var_dump($SSAkey['ssa_gradeLevels']);

                        if ($SSAkey['ssa_gradeLevels'] == 12 || $SSAkey['ssa_gradeLevels'] == '10+2') {
                            $attended12 = TRUE;
                        }

                        if ($modelSSA->save()) {
                            continue;
                        } else {
                            $ssaSuccess = false;
                            echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                            echo "Secondary Schools Information Creating failed, Please check under Education Information tab!";
                            echo "</pre>";
                            echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                            var_dump($modelSSA->getErrors());
                            echo "</pre></div>";
                            throw new ErrorException("Operatin halted", 1);
                        }
                    }
                    if (!$attended12) { //information about g12 or 10+2 is required for student copy
                        echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                        echo "Secondary Schools Information Creating failed, Grade levels [12 or 10+2] missing";
                        echo "</pre>";
                        echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                        var_dump($modelSSA->getErrors());
                        echo "</pre></div>";
                        throw new ErrorException("Operatin halted", 1);
                    }
                } else {
                    echo "<p class = 'bg-default'>Secondary Schools Information NOT available</p>";
                }
                if ($ssaSuccess && $attended12) {
                    echo "<p class = 'bg-success'>Secondary Schools Information Saved - Success</p>";
                }

                // save post secondary schools attended::
                $psseSuccess = TRUE;
                $psseRecordAvailable = false;
                $modelPSEs = isset($this->sessionForm['models']['modelPSE']) ? $this->sessionForm['models']['modelPSE'] : NULL;
                if ($modelPSEs && is_array($modelPSEs) && $modelPSEs[0] == 1 && $modelEI) {


                    $psseRecordAvailable = true;
                    foreach ($modelPSEs as $PSEkey) {
                        if (!is_array($PSEkey)) {
                            continue;
                        }
                        $modelPSE = new PostSecondaryEducation();

                        $modelPSE->pse_ei_relId = $modelEI->id;
                        $modelPSE->pse_institute_name = $PSEkey['pse_institute_name'];
                        $modelPSE->pse_nationality_relId = $PSEkey['pse_nationality_relId'];
                        $modelPSE->pse_credit_earned = $PSEkey['pse_credit_earned'];
                        $modelPSE->pse_CGPA_obtained = $PSEkey['pse_CGPA_obtained'];
                        $modelPSE->pse_certificate_earned = $PSEkey['pse_certificate_earned'];
                        $modelPSE->pse_from_year = intval($PSEkey['pse_from_year']);
                        $modelPSE->pse_to_year = intval($PSEkey['pse_to_year']);
                        if (strtotime($modelPSE->pse_from_year) === false || strtotime($modelPSE->pse_to_year) === false) {
                            throw new ErrorException("Year Invalid, Please check Under Post Secondary Schools attended tab.", 1);
                        }
                        // $modelPSE->eem_markObtained = $PSEkey['eem_markObtained'];
                        $modelPSE->pse_created_at = date("Y-m-d h:i:s");

                        if ($modelPSE->save()) {
                            continue;
                        } else {
                            $psseSuccess = false;
                            echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                            echo "Post secondary eduacation Information Creating failed, Please check under Education Background tab!";
                            echo "</pre>";
                            echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                            var_dump($modelPSE->getErrors());
                            echo "</pre></div>";
                            throw new ErrorException("Operatin halted", 1);
                        }
                    }
                }
                if ($psseSuccess && $psseRecordAvailable) {
                    echo "<p class = 'bg-success'>Post Secodary Education Information Saved - Success</p>";
                } else {
                    echo "<p class = 'bg-default'>Post Secodary Education Information - Info NOT available</p>";
                }

                // save addressEC

                $addressEC = $this->sessionForm['modelAddressEC'];
                $modelAddressEC = new AddressInfo();

                $modelAddressEC->address_country_relId = $this->sessionForm['modelAddressEC']['address_country_relId'];
                $modelAddressEC->address_region_relId = $this->sessionForm['modelAddressEC']['address_region_relId'];
                $modelAddressEC->address_woreda = $this->sessionForm['modelAddressEC']['address_woreda'];
                $modelAddressEC->address_kebele = $this->sessionForm['modelAddressEC']['address_kebele'];
                $modelAddressEC->address_hourse_no = $this->sessionForm['modelAddressEC']['address_hourse_no'];
                $modelAddressEC->address_town = $this->sessionForm['modelAddressEC']['address_town'];
                $modelAddressEC->address_zone = $this->sessionForm['modelAddressEC']['address_zone'];

                if ($modelAddressEC->save()) {
                    echo "<p class = 'bg-success'>Emergency Contact Address Information Saved - Success</p>";
                } else {
                    echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                    echo "Emergency Contact address Information saving failed, Please check under Emergency Contact Address Information !";
                    echo "</pre>";
                    echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                    var_dump($modelAddressEC->getErrors());
                    echo "</pre></div>";
                    throw new ErrorException("Operatin halted", 1);
                }


                // save modelEC
                $modelEC = $this->sessionForm['modelEC'];
                if ($modelEC && is_array($modelEC)) {

                    $modelEC = new EmergencyContact();

                    $modelEC->ec_first_name = $this->sessionForm['modelEC']['ec_first_name'];
                    $modelEC->ec_last_name = $this->sessionForm['modelEC']['ec_last_name'];
                    $modelEC->ec_relationShip = $this->sessionForm['modelEC']['ec_relationShip'];
                    $modelEC->ec_phone_home = $this->sessionForm['modelEC']['ec_phone_home'];
                    $modelEC->ec_phone_mobile = $this->sessionForm['modelEC']['ec_phone_mobile'];
                    $modelEC->ec_address_relId = $modelAddressEC->id;
                    $modelEC->ec_email = $this->sessionForm['modelEC']['ec_email'];

                    if ($modelEC->save()) {
                        echo "<p class = 'bg-success'>Emergency Contact Information Saved - Success</p>";
                    } else {
                        echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                        echo "Emergency Contact Information saving failed, Please check under Emergency Contact Information !";
                        echo "</pre>";
                        echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                        var_dump($modelEC->getErrors());
                        echo "</pre></div>";
                        throw new ErrorException("Operatin halted", 1);
                    }
                }
                // adl'alsfksd

                // student record at the last

                $sModel = $this->sessionForm['modelStudent'];
                // var_dump($sModel);die();
                if ($sModel && is_array($sModel)) {

                    $modelStudent = new Student1();
                    $modelStudent->std_user_relId = $modelUser->id;
                    $modelStudent->std_admission_relId = $sModel['std_admission_relId'];
                    $modelStudent->std_prog_relId = $sModel['std_prog_relId'];
                    // var_dump($sModel);exit;
                    // $modelStudent->std_admission_relId = ;
                    // $modelStudent->std_prog_relId = ;

                    $modelStudent->std_dept_relId = $sModel['std_dept_relId'];
                    $modelStudent->std_semester_status = "NOT APPROVED";
                    $modelStudent->std_regId = $modelStudent->getStdRegId($activeProgram->admission_relId, $activeProgram->program_relId, $modelStudent->std_dept_relId);
                    $this->__constructor();
                    $modelStudent->std_acadYear_relId = $sModel['std_acadYear_relId'];
                    $modelStudent->std_sem_relId = $sModel['std_sem_relId'];
                    $modelStudent->std_schlrshpType_relId = $sModel['std_schlrshpType_relId'];
                    $modelStudent->std_deptChoice = isset($sModel['std_deptChoice']) ? $sModel['std_deptChoice'] : json_encode([]);
                    $modelStudent->st_educ_info_relId =  $modelEI ? $modelEI->id : NULL;
                    $modelStudent->std_emergency_contact_relId = $modelEC->id;
                    $modelStudent->std_type = $sModel['std_type'];
                    $modelStudent->std_marital_status = $sModel['std_marital_status'];
                    $modelStudent->std_spec_relId = $sModel['std_spec_relId'];
                    $modelStudent->std_acProgSched_relId = $sModel['std_acProgSched_relId'];
                    // std_acProgSched_relId

                    $p = \backend\models\Program::findOne($modelStudent->std_prog_relId);
                    $c = \backend\models\ClassLevel::find()
                        ->where([
                            'clslvl_program_relId' => $modelStudent->std_prog_relId
                        ])
                        ->orderBy('clslvl_num')
                        ->all();
                    $modelStudent->std_clslvl_relId = $c[0]->id;

                    $modelStudent->std_created_at = date('Y-m-d h:i:s');

                    // var_dump($modelStudent);
                    // exit;

                    // $model->std_created_by = Yii::$app->user->identity->id;
                    if ($modelStudent->save()) {
                        $transaction->commit();
                        Yii::$app->session->setFlash('type', 'success');
                        // Yii::$app->session->setFlash('type','success');
                        Yii::$app->session->setFlash('message', 'Admission Request accepted for this academic year schedule');
                        return $this->redirect(['site/index']);
                        // echo "<p class = 'bg-success'>Student Account Created - Success</p>";
                        // echo "<div class = 'container bg-success wonde_message'> 
                        //         <div class='panel panel-success'>
                        //             <div class='panel-heading'><h2>Admission Form Accepted ! </h2></div>
                        //             <div class='panel-body'>
                        //                 <center>
                        //                     Your Admission form submitted successfully, </br>
                        //                     Please follow up approval status.<br>
                        //                 </center>
                        //                 <div class='container bg-info'>
                        //                     <p>Please click the OK button below to complete the operation!</p>
                        //                     <button class='btn btn-warning btn-sm' onclick='refreshFrom();'>OK</button>
                        //                 </div>
                        //             </div>
                        //         </div>
                        //     </div>";
                        // return;
                    } else {
                        // var_dump($modelStudent->errors);
                        echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                        echo "Student Academic Information Creating failed, Please check under Academic Information Information tab!";
                        echo "</pre>";
                        echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                        var_dump($modelStudent->errors);
                        echo "</pre></div>";
                        throw new ErrorException("Operation halted", 1);
                    }
                }
            } catch (ErrorException $e) {
                $transaction->rollBack();
                echo "<pre onclick = '$(\".wonde_message\").toggle(\"display\")'>";
                var_dump($e->getMessage());
                echo "</pre>";
                echo "<div class = 'container bg-danger wonde_message' style='display:none;'> <pre>";
                var_dump($e);
                echo "</pre></div>";
                return;
            }
        } else {
            die("Form Error");
        }
    }
    public function actionNewAdmission()
    {
        // for those student who have already signuped and want to admit on new program

        $prevAdmissionEntry = Student1::find()
            ->where(['std_user_relId' => Yii::$app->user->identity->id])
            ->all();

        return $this->render('new-admission', ['prevAdmissionEntry' => $prevAdmissionEntry]);
    }
    public function actionSelfAdmissionForm2()
    {
        if (isset($_POST)) {
            $modelName = NULL;
            if (isset(array_keys($_POST)[1])) {
                $modelName = array_keys($_POST)[1];
            }
            if (!is_null($modelName) && is_array($_POST[$modelName]) && isset(array_keys($_POST)[1])) {
                $modelName = array_keys($_POST)[1];
                switch ($modelName) {
                    case 'User1':
                        $this->sessionForm['modelUser'] = $_POST[$modelName];
                        break;
                    case 'Student1':
                        $this->sessionForm['modelStudent'] = $_POST[$modelName];
                        break;
                    case 'EmergencyContact':
                        $this->sessionForm['modelEC'] = $_POST[$modelName];
                        break;
                    case 'AddressInfo':

                        // address indicator -
                        //  1=> place of birth
                        //  2=> current place of residential
                        //  1=> place of birth of Emergency contact

                        if (isset($_POST[$modelName]->indicator) && $_POST[$modelName]->indicator == 1) {
                            $this->sessionForm['modelAddressBirth'] = $_POST[$modelName];
                        } elseif (isset($_POST[$modelName]->indicator) && $_POST[$modelName]->indicator == 2) {
                            $this->sessionForm['modelAddressRedencial'] = $_POST[$modelName];
                        } else if (isset($_POST[$modelName]->indicator) && $_POST[$modelName]->indicator == 3) {
                            $this->sessionForm['modelAddressEC'] = $_POST[$modelName];
                        }

                        break;
                    case 'EducationInformation':
                        $this->sessionForm['models']['modelEI'] = $_POST[$modelName];
                        break;
                    case 'PostSecondaryEducation':
                        $this->sessionForm['models']['modelPSE'] = $_POST[$modelName];
                        break;
                    case 'SecondarySchoolAttended':
                        $this->sessionForm['models']['modelSSA'] = $_POST[$modelName];
                        break;
                    case 'EnteranceExamMark':
                        $this->sessionForm['models']['modelEEM'] = $_POST[$modelName];
                        break;

                    default:
                        var_dump('Uknown Form');
                        break;
                }

                echo "<pre>";
                var_dump($this->sessionForm);
                die();
                return $this->renderPartial(($_POST['models']['programId'] == 1 ? 'index2' : 'index2_pg'), ['models' => $this->sessionForm]);;
            }
        }
        // 
        $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : die('Invalid request');
        $v2 = isset($_POST['v2']) && !empty($_POST['v2']) ? intval($_POST['v2']) : NULL;
        $settingId = isset($_POST['settingId']) && !empty($_POST['settingId']) ? intval($_POST['settingId']) : NULL;

        $studentModel = new Student1();
        $user = new User1();

        if (!Yii::$app->user->isGuest) {
            $user = User1::findOne(Yii::$app->user->id);
            $studentModel->std_user_relId = $user->id;
        } else {
            $user = new User1();
        }

        if ($user->getStudents1()->andFilterWhere(['std_acProgSched_relId' => $settingId])->count() > 0) {
            return "adminssion already requested for this schedule";
        }

        // $settingEntry = \backend\models\AcademicCalander::findOne($settingId);
        $settingEntry = \frontend\modules\AcademicCalendar\models\AcProgramSchedule::findOne($settingId);

        $program = \backend\models\Program::findOne($id);

        if ($settingEntry) {
            $activeProgram = $settingEntry->activeProgramRel;
            $studentModel->std_admission_relId = $activeProgram->admission_relId;
            $studentModel->std_prog_relId = $activeProgram->program_relId;

            $studentModel->std_acadYear_relId = $activeProgram->academicYearRel->schoolYearRel->id;
            $studentModel->std_sem_relId = $activeProgram->academicYearRel->semesterRel->id;
            $studentModel->std_clslvl_relId = $activeProgram->class_level_relId;
            $studentModel->std_dept_relId = $activeProgram->dept_relId;
            $studentModel->std_spec_relId = $activeProgram->spec_relId;
            $studentModel->std_acProgSched_relId = $settingEntry->id;

            if ($studentModel->std_prog_relId) {
                $id = strtoupper($studentModel->stdProgRel->program_name) == 'MASTERS' ? 2 : 1;
            }
        } else {
            return "Schedule entry not found";
        }
        if (!is_null($v2) && $id == 1) { //Ug-form V.2
            // version 2 case
            $this->sessionForm = [
                'uniqueApplicationId' => uniqid(),
                'programId' => $program->id,
                'models' => [
                    'modelUser' => $user,
                    'modelStudent' => $studentModel,
                    'modelEC' => new EmergencyContact(),
                    'modelAddressBirth' => new AddressInfo(),
                    'modelAddressRedencial' => new AddressInfo(),
                    'modelAddressEC' => new AddressInfo(),
                    'modelEI' => [
                        'modelEI' => new EducationInformation(),
                        'modelPSE' => [new PostSecondaryEducation()],
                        'modelSSA' => [new SecondarySchoolAttended()],
                        'modelEEM' => [new EnteranceExamMark],
                    ],
                ],
            ];
            return $this->renderPartial(($id == 1 ? 'index2' : '../admit-student-pg/index2'), ['models' => $this->sessionForm]);
        }
        if ($id == 1 || 0) { //UG case
            $modelUser = $user;
            $modelStudent = new Student1();
            $emergencyContact = new EmergencyContact();
            $modelAddress = new AddressInfo();
            $modelPSE = [new PostSecondaryEducation()];
            $modelEI = new EducationInformation();
            $modelSSA = [new SecondarySchoolAttended()];
            $modelEEM = [new EnteranceExamMark()];
            return $this->renderAjax(
                'index',
                [
                    'modelStudent' => $modelStudent,
                    'modelUser' => $modelUser,
                    'emergencyContact' => $emergencyContact,
                    'modelAddress' => $modelAddress,
                    'modelPSE' => $modelPSE,
                    'modelEI' => $modelEI,
                    'modelSSA' => $modelSSA,
                    'modelEEM' => $modelEEM,
                ]
            );
        } else if ($id == 2) { // PG Admission Form
            $this->sessionForm = [
                'uniqueApplicationId' => uniqid(),
                'programId' => $program->id,
                'models' => [
                    'modelUser' => $user,
                    'modelStudent' => $studentModel,
                    'modelEC' => new EmergencyContact(),
                    'modelAddressEC' => new AddressInfo(),
                    'modelEI' => [
                        'modelEI' => new PgEducationInfo(),
                        'modelPSE' => [new PgPostSecondarySchool()],
                        'modelSSA' => [new PgSecondarySchoolsAttended()],
                    ],
                ],
            ];
            return $this->renderAjax(
                '/admit-student-pg/index2',
                [
                    'models' => $this->sessionForm,
                ]
            );
        }
    }

    public function actionSelfAdmissionForm()
    {

        if (isset($_POST)) {
            $modelName = NULL;
            if (isset(array_keys($_POST)[1])) {
                $modelName = array_keys($_POST)[1];
            }
            if (!is_null($modelName) && is_array($_POST[$modelName]) && isset(array_keys($_POST)[1])) {
                $modelName = array_keys($_POST)[1];
                switch ($modelName) {
                    case 'User1':
                        $this->sessionForm['modelUser'] = $_POST[$modelName];
                        break;
                    case 'Student1':
                        $this->sessionForm['modelStudent'] = $_POST[$modelName];
                        break;
                    case 'EmergencyContact':
                        $this->sessionForm['modelEC'] = $_POST[$modelName];
                        break;
                    case 'AddressInfo':

                        // address indicator -
                        //  1=> place of birth
                        //  2=> current place of residential
                        //  1=> place of birth of Emergency contact

                        if (isset($_POST[$modelName]->indicator) && $_POST[$modelName]->indicator == 1) {
                            $this->sessionForm['modelAddressBirth'] = $_POST[$modelName];
                        } elseif (isset($_POST[$modelName]->indicator) && $_POST[$modelName]->indicator == 2) {
                            $this->sessionForm['modelAddressRedencial'] = $_POST[$modelName];
                        } else if (isset($_POST[$modelName]->indicator) && $_POST[$modelName]->indicator == 3) {
                            $this->sessionForm['modelAddressEC'] = $_POST[$modelName];
                        }

                        break;
                    case 'EducationInformation':
                        $this->sessionForm['models']['modelEI'] = $_POST[$modelName];
                        break;
                    case 'PostSecondaryEducation':
                        $this->sessionForm['models']['modelPSE'] = $_POST[$modelName];
                        break;
                    case 'SecondarySchoolAttended':
                        $this->sessionForm['models']['modelSSA'] = $_POST[$modelName];
                        break;
                    case 'EnteranceExamMark':
                        $this->sessionForm['models']['modelEEM'] = $_POST[$modelName];
                        break;

                    default:
                        var_dump('Uknown Form');
                        break;
                }

                echo "<pre>";
                var_dump($this->sessionForm);
                die();
                return $this->renderPartial(($_POST['models']['programId'] == 1 ? 'index2' : 'index2_pg'), ['models' => $this->sessionForm]);;
            }
        }
        // 
        $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : die('Invalid request');
        $otp = isset($_POST['otp']) && !empty($_POST['otp']) ? (string)$_POST['otp'] : die('Invalid request, OTP Uknown');
        $v2 = isset($_POST['v2']) && !empty($_POST['v2']) ? intval($_POST['v2']) : NULL;
        $settingId = isset($_POST['settingId']) && !empty($_POST['settingId']) ? intval($_POST['settingId']) : NULL;

        $studentModel = new Student1();
        $user = new User1();

        if (!Yii::$app->user->isGuest) {
            $user = User1::findOne(Yii::$app->user->identity->id);
        } else {
            $user = User1::findOne(['OTP' => $otp]);
        }

        if (is_null($user)) {
            return "Such Information is not found!";
        }

        $settingEntry = \frontend\modules\AcademicCalendar\models\AcProgramSchedule::findOne($settingId);
        $activeProgram = $settingEntry->activeProgramRel;

        // check if student already filled admission request
        if ($user->getStudents1()->andFilterWhere([
            'std_acadYear_relId' => $activeProgram->academicYearRel->schoolYearRel->id,
            'std_sem_relId' => $activeProgram->academicYearRel->semesterRel->id
        ])->count() > 0) {
            Yii::$app->session->setFlash('type', 'warning');
            Yii::$app->session->setFlash('message', 'Admission Request accepted for this academic year schedule');
            return $this->redirect(['site/index']);
            // return "admission already requested for this schedule";
        }

        // $program = \backend\models\Program::findOne($id);

        if ($settingEntry) {
            $activeProgram = $settingEntry->activeProgramRel;
            $studentModel->std_admission_relId = $activeProgram->admission_relId;
            $studentModel->std_prog_relId = $activeProgram->program_relId;

            $studentModel->std_acadYear_relId = $activeProgram->academicYearRel->schoolYearRel->id;
            $studentModel->std_sem_relId = $activeProgram->academicYearRel->semesterRel->id;
            $studentModel->std_clslvl_relId = $activeProgram->class_level_relId;
            $studentModel->std_dept_relId = $activeProgram->dept_relId;
            $studentModel->std_spec_relId = $activeProgram->spec_relId;
            $studentModel->std_acProgSched_relId = $settingEntry->id;

            if ($studentModel->std_prog_relId) {
                $id = strtoupper($studentModel->stdProgRel->program_name) == 'MASTERS' ? 2 : 1;
            }
        } else {
            return "Schedule entry not found";
        }
        if (!is_null($v2) && $id == 1) { //Ug-form V.2
            // version 2 case
            $this->sessionForm = [
                'uniqueApplicationId' => uniqid(),
                'programId' => $studentModel->std_prog_relId,
                'models' => [
                    'modelUser' => $user,
                    'modelStudent' => $studentModel,
                    'modelEC' => new EmergencyContact(),
                    'modelAddressBirth' => new AddressInfo(),
                    'modelAddressRedencial' => new AddressInfo(),
                    'modelAddressEC' => new AddressInfo(),
                    'modelEI' => [
                        'modelEI' => new EducationInformation(),
                        'modelPSE' => [new PostSecondaryEducation()],
                        'modelSSA' => [new SecondarySchoolAttended()],
                        'modelEEM' => [new EnteranceExamMark],
                    ],
                ],
            ];
            return $this->renderPartial(($id == 1 ? 'index2' : '../admit-student-pg/index2'), ['models' => $this->sessionForm]);
        }
        if ($id == 1 || 0) { //UG case
            $modelUser = $user;
            $modelStudent = new Student1();
            $emergencyContact = new EmergencyContact();
            $modelAddress = new AddressInfo();
            $modelPSE = [new PostSecondaryEducation()];
            $modelEI = new EducationInformation();
            $modelSSA = [new SecondarySchoolAttended()];
            $modelEEM = [new EnteranceExamMark()];
            return $this->renderAjax(
                'index',
                [
                    'modelStudent' => $modelStudent,
                    'modelUser' => $modelUser,
                    'emergencyContact' => $emergencyContact,
                    'modelAddress' => $modelAddress,
                    'modelPSE' => $modelPSE,
                    'modelEI' => $modelEI,
                    'modelSSA' => $modelSSA,
                    'modelEEM' => $modelEEM,
                ]
            );
        } else if ($id == 2) { // PG Admission Form
            $this->sessionForm = [
                'uniqueApplicationId' => uniqid(),
                'programId' => $activeProgram->program_relId,
                'models' => [
                    'modelUser' => $user,
                    'modelStudent' => $studentModel,
                    'modelEC' => new EmergencyContact(),
                    'modelAddressEC' => new AddressInfo(),
                    'modelEI' => [
                        'modelEI' => new PgEducationInfo(),
                        'modelPSE' => [new PgPostSecondarySchool()],
                        'modelSSA' => [new PgSecondarySchoolsAttended()],
                    ],
                ],
            ];
            return $this->renderAjax(
                '/admit-student-pg/index2',
                [
                    'models' => $this->sessionForm,
                ]
            );
        }
    }

    private function saveModels() {}

    public function actionIndex()
    {
        if (Yii::$app->user->can('Admin') || Yii::$app->user->can('clerk') || Yii::$app->user->can('Registrar')) {
            $modelUser = new User1();
            $modelStudent = new Student1();
            $emergencyContact = new EmergencyContact();
            $modelAddress = new AddressInfo();
            $modelPSE = [new PostSecondaryEducation()];
            $modelEI = new EducationInformation();
            $modelSSA = [new SecondarySchoolAttended()];
            $modelEEM = [new EnteranceExamMark()];
            return $this->render(
                'index',
                [
                    'modelStudent' => $modelStudent,
                    'modelUser' => $modelUser,
                    'emergencyContact' => $emergencyContact,
                    'modelAddress' => $modelAddress,
                    'modelPSE' => $modelPSE,
                    'modelEI' => $modelEI,
                    'modelSSA' => $modelSSA,
                    'modelEEM' => $modelEEM,
                ]
            );
        }
        return $this->render('index_another', ['error, You have no permission for this action']);
    }

    public function actionPg()
    { //render for PG admission
        if (Yii::$app->user->can('Admin') || Yii::$app->user->can('clerk') || Yii::$app->user->can('Registrar')) {
            $modelUser = new User1();
            $modelStudent = new Student1();
            $emergencyContact = new EmergencyContact();
            $modelAddress = new AddressInfo();
            $modelPSE = [new PgPostSecondarySchool()];
            $modelEI = new PgEducationInfo();
            $modelSSA = [new PgSecondarySchoolsAttended()];
            $modelEEM = [new EnteranceExamMark()];
            return $this->render(
                'index_PG',
                [
                    'modelStudent' => $modelStudent,
                    'modelUser' => $modelUser,
                    'emergencyContact' => $emergencyContact,
                    'modelAddress' => $modelAddress,
                    'modelPSE' => $modelPSE,
                    'modelEI' => $modelEI,
                    'modelSSA' => $modelSSA,
                    'modelEEM' => $modelEEM,
                ]
            );
        }
    }

    public function actionUploadPhoto()
    {
        $model = new User1();

        if ($model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');

            // var_dump($model);die();

            try {
                $userId = intval($_POST['User1']['id_for_file']);
                $user = User1::findOne($userId);

                if ($user) {

                    $fileName = md5($user->first_name . '_' . $user->middle_name[0] . '_' . date('Y_m_d@h_i'));

                    $model->file->saveAs('uploads/profileImages/' . $fileName . '.' . $model->file->extension);

                    // dump into the bd column.....

                    $result = Yii::$app->db->createCommand()
                        ->update(
                            'user',
                            [
                                'photo_num' => 'profileImages/' . $fileName . '.' . $model->file->extension,
                            ],
                            [
                                'id' => $userId,
                            ]
                        )
                        ->execute();
                    $model = NULL;
                    return $this->redirect('documents');
                } else {
                    echo "<p class='bg-danger'>Error with your entry, Please re-try.</p>";
                    die();
                }
            } catch (\Exception $e) {
                print_r($e->getMessage());
                die();
            }
        }
    }

    public function actionGetUserImage()
    {
        $userId = isset($_POST['userId']) && !empty($_POST['userId']) ? intval($_POST['userId']) : die('Uknown User Id.');

        $user = User1::findOne($userId);

        if ($user) {
            echo "{$user->photo_num}";
            die();
        } else {
            echo "<p class='bg-danger'>No such record found!.</p>";
            die();
        }
    }
    public function actionGetUserById()
    {
        // 
        $userId = isset($_POST['userId']) && !empty($_POST['userId']) ? $_POST['userId'] : die('Empty search key, Please enter one.');
        // first check student ID :

        $std = Student1::find()->where(['std_regId' => $userId])->all();
        $table = NULL;
        if ($std && count($std) > 0) {
            $table = "<table class='table table-striped table-condensed table-hover table-sm' style='font-size:9pt;'><tbody>";
            $count = 0;

            foreach ($std as $key) {
                $user = User1::findOne($key->std_user_relId);

                $btnnn = NULL;
                if (isset($_POST['t'])) {
                    $btnnn = "<button class='btn btn-xs btn-primary pull-right' onclick='loadrequestForm($user->id)'>Load Form</button>";
                }

                if (isset($_POST['ch'])) {
                    $btnnn = "<button class='btn btn-xs btn-danger pull-right' title='Reset Password of this user?' onclick='resetPassword($user->id)'>Resset</button>";
                } else {
                    $btnnn = "<button class='btn btn-xs btn-primary pull-right' onclick='loadImagePRE($user->id)'>Upload / Change</button>";
                }

                $table .= "<tr>
                            <td>" . ++$count . "</td>
                            <td>{$key->std_regId}</td>
                            <td>{$user->first_name} {$user->middle_name}</td>
                            <td>{$btnnn}</td>
                        </tr>";
            }

            $table .= "</tbody></table>";
            echo "<span style='font-style:italic' class='bg-success'>Found <b>{$count}</b> record(s).</span>";
            echo "{$table}";
            die();
        } else {
            // look for user firstname and lastname

            $user = NULL;
            if (isset($_POST['t'])) {
                // get HOD dept.
                if (Yii::$app->user->can('DepartmentHead')) {
                    $instDept = \backend\models\Instructor::find()->where(['inst_user_relId' => Yii::$app->user->identity->id])->one()->inst_dep_relId;
                    $user = User1::find()
                        ->where(['like', 'first_name', $userId])
                        ->orWhere(['like', 'middle_name', $userId])
                        ->andWhere(['student.std_dept_relId' => $instDept])
                        ->innerJoin('student', 'student.std_user_relId = user.id')
                        ->groupBy('id')
                        ->all();
                } else {
                    $user = User1::find()
                        ->where(['like', 'first_name', $userId])
                        ->orWhere(['like', 'middle_name', $userId])
                        ->innerJoin('student', 'student.std_user_relId = user.id')
                        ->groupBy('id')
                        ->all();
                }
            } else {
                $user = User1::find()
                    ->where(['like', 'first_name', $userId])
                    ->orWhere(['like', 'middle_name', $userId])
                    ->groupBy('id')
                    ->all();
            }

            if ($user && count($user) > 0) {
                $table = "<table class='table table-striped table-condensed table-hover table-sm' style='font-size:9pt;'><tbody>";
                $count = 0;

                foreach ($user as $key) {
                    // $user = \backend\models\User1::findOne($key->std_user_relId);
                    $btnnn = NULL;
                    if (isset($_POST['t'])) {
                        $btnnn = "<button class='btn btn-xs btn-primary pull-right' onclick='loadrequestForm($key->id)'>Load Form</button>";
                    } else {
                        if (isset($_POST['ch'])) {
                            $btnnn = "<button class='btn btn-xs btn-danger pull-right'  title='Reset Password of this user?' onclick='resetPassword($key->id)'>Reset</button>";
                        } else {
                            $btnnn = "<button class='btn btn-xs btn-primary pull-right' onclick='loadImagePRE($key->id)'>Upload / Change</button>";
                        }
                    }


                    $table .= "<tr>
                                <td>" . ++$count . "</td>
                                <td>{$key->first_name} {$key->middle_name}</td>
                                <td>{$btnnn}</td>
                            </tr>";
                }

                $table .= "</tbody></table>";
                echo "<span style='font-style:italic' class='bg-success'>Found <b>{$count}</b> record(s).</span>";
                echo "{$table}";
                die();
            } else {
                echo "<p class='bg-danger'> No such record found.</p>";
                die();
            }
        }
    }

    public function actionDocuments()
    {
        $modelUser = new User1();
        $modelStudent = new Student1();
        return $this->render('documentUploads', ['model' => $modelUser]);
    }

    public function actionAdmissionFormReport($id = NULL)
    { // optional user ID
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            $id = intval($_GET['id']);
        }
        if (Yii::$app->user->can('Student')) {
            $modelUser = \common\models\User1::findone(Yii::$app->user->identity->id);
            $modelStudent = \backend\models\Student::findOne(['std_user_relId' => $modelUser->id]);
            return $this->renderTT($modelStudent, $modelUser);
        } else {
            if (!is_null($id)) {
                $id = intval($id);
                $modelUser = \common\models\User1::findone($id);
                $modelStudent = \backend\models\Student::findOne(['std_user_relId' => $modelUser->id]);
                return $this->renderTT($modelStudent, $modelUser);
            } else {
                throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
            }
        }
    }

    private function renderTT($modelStudent, $modelUser)
    {
        $modelAddressBirth = is_null($modelUser->place_of_birth_address_relId) ? $modelUser->place_of_birth_address_relId : \backend\models\AddressInfo::findOne($modelUser->place_of_birth_address_relId);
        $modelAddressRedencial = is_null($modelUser->place_of_residence_address_relId) ? $modelUser->place_of_residence_address_relId : \backend\models\AddressInfo::findOne($modelUser->place_of_residence_address_relId);
        $modelEC = is_null($modelStudent->std_emergency_contact_relId) ? $modelStudent->std_emergency_contact_relId : \backend\models\EmergencyContact::findOne($modelStudent->std_emergency_contact_relId);
        $modelAddressEC = is_null($modelEC) ? NULL : \backend\models\AddressInfo::findOne($modelEC->ec_address_relId);

        $admission = \backend\models\Admission::findOne($modelStudent->std_admission_relId);
        $program = \backend\models\Program::findOne($modelStudent->std_prog_relId);

        $programName = strtoupper($program->program_name); //MASTERS || DEGREE || 

        $modelSSA = NULL;
        $modelPSE = NULL;
        $modelEEM = NULL;

        // var_dump($programName);die();
        if ($programName == 'MASTERS') {
            // var_dump('eerer');die();
            // throw new \yii\web\NotFoundHttpException('Application report NOT completed for PG students.');
            $modelEI = is_null($modelStudent->st_educ_info_relId) ? $modelStudent->st_educ_info_relId : \backend\models\PgEducationInfo::findOne($modelStudent->st_educ_info_relId);
            $modelSSA = is_null($modelEI) ? NULL : \backend\models\PgSecondarySchoolsAttended::find()
                ->where(['pssa_pei_relId' => $modelEI->id])
                ->all();
            $modelPSE = is_null($modelEI) ? NULL : \backend\models\PgPostSecondarySchool::find()
                ->where(['ppss_pei_relId' => $modelEI->id])
                ->all();
            return $this->renderAjax(
                'index_report_pg',
                [
                    'modelUser' => $modelUser,
                    'modelStudent' => $modelStudent,
                    'modelAddressEC' => $modelAddressEC,
                    'modelEI' => $modelEI,
                    'modelSSA' => $modelSSA,
                    'modelPSE' => $modelPSE,
                    'modelEC' => $modelEC,
                ]
            );
        } else {

            $modelEI = is_null($modelStudent->st_educ_info_relId) ? $modelStudent->st_educ_info_relId : \backend\models\EducationInformation::findOne($modelStudent->st_educ_info_relId);
            $modelSSA = is_null($modelEI) ? NULL : \backend\models\SecondarySchoolAttended::find()
                ->where(['ssa_ei_relId' => $modelEI->id])
                ->all();
            $modelPSE = is_null($modelEI) ? NULL : \backend\models\PostSecondaryEducation::find()
                ->where(['pse_ei_relId' => $modelEI->id])
                ->all();
            $modelEEM = is_null($modelEI) ? NULL : \backend\models\EnteranceExamMark::find()
                ->where(['eem_ei_relId' => $modelEI->id])
                ->all();
        }

        return $this->renderAjax(
            'index_report',
            [
                'modelUser' => $modelUser,
                'modelStudent' => $modelStudent,
                'modelAddressBirth' => $modelAddressBirth,
                'modelAddressEC' => $modelAddressEC,
                'modelEI' => $modelEI,
                'modelSSA' => $modelSSA,
                'modelPSE' => $modelPSE,
                'modelEEM' => $modelEEM,
                'modelEC' => $modelEC,
                'modelAddressRedencial' => $modelAddressRedencial,
            ]
        );
    }


    public function actionCreate()
    {
        if (Yii::$app->user->can('Admin')) {

            $modelUser = new User1();
            $modelStudent = new Student1();
            $emergencyContact = new EmergencyContact();
            $modelAddress = new AddressInfo();
            // $modelPSE = [new PostSecondaryEducation()];
            $modelPSE = (empty($modelPSE)) ? [new PostSecondaryEducation] : $modelPSE;
            $modelSSA = [new SecondarySchoolAttended()];
            $modelEEM = [new EnteranceExamMark()];


            // echo "<pre>";
            // print_r($_POST['formList']);
            // die();


            $forms = json_decode($_POST['formList']);

            $transaction = Yii::$app->db->beginTransaction();
            $modelError = NULL;
            // first validate and save modelEI
            try {

                $eiId = NULL;
                $modelEI = isset($forms) ? $forms->modelEI : die('Invalid form submission');

                $model = new EducationInformation();

                $params = [];
                parse_str($modelEI, $params);
                if (empty($params)) {
                    throw new \Exception("Form not completed. Please complete it.", 1);
                }
                $model->load($params);

                $model->ei_enteranceMarkPoint = intval($model->ei_enteranceMarkPoint);
                $model->ei_created_at = date('Y-m-d h:i:d');
                $model->ei_created_by = Yii::$app->user->identity->id;

                if ($model->save()) {
                    $eiId = $model->id;

                    if (isset($forms)) {
                        $modelSSA = $forms->modelSSA;
                    } else {
                        throw new \Exception("Invalid form submission", 1);
                    }


                    $message = $this->saveModelSSA($modelSSA, $eiId);
                    // $message = is_null($message);
                    throw new \Exception("{$message}", 1);

                    if ($message == TRUE) {

                        throw new \Exception("Doing well toooo,... SSA save success. PROCEED", 1);
                    } else {
                        throw new \Exception("Error, Saving Secondary School Attended Information Saving failed. Pleae try again. ERROR_XX [{$message}}", 1);
                    }
                } else {
                    $modelError = $model->getErrors();

                    throw new \Exception("What is that? ERROR?", 1);
                }
            } catch (\Exception $e) {
                $transaction->rollBack();
                echo "<p class='bg-danger'>{$e->getMessage()}</p>";
                // var_dump($modelError);
                die();
            }
        }
        return $this->render('index_another', ['error, You have no permission for this action']);
    }

    public function actionRollBack()
    {
        // rolls back the opearations mistaken.

        $actionId = $_POST['actionId'];
        $ids = $_POST['ids'];

        if ($actionId == 'modelEI') {
            if (is_array($ids)) {
                foreach ($ids as $key) {
                    EducationInformation::findOne($key)->delete();
                }
            } else {
                EducationInformation::findOne($ids)->delete();
            }
        } else if ($actionId == 'modelAddress') {
            if (is_array($ids)) {
                foreach ($ids as $key) {
                    AddressInfo::findOne($key)->delete();
                }
            } else {
                AddressInfo::findOne($ids)->delete();
            }
        } else if ($actionId == 'modelEC') {
            if (is_array($ids)) {
                foreach ($ids as $key) {
                    EmergencyContact::findOne($key)->delete();
                }
            } else {
                EmergencyContact::findOne($ids)->delete();
            }
        } else if ($actionId == 'modelUser') {
            if (is_array($ids)) {
                foreach ($ids as $key) {
                    User1::findOne($key)->delete();
                }
            } else {
                User1::findOne($ids)->delete();
            }
        } else if ($actionId == 'modelStudent') {
            foreach ($ids as $key) {
                Student1::findOne($ids)->delete();
            }
        }
    }

    public function actionRollBackPg()
    {
        // rolls back the opearations mistaken.

        $actionId = $_POST['actionId'];
        $ids = $_POST['ids'];

        if ($actionId == 'modelEI') {
            if (is_array($ids)) {
                foreach ($ids as $key) {
                    PgEducationInfo::findOne($key)->delete();
                }
            } else {
                PgEducationInfo::findOne($ids)->delete();
            }
        } else if ($actionId == 'modelAddress') {
            if (is_array($ids)) {
                foreach ($ids as $key) {
                    AddressInfo::findOne($key)->delete();
                }
            } else {
                AddressInfo::findOne($ids)->delete();
            }
        } else if ($actionId == 'modelEC') {
            if (is_array($ids)) {
                foreach ($ids as $key) {
                    EmergencyContact::findOne($key)->delete();
                }
            } else {
                EmergencyContact::findOne($ids)->delete();
            }
        } else if ($actionId == 'modelUser') {
            if (is_array($ids)) {
                foreach ($ids as $key) {
                    User1::findOne($key)->delete();
                }
            } else {
                User1::findOne($ids)->delete();
            }
        } else if ($actionId == 'modelStudent') {
            foreach ($ids as $key) {
                Student1::findOne($ids)->delete();
            }
        }
    }


    public function actionCreateModelEI()
    {
        $model = new EducationInformation();

        if ($model->load(Yii::$app->request->post())) {

            $model->ei_created_at = date('Y-m-d h:i:s');
            // $model->ei_created_by = Yii::$app->user->identity->id;

            if ($model->save()) {
                echo json_encode([1, $model->id]);
            } else {
                $modelErrors = $model->getErrors();
                if (isset($modelErrors['ei_enteranceMarkPoint'])) {
                    echo "<p class='bg-danger'>{$modelErrors['ei_enteranceMarkPoint'][0]}</p>";
                } else {
                    print_r($modelErrors);
                }
                die();
            }
            //  && $model->save()
            // return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionValidateUser()
    {
        $model = new User1();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }

    public function actionValidateStudent()
    {
        $model = new Student1();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
    }
}
