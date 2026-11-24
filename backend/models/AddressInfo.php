<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "address_info".
 *
 * @property integer $id
 * @property integer $address_country_relId
 * @property integer $address_region_relId
 * @property string $address_zone
 * @property string $address_woreda
 * @property string $address_town
 * @property string $address_kebele
 * @property string $address_hourse_no
 *
 * @property Nationality $addressCountryRel
 * @property EmergencyContact[] $emergencyContacts
 * @property User[] $users
 * @property User[] $users0
 */
class AddressInfo extends \yii\db\ActiveRecord
{

    public $_indicator;//whose address info it is?
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'address_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['address_country_relId', 'address_town','address_region_relId'], 'required'],
            [['address_country_relId', 'address_region_relId','_indicator'], 'integer'],
            [['address_zone', 'address_woreda', 'address_town', 'address_kebele'], 'string'],
            [['address_hourse_no'], 'string', 'max' => 30],
            [['address_country_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Nationality::className(), 'targetAttribute' => ['address_country_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'address_country_relId' => Yii::t('rms', 'Country'),
            'address_region_relId' => Yii::t('rms', 'Region'),
            'address_zone' => Yii::t('rms', 'Zone'),
            'address_woreda' => Yii::t('rms', 'Woreda'),
            'address_town' => Yii::t('rms', 'Town'),
            'address_kebele' => Yii::t('rms', 'Kebele'),
            'address_hourse_no' => Yii::t('rms', 'House No.'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddressCountryRel()
    {
        return $this->hasOne(Nationality::className(), ['id' => 'address_country_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddressRegionRel()
    {
        return $this->hasOne(Region::className(), ['id' => 'address_region_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEmergencyContacts()
    {
        return $this->hasMany(EmergencyContact::className(), ['ec_address_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['place_of_birth_address_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers0()
    {
        return $this->hasMany(User::className(), ['place_of_residence_address_relId' => 'id']);
    }
}
