<?php

namespace backend\models;

use Yii;
use \common\components\CommonControllerFunctions as CCF;

/**
 * This is the model class for table "self_sponsored_payment_history".
 *
 * @property integer $id
 * @property integer $ssph_std_relId
 * @property integer $ssph_acadYear_relId
 * @property integer $ssph_sem_relId
 * @property integer $ssph_approved
 * @property string $ssph_approved_at
 * @property integer $ssph_approved_by
 * @property string $ssph_txn_num
 * @property string $ssph_RCT_scan_path
 * @property string $ssph_registrarApproved
 *
 * @property SchoolYear $ssphAcadYearRel
 * @property User $ssphApprovedBy
 * @property Semester $ssphSemRel
 * @property Student $ssphStdRel
 */
class SelfSponsoredPaymentHistory extends \yii\db\ActiveRecord
{
    public $courseId = NULL;
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
    public static function tableName()
    {
        return 'self_sponsored_payment_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ssph_std_relId', 'ssph_acadYear_relId', 'ssph_sem_relId','ssph_txn_num','ssph_pymntRsn_relId'], 'required'],
            [['ssph_std_relId', 'ssph_acadYear_relId', 'ssph_sem_relId', 'ssph_registrarApproved', 'ssph_approved', 'ssph_approved_by','ssph_pymntRsn_relId'], 'integer'],
            [['ssph_approved_at'], 'safe'],
            [['ssph_txn_num'], 'string', 'max' => 15],[['ssph_txn_num'], 'string', 'min' => 12],
            [['ssph_RCT_scan_path'], 'string', 'max' => 50],
            [['ssph_txn_num'], 'unique'],
            [['ssph_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['ssph_acadYear_relId' => 'id']],
            [['ssph_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['ssph_approved_by' => 'id']],
            [['ssph_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['ssph_sem_relId' => 'id']],
            [['ssph_pymntRsn_relId'], 'exist', 'skipOnError' => true, 'targetClass' => PaymentReason::class, 'targetAttribute' => ['ssph_pymntRsn_relId' => 'id']],
            [['ssph_std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['ssph_std_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ssph_std_relId' => Yii::t('rms', 'Student Id'),
            'ssph_pymntRsn_relId' => Yii::t('rms', 'Payment Reason'),
            'ssph_acadYear_relId' => Yii::t('rms', 'Acad Year'),
            'ssph_sem_relId' => Yii::t('rms', 'Semester'),
            'ssph_approved' => Yii::t('rms', 'Approved'),
            'ssph_approved_at' => Yii::t('rms', 'Approved At'),
            'ssph_approved_by' => Yii::t('rms', 'Approved By'),
            'ssph_txn_num' => Yii::t('rms', 'Txn Num'),
            'ssph_RCT_scan_path' => Yii::t('rms', 'Scan file Path'),
            'ssph_registrarApproved' => Yii::t('rms', 'Registrar Approved'),
        ];
    }

    public function getData(){
        $this->data = md5(serialize((object)[
            'ssph_std_relId'=>$this->ssph_std_relId,
            'ssph_pymntRsn_relId'=>$this->ssph_pymntRsn_relId,
            'ssph_acadYear_relId'=>intval($this->ssph_acadYear_relId),
            'ssph_sem_relId'=>intval($this->ssph_sem_relId),
            'ssph_approved'=>intval($this->ssph_approved),
            'ssph_txn_num'=>intval($this->ssph_txn_num),
            'ssph_registrarApproved'=>intval($this->ssph_registrarApproved),
            'created_by'=>intval($this->created_by),
            'updated_by'=>intval($this->updated_by),
        ]));
    }

    public function getCEPAlert(){
        $this->__constructor();
        return $this->find()
            ->where([
                'ssph_approved'=>NULL,
                'ssph_acadYear_relId'=>$this->acadYearId,
                'ssph_sem_relId'=>$this->semId,
            ])
            ->count();
    }

    public function getRegistrarAlert(){
        $this->__constructor();
        return $this->find()
            ->where([
                'ssph_registrarApproved'=>NULL,
                'ssph_acadYear_relId'=>$this->acadYearId,
                'ssph_sem_relId'=>$this->semId,
            ])
            ->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsphAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'ssph_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsphApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'ssph_approved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsphSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'ssph_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsphPymntRsnRel()
    {
        return $this->hasOne(PaymentReason::class, ['id' => 'ssph_pymntRsn_relId']);
    }

     /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsphCourseRel()
    {
        return $this->hasOne(Course::class, ['id' => 'ssph_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsphStdRel()
    {
        return $this->hasOne(Student::class, ['id' => 'ssph_std_relId']);
    }
}
