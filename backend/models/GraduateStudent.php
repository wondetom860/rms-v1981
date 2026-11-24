<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "graduate_student".
 *
 * @property integer $id
 * @property integer $gstd_user_relId
 * @property integer $gstd_adm_relId
 * @property integer $gstd_prog_relId
 * @property integer $gstd_dept_relId
 * @property integer $gstd_spec_relId
 * @property integer $gstd_sponsorship_relId
 * @property string $gstd_adm_year
 * @property string $gstd_grad_year
 * @property string $gstd_regId
 * @property double $gstd_CGPA
 * @property string $gstd_date_of_graduation
 * @property integer $gstd_doc_collected
 * @property string $gstd_doc_collected_at
 * @property integer $gsdt_prepared_by
 * @property integer $gstd_profession_category
 * @property integer $gstd_rank
 * @property integer $gstd_doc_signed_by
 * @property string $gstd_doc_signed_at
 * @property integer $gstd_doc_signed
 *
 * @property integer $gstd_prev_CGPA
 * @property integer $gstd_enterance_exam
 * @property integer $gstd_created_at
 * @property integer $gstd_created_by
 * @property integer $gstd_updated_at
 * @property integer $gstd_updated_by
 *
 * @property User $gsdtPreparedBy
 * @property Admission $gstdAdmRel
 * @property Department $gstdDeptRel
 * @property User $gstdDocSignedBy
 * @property Program $gstdProgRel
 * @property Specialization $gstdSpecRel
 * @property ScholarshipType $gstdSponsorshipRel
 * @property User $gstdUserRel
 */
class GraduateStudent extends \yii\db\ActiveRecord
{
    public $fullname = "";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'graduate_student';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gstd_user_relId', 'gstd_adm_relId', 'gstd_grad_year', 'gstd_prog_relId', 'gstd_dept_relId', 'gstd_sponsorship_relId', 'gstd_adm_year', 'gstd_CGPA', 'gstd_regId', 'gstd_date_of_graduation', 'gstd_profession_category'], 'required'],
            [['gstd_user_relId', 'gstd_adm_relId', 'gstd_prog_relId', 'gstd_dept_relId', 'gstd_spec_relId', 'gstd_sponsorship_relId', 'gstd_doc_collected', 'gsdt_prepared_by', 'gstd_doc_signed_by', 'gstd_doc_signed', 'gstd_updated_by', 'gstd_created_by'], 'integer'],
            [['gstd_CGPA', 'gstd_prev_CGPA', 'gstd_enterance_exam'], 'number'],
            [['gstd_date_of_graduation', 'gstd_doc_collected_at', 'gstd_doc_signed_at'], 'safe'],
            [['gstd_adm_year', 'gstd_grad_year'], 'string', 'max' => 40],
            [['gstd_regId'], 'string', 'max' => 15],
            [['gstd_rank', 'gstd_profession_category'], 'string', 'max' => 15],
            [['gsdt_prepared_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['gsdt_prepared_by' => 'id']],
            [['gstd_adm_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['gstd_adm_relId' => 'id']],
            [['gstd_dept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['gstd_dept_relId' => 'id']],
            [['gstd_doc_signed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['gstd_doc_signed_by' => 'id']],
            [['gstd_prog_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['gstd_prog_relId' => 'id']],
            [['gstd_spec_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Specialization::class, 'targetAttribute' => ['gstd_spec_relId' => 'id']],
            [['gstd_sponsorship_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ScholarshipType::class, 'targetAttribute' => ['gstd_sponsorship_relId' => 'id']],
            [['gstd_user_relId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['gstd_user_relId' => 'id']],
            [['gstd_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['gstd_created_by' => 'id']],
            [['gstd_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['gstd_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'gstd_user_relId' => Yii::t('rms', 'Full Name'),
            'gstd_adm_relId' => Yii::t('rms', 'Admission'),
            'gstd_regId' => Yii::t('rms', 'Reg. Id'),
            'gstd_rank' => Yii::t('rms', 'Rank'),
            'gstd_profession_category' => Yii::t('rms', 'Prof. Category'),
            'gstd_prog_relId' => Yii::t('rms', 'Program'),
            'gstd_dept_relId' => Yii::t('rms', 'Department'),
            'gstd_spec_relId' => Yii::t('rms', 'Specialization'),
            'gstd_sponsorship_relId' => Yii::t('rms', 'Sponsorship'),
            'gstd_adm_year' => Yii::t('rms', 'Admission Year'),
            'gstd_grad_year' => Yii::t('rms', 'Year of Graduation'),
            'gstd_CGPA' => Yii::t('rms', 'CGPA'),
            'gstd_prev_CGPA' => Yii::t('rms', 'Prev. CGPA'),
            'gstd_enterance_exam' => Yii::t('rms', 'Enterance Exam Mark'),
            'gstd_date_of_graduation' => Yii::t('rms', 'Date Of Graduation'),
            'gstd_doc_collected' => Yii::t('rms', 'Document Collected'),
            'gstd_doc_collected_at' => Yii::t('rms', 'Document Collected At'),
            'gsdt_prepared_by' => Yii::t('rms', 'Document Prepared By'),
            'gstd_doc_signed_by' => Yii::t('rms', 'Document Signed By'),
            'gstd_doc_signed_at' => Yii::t('rms', 'Document Signed At'),
            'gstd_doc_signed' => Yii::t('rms', 'Document Signed'),
        ];
    }


    public function getGraduationCGPA()
    {
        $lastCGPA = $this->getGraduateStudentPointHistories()->orderBy(['gph_registration_entry_relId' => SORT_DESC])->all()[0];

        if ($this->gstd_CGPA != $lastCGPA->gph_CGPA) {
            $this->gstd_CGPA = $lastCGPA->gph_CGPA;
            $this->save();
        }

        return $this->gstd_CGPA;
    }

    public function generateRegistrarRequestAlert()
    {

        return $this->find()->where(['gstd_approved_by' => NULL])->count();
    }

    public function getUserRole2($userId)
    {
        $r = array_keys(Yii::$app->authManager->getRolesByUser($userId));
        return isset($r[0]) ? $r[0] : '';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGsdtPreparedBy()
    {
        return $this->hasOne(User::class, ['id' => 'gsdt_prepared_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGstdAdmRel()
    {
        return $this->hasOne(Admission::class, ['id' => 'gstd_adm_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGstdDeptRel()
    {
        return $this->hasOne(Department::class, ['id' => 'gstd_dept_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGstdDocSignedBy()
    {
        return $this->hasOne(User::class, ['id' => 'gstd_doc_signed_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGstdProgRel()
    {
        return $this->hasOne(Program::class, ['id' => 'gstd_prog_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGstdSpecRel()
    {
        return $this->hasOne(Specialization::class, ['id' => 'gstd_spec_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGstdSponsorshipRel()
    {
        return $this->hasOne(ScholarshipType::class, ['id' => 'gstd_sponsorship_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGraduateStudentPointHistories()
    {
        return $this->hasMany(GraduateStudentPointHistory::class, ['gph_gstd_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGstdUserRel()
    {
        return $this->hasOne(User::class, ['id' => 'gstd_user_relId']);
    }
}
