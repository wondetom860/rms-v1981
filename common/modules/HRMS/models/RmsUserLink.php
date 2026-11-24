<?php

namespace common\modules\HRMS\models;

use Yii;
use backend\models\Student;
use backend\models\User as USER_RMS;
/**
 * This is the model class for table "rms_user_link".
 *
 * @property int $id
 * @property int $person_relId
 * @property string $rms_user_id
 * @property string|null $rms_inst_id
 * @property string|null $rms_std_id
 * @property string $created_at
 * @property int $created_by
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string $data
 *
 * @property User $createdBy
 * @property Person $personRel
 * @property User $updatedBy
 */
class RmsUserLink extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rms_user_link';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_hrms');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['person_relId', 'rms_user_id', 'created_at', 'created_by', 'data'], 'required'],
            [['person_relId', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['data'], 'string'],
            [['rms_user_id', 'rms_inst_id', 'rms_std_id'], 'string', 'max' => 11],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => USER_RMS::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['person_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Person::className(), 'targetAttribute' => ['person_relId' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => USER_RMS::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'person_relId' => Yii::t('rms', 'Person Rel ID'),
            'rms_user_id' => Yii::t('rms', 'Rms User ID'),
            'rms_inst_id' => Yii::t('rms', 'Rms Inst ID'),
            'rms_std_id' => Yii::t('rms', 'Rms Std ID'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData(){
        $mainData = (object)[
            'rms_user_id'=>$this->rms_user_id,
            'rms_inst_id'=>$this->rms_inst_id,
            'rms_std_id'=>$this->rms_std_id,
            'person_relId'=>intval($this->person_relId),
            'updated_by'=>intval($this->updated_by),
            'created_by'=>intval($this->created_by),
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered(){
        return $this->getData() == $this->data;
    }

    public function generateNotification(){
        $linkedIds = $this->getLinkedStudentAccount();
        return Student::find()->where(['NOT IN','id',$linkedIds])->count();
    }

    public function getUnlinkedAnyAccount(){//get those accounts in RMS but NOT in HRMS
        $linkedIds = $this->getLinkedAnyUserAccount();

        return USER_RMS::find()  
            ->where([
                'NOT IN','id',$linkedIds
            ])
            ->orderBy([
                'first_name' => SORT_ASC
            ])
            ->all();
    }


    public function getUnlinkedPersonAccount(){//get those accounts in RMS but NOT in HRMS
        $linkedIds = $this->getLinkedPersonAccount();

        return Person::find()  
            ->where([
                'NOT IN','id',$linkedIds
            ])
            ->orderBy([
                'first_name' => SORT_ASC
            ])
            ->all();
    }

    public function getlinkedAccounts(){
        return $this->find()->all();
    }

    public function getUnlinkedAccounts(){
        // Get all student records in Which student account on RMS is NOT linked to any user record in HRMS
        // 1. get linked accounts:
        $linkedIds = $this->getLinkedStudentAccount();

        // 2. return those students whose id is NOT in $linkedIds

        return Student::find()  
            ->innerJoin('user','user.id = student.std_user_relId')
            ->where([
                'NOT IN','student.id',$linkedIds
            ])
            ->orderBy([
                'std_prog_relId' => SORT_DESC,
                'std_dept_relId' => SORT_ASC,
                'std_spec_relId' => SORT_ASC,
                'user.first_name' => SORT_ASC
            ])
            ->all();
        

    }

    private function getLinkedStudentAccount(){
        $linkedIds = [];
        $linked = $this->find()->all();

        if ($linked && count($linked) > 0) {
            foreach ($linked as $keyLinked) {
                $linkedIds[] = $keyLinked->rms_std_id;
            }
        }
        return $linkedIds;
    }

    private function getLinkedAnyUserAccount(){
        $linkedIds = [];
        $linked = $this->find()->all();

        if ($linked && count($linked) > 0) {
            foreach ($linked as $keyLinked) {
                $linkedIds[] = $keyLinked->rms_user_id;
            }
        }
        return $linkedIds;
    }

    private function getLinkedPersonAccount(){
        $linkedIds = [];
        $linked = $this->find()->all();

        if ($linked && count($linked) > 0) {
            foreach ($linked as $keyLinked) {
                $linkedIds[] = $keyLinked->person_relId;
            }
        }
        return $linkedIds;
    }

    public function getRMSFullName(){//get full Name on REGISTRAR-MANAGEMENT-SYSTEM
        $rmsUser = USER_RMS::findOne(intval($this->rms_user_id));

        return ($rmsUser && count($rmsUser) > 0) ? $rmsUser->getFullName(): NULL;
    }

    /**
     * Gets query for [[PersonRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPersonRel()
    {
        return $this->hasOne(Person::className(), ['id' => 'person_relId']);
    }

    /**
     * Gets query for [[common\modules\HRMS\models\Student]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function rmsStd()
    {
        return Student::findOne(['id' => $this->rms_std_id]);
        // return $this->hasOne(Student::className(), ['id' => 'rms_std_id']);
    }

    /**
     * Gets query for [[common\modules\HRMS\models\User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function rmsAll()
    {
        return USER_RMS::findOne(['id' => $this->rms_user_id]);
        // return $this->hasOne(Student::className(), ['id' => 'rms_std_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(USER_RMS::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(USER_RMS::className(), ['id' => 'updated_by']);
    }
}
