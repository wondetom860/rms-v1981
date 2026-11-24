<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "emergency_contact".
 *
 * @property integer $id
 * @property string $ec_first_name
 * @property string $ec_last_name
 * @property string $ec_relationShip
 * @property string $ec_phone_home
 * @property string $ec_phone_mobile
 * @property integer $ec_address_relId
 * @property string $ec_email
 *
 * @property AddressInfo $ecAddressRel
 */
class EmergencyContact extends \yii\db\ActiveRecord
{
    public int $ec_address_student_relId;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'emergency_contact';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ec_first_name', 'ec_last_name', 'ec_relationShip', 'ec_phone_mobile'], 'required'],
            [['ec_relationShip'], 'string'],
            [['ec_address_relId'], 'integer'],
            [['ec_first_name', 'ec_last_name'], 'string', 'max' => 50],
            [['ec_phone_home', 'ec_phone_mobile'], 'string', 'max' => 15,'min' =>9 ],
            [['ec_email'],'email'],
            [['ec_address_student_relId'], 'integer'],
            [['ec_email'], 'string', 'max' => 40],
            [['ec_address_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AddressInfo::class, 'targetAttribute' => ['ec_address_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ec_first_name' => Yii::t('rms', 'First Name'),
            'ec_last_name' => Yii::t('rms', 'Last Name'),
            'ec_relationShip' => Yii::t('rms', 'Relation Ship'),
            'ec_phone_home' => Yii::t('rms', 'Phone Home'),
            'ec_phone_mobile' => Yii::t('rms', 'Phone Mobile'),
            'ec_address_relId' => Yii::t('rms', 'Address Info.'),
            'ec_email' => Yii::t('rms', 'Email'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEcAddressRel()
    {
        return $this->hasOne(AddressInfo::class, ['id' => 'ec_address_relId']);
    }

    public function getEcStudentRel()
    {
        return $this->hasOne(Student::class, ['id' => 'std_emergency_contact_relId']);
    }
}
