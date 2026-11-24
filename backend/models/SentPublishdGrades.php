<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sent_publishd_grades".
 *
 * @property int $id
 * @property int $semreg_relId
 * @property int $date_published
 * @property string $recipient_email
 * @property string $relation
 * @property int|null $reg_approved
 * @property int|null $mail_sent
 * @property int|null $date_sent 
 * @property string|null $grade_report_path
 * @property int|null $trail_count
 *
 * @property SemesterRegistration2 $semregRel
 */
class SentPublishdGrades extends \yii\db\ActiveRecord
{

    public $acadYear;
    public $semester;
    public $studentName;
    public $std_regId;
    public $class_level;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sent_publishd_grades';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['semreg_relId', 'date_published', 'recipient_email', 'relation'], 'required'],
            [['studentName', 'std_regId'] , 'string'],
            [['semreg_relId', 'date_published', 'reg_approved', 'mail_sent', 'date_sent', 'trail_count', 'acadYear', 'semester','class_level'], 'integer'],
            [['recipient_email', 'relation', 'grade_report_path'], 'string', 'max' => 150],
            [['semreg_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SemesterRegistration2::class, 'targetAttribute' => ['semreg_relId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'semreg_relId' => Yii::t('rms', 'Semreg Rel ID'),
            'date_published' => Yii::t('rms', 'Date Published'),
            'recipient_email' => Yii::t('rms', 'Recipient Email'),
            'relation' => Yii::t('rms', 'Relation'),
            'std_regId' => Yii::t('rms', 'Student ID'),
            'studentName' => Yii::t('rms', 'Student Name'),
            'acadYear' => Yii::t('rms', 'Academic Year'),
            'semester' => Yii::t('rms', 'Semester'),
            'reg_approved' => Yii::t('rms', 'Reg Approved'),
            'mail_sent' => Yii::t('rms', 'Mail Sent'),
            'date_sent' => Yii::t('rms', 'Date Sent'),
            'grade_report_path' => Yii::t('rms', 'Grade Report Path'),
            'trail_count' => Yii::t('rms', 'Trail Count'),
        ];
    }

    public function sendPublishedSemesterreportEmail($to, $filename,$studentName, $academicYear, $semester)
    {
        $message = Yii::$app->mailer->compose('@common/mail/grade-statement-template', [
            'studentName' => $studentName,
            'academicYear' => $academicYear,
            'semester' => $semester
        ])
            ->setFrom([Yii::$app->params['adminEmail'] => 'ETDU - Registrar & Alumni Directorate'])
            ->setTo($to)
            ->setSubject('Published Semester Report');

        $message->attach($filename, ['fileName' => $filename, 'contentType' => 'application/pdf']);

        // message ID header (hide admin panel)
        $msgId = md5(time()) . '@edu.et';
        if ($message->send()) {
            Yii::$app->session->setFlash('success', 'Email Sent!');
        } else {
            Yii::$app->session->setFlash('error', 'Sending Email failed, Please try again');
        }
    }

    public function loadPublishedGrades()
    {
        // Implement the logic to load published grades here
        // For example, you might want to fetch data from an external source or perform some calculations
        $semregs = SemesterRegistration2::find()
            ->innerJoin('student', 'student.id = semester_registration_2.semreg_std_relId')
            ->innerJoin('emergency_contact', 'emergency_contact.id = student.std_emergency_contact_relId')
            ->where([
                'semreg_gradeCompleted' => 1,
                'student.std_type' => 'CIVIL',
            ])
            ->andWhere(['not in', 'semester_registration_2.id', SentPublishdGrades::find()->select('semreg_relId')])
            ->andWhere(['!=', 'semreg_gradePoint_sum', 0])
            ->andWhere('emergency_contact.ec_email IS NOT NULL')
            ->andWhere('emergency_contact.ec_email != ""')
            ->groupBy('semester_registration_2.semreg_std_relId')
            ->limit(89)
            ->all();
        // Return a success response for demonstration purposes
        return $semregs;
    }

    /**
     * Gets query for [[SemregRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSemregRel()
    {
        return $this->hasOne(SemesterRegistration2::class, ['id' => 'semreg_relId']);
    }
}
