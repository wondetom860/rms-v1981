<aside class="main-sidebar">

    <section class="sidebar">

        <?php

        if (Yii::$app->user->isGuest) {
        ?>
            <div class="pull-left info">
                <a href="/site/login"><i class="fa fa-circle text-warning"></i> Login</a>
            </div>
        <?php
        } else {
        ?>

            <!-- Sidebar user panel -->
            <div class="user-panel">
                <div class="pull-left image">
                    <img src="/uploads/app_logo.png" class="img-circle" alt="User Image" />
                </div>
                <div class="pull-left info">
                    <p><?= Yii::$app->params['client_accr'] ?></p>
                    <span style="font-size: 7pt;"><?= Yii::$app->params['college_accr'] ?></span>
                </div>
            </div>

            <!-- search form -->
            <!-- <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..."/>
              <span class="input-group-btn">
                <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
            </div>
        </form> -->
            <!-- /.search form -->
            <?php

            if (Yii::$app->user->can('Admin')) {
            ?>

                <?= dmstr\widgets\Menu::widget(
                    [
                        'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                        'items' => [
                            ['label' => 'AS AN ADMINISTRATOR', 'options' => ['class' => 'header bg-dark']],
                            ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                            [
                                'label' => 'PARAMETERS',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('rms', 'COMMON'),
                                        'icon' => 'user-secret',
                                        'url' => '',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Nationality'), 'icon' => 'home', 'url' => ['/nationality']],
                                            ['label' => Yii::t('rms', 'Region'), 'icon' => 'home', 'url' => ['/region']],
                                            ['label' => Yii::t('rms', 'Ethnic Group'), 'icon' => 'home', 'url' => ['/ethnic-group']],
                                            ['label' => Yii::t('rms', 'Academic rank'), 'icon' => 'home', 'url' => ['/academic-rank']],
                                            ['label' => Yii::t('rms', 'Academic Qualification'), 'icon' => 'home', 'url' => ['/academic-qualification']],
                                            ['label' => Yii::t('rms', 'Employment Type'), 'icon' => 'home', 'url' => ['/employment-type']],

                                            ['label' => Yii::t('rms', 'Course Category'), 'icon' => 'home', 'url' => ['/course-category']],
                                            ['label' => Yii::t('rms', 'Assessment Names'), 'icon' => 'home', 'url' => ['/catagory-name2']],
                                            ['label' => Yii::t('rms', 'Sponsorship Types'), 'icon' => 'home', 'url' => ['/scholarship-type']],

                                            ['label' => 'Semester', 'icon' => 'file-code-o', 'url' => ['/semester'],],
                                            ['label' => 'Academi Calendar Actions', 'icon' => 'file-code-o', 'url' => ['/academic-calander-action'],],
                                            ['label' => 'Group / Section Names', 'icon' => 'file-code-o', 'url' => ['/w-group'],],

                                            ['label' => Yii::t('rms', 'Withdrawal Reason '), 'icon' => 'home', 'url' => ['/withdrawal-reason']],
                                            ['label' => Yii::t('rms', 'Makeup Exam Reason'), 'icon' => 'home', 'url' => ['/makeup-exam-reason']],
                                            ['label' => Yii::t('rms', 'Payment Reason'), 'icon' => 'home', 'url' => ['/payment-reason']],
                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'INSTITUTIONAL'),
                                        'icon' => 'user-secret',
                                        'url' => '',
                                        'items' => [
                                            ['label' => 'Admission', 'icon' => 'file-code-o', 'url' => ['/admission'],],
                                            ['label' => 'Program', 'icon' => 'file-code-o', 'url' => ['/program'],],
                                            ['label' => 'Class Level', 'icon' => 'file-code-o', 'url' => ['/class-level'],],
                                            ['label' => 'Department', 'icon' => 'file-code-o', 'url' => ['/department'],],
                                            ['label' => 'Specialization', 'icon' => 'file-code-o', 'url' => ['/specialization'],],
                                            ['label' => Yii::t('rms', 'Course'), 'icon' => 'home', 'url' => ['/course']],
                                        ],
                                    ],

                                    // [
                                    //     'label' => Yii::t('rms', 'Military Rank'),
                                    //     'icon' => 'share',
                                    //     'url' => '',
                                    //     'items' => [
                                    //         ['label' => Yii::t('rms', 'Rank Category'), 'icon' => 'share', 'url' => ['/military-rank-category']],
                                    //         ['label' => Yii::t('rms', 'Military Ranks'), 'icon' => 'share', 'url' => ['/military-rank']],
                                    //     ],
                                    // ],
                                ],
                            ],

                            [
                                'label' => 'CONTROLS',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Course Catalog'), 'icon' => 'home', 'url' => ['/course-catalog']],
                                    ['label' => Yii::t('rms', 'Course Pre-Requisite'), 'icon' => 'home', 'url' => ['/course-prerequisit']],
                                    ['label' => Yii::t('rms', 'Honor List Control'), 'icon' => 'home', 'url' => ['/honor-list-control']],
                                    ['label' => Yii::t('rms', 'GC Special Mention'), 'icon' => 'home', 'url' => ['/gc-special-mention']],
                                    ['label' => Yii::t('rms', 'Instructors'), 'icon' => 'vcard', 'url' => ['/instructor']],
                                    ['label' => Yii::t('rms', 'Student Admission'), 'icon' => 'vcard', 'url' => ['/student/manage-admission']],
                                    ['label' => Yii::t('rms', 'Grade Scale'), 'icon' => 'home', 'url' => ['/grade-scale']],
                                    ['label' => Yii::t('rms', 'Payment Requests'), 'icon' => 'vcard', 'url' => ['/payment-request']],
                                    // ['label' => Yii::t('rms', ' Graduation Elegiblity PG'), 'icon' => 'vcard', 'url' => ['/graduation-eligiblity-master']],
                                    ['label' => Yii::t('rms', ' Readmission Entry control'), 'icon' => 'vcard', 'url' => ['/readmission-calculation-control']],

                                    [
                                        'label' => Yii::t('rms', 'Academic Calendar'),
                                        'icon' => 'calendar-o',
                                        'url' => '/AcademicCalendar/default',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main Entry'), 'icon' => 'home', 'url' => ['/AcademicCalendar']],
                                            ['label' => Yii::t('rms', 'Academic Year'), 'icon' => 'home', 'url' => ['/AcademicCalendar/ac-academic-year']],
                                            ['label' => Yii::t('rms', 'Academic Programs'), 'icon' => 'home', 'url' => ['/AcademicCalendar/ac-active-programs']],
                                            ['label' => Yii::t('rms', 'Program Schedule'), 'icon' => 'home', 'url' => ['/AcademicCalendar/ac-program-schedule']],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Student Semester Status'),
                                        'icon' => 'user-secret',
                                        'url' => '',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Status Name'), 'icon' => 'home', 'url' => ['/student-semester-status-name']],
                                            ['label' => Yii::t('rms', ' Calc. Control Default'), 'icon' => 'vcard-o', 'url' => ['/std-status-calculation-control-default']],
                                            ['label' => Yii::t('rms', 'Calc. Control AC'), 'icon' => 'vcard-o', 'url' => ['/std-status-calculation-control-ac']],

                                        ],
                                    ],

                                    [
                                        'label' => Yii::t('rms', 'Academic Staff Evaluation'),
                                        'icon' => 'calendar-o',
                                        'url' => '/AcStEva/default',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main Entry'), 'icon' => 'home', 'url' => ['/AcStEva']],
                                            // ['label' => Yii::t('rms', 'Eval. Criteria Category'), 'icon' => 'home', 'url' => ['/AcStEva/ase-criteria-category']],
                                            // ['label' => Yii::t('rms', 'Eval. Criteria Descrip.'), 'icon' => 'home', 'url' => ['/AcStEva/ase-criteria-description']],
                                            // ['label' => Yii::t('rms', 'Evaluation Types'), 'icon' => 'home', 'url' => ['/AcStEva/ase-evaluation-type']],
                                            // ['label' => Yii::t('rms', 'Type-Criteria-Bundle'), 'icon' => 'home', 'url' => ['/AcStEva/ase-criteria-bundle']],
                                            ['label' => Yii::t('rms', 'Semester Evaluation'), 'icon' => 'home', 'url' => ['/AcStEva/ase-semester-evaluation']],
                                            ['label' => Yii::t('rms', 'Department Schedule'), 'icon' => 'home', 'url' => ['/AcStEva/ase-department-evaluation-schedule']],
                                            ['label' => Yii::t('rms', 'Setup Entry'), 'icon' => 'home', 'url' => ['/AcStEva/ase-evaluation-control-setting']],
                                            ['label' => Yii::t('rms', 'Evaluation Result'), 'icon' => 'home', 'url' => ['/AcStEva/ase-evaluation-result']],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Reassessment'),
                                        'icon' => 'calendar-o',
                                        'url' => '/Reassessment/default',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main Entry'), 'icon' => 'home', 'url' => ['/Reassessment/default']],
                                            ['label' => Yii::t('rms', 'Requests'), 'icon' => 'home', 'url' => ['/Reassessment/reassessment-history']],
                                            // ['label' => Yii::t('rms', 'Eval. Criteria Descrip.'), 'icon' => 'home', 'url' => ['/Reassessment/ase-criteria-description']],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Transfer Students'),
                                        'icon' => 'user-secret',
                                        'url' => '/TRANSTUD',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main'), 'icon' => 'vcard-o', 'url' => ['/TRANSTUD/default']],
                                            ['label' => Yii::t('rms', 'Transfer Students'), 'icon' => 'vcard-o', 'url' => ['/TRANSTUD/transfer-student']],
                                            ['label' => Yii::t('rms', 'Exempted Courses'), 'icon' => 'home', 'url' => ['/TRANSTUD/exempted-course']],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Department Selection'),
                                        'icon' => 'calendar-o',
                                        'url' => '/Depal/default',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main Entry'), 'icon' => 'home', 'url' => ['/Depal']],
                                            ['label' => Yii::t('rms', 'Criteria Category'), 'icon' => 'home', 'url' => ['/Depal/depal-category']],
                                            ['label' => Yii::t('rms', 'Setting Entry'), 'icon' => 'home', 'url' => ['/Depal/depal-setting']],
                                            ['label' => Yii::t('rms', 'Eligible candidates'), 'icon' => 'home', 'url' => ['/Depal/depal-categorized-candidates']],
                                            ['label' => Yii::t('rms', 'Students\' choise'), 'icon' => 'home', 'url' => ['/Depal/depal-choice']],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Withdrawal/Readmission'),
                                        'icon' => 'user-secret',
                                        'url' => '/WithdrawalReadmission',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/default']],
                                            ['label' => Yii::t('rms', 'Withdrawal'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/w-student-withdrawal-history']],
                                            ['label' => Yii::t('rms', 'Readmission'), 'icon' => 'home', 'url' => ['/WithdrawalReadmission/w-student-readmission-history']],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Graduate Class'),
                                        'icon' => 'graduation-cap',
                                        'url' => '/GC',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main'), 'icon' => 'vcard-o', 'url' => ['/GC/default']],
                                            ['label' => Yii::t('rms', 'Distnictions'), 'icon' => 'vcard-o', 'url' => ['/GC/gc-special-mention']],
                                            ['label' => Yii::t('rms', 'Entry'), 'icon' => 'home', 'url' => ['/GC/gc-entry']],
                                            ['label' => Yii::t('rms', 'Students'), 'icon' => 'home', 'url' => ['/GC/gc-student']],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Manage Contact'),
                                        'icon' => 'user-secret',
                                        'url' => '',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Contact Category'), 'icon' => 'home', 'url' => ['/contact-catagory']],
                                            ['label' => Yii::t('rms', 'Contact List'), 'icon' => 'vcard-o', 'url' => ['/contact-list']],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Class Scheduling'),
                                        'icon' => 'vcard',
                                        'url' => '',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Class Rooms'), 'icon' => 'home', 'url' => ['/ClassSchedule/cs-available-class-room']],
                                            ['label' => Yii::t('rms', 'Work Days'), 'icon' => 'vcard-o', 'url' => ['/ClassSchedule/cs-academic-work-day']],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Semester Registration'),
                                        'icon' => 'user-secret',
                                        'url' => '#',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Semester Registration Control Default'), 'icon' => 'home', 'url' => ['/semester-registration-controld']],
                                            ['label' => Yii::t('rms', 'Semester Registration Control AC'), 'icon' => 'vcard', 'url' => ['/semester-registration-controlac']],
                                        ],
                                    ],

                                    // [
                                    //     'label' => Yii::t('rms', 'Semester Evaluation'),
                                    //     'icon' => 'vcard-o',
                                    //     'url' => '',
                                    //     'items' => [
                                    //         ['label' => Yii::t('rms', 'Evaluation Type'), 'icon' => 'home', 'url' => ['/SemesterEvaluation/w-evaluation-types']],
                                    //         ['label' => Yii::t('rms', 'Evaluation Entry Table'), 'icon' => 'vcard-o', 'url' => ['/SemesterEvaluation/w-evaluation-entry-table']],
                                    //         ['label' => Yii::t('rms', 'Evaluation Points/Keys'), 'icon' => 'vcard-o', 'url' => ['/SemesterEvaluation/w-evaluation-points']],
                                    //         ['label' => Yii::t('rms', 'Evaluation Points Entry'), 'icon' => 'vcard-o', 'url' => ['/SemesterEvaluation/w-evaluation-point-entry-table']],

                                    //     ],
                                    // ],
                                    [
                                        'label' => Yii::t('rms', 'Schedule Examinaion'),
                                        'icon' => 'vcard-o',
                                        'url' => '/ExamSchedule/default/index',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main'), 'icon' => 'home', 'url' => ['/ExamSchedule/ex-exam-schedule-entry']],
                                            ['label' => Yii::t('rms', 'Assign Date Time'), 'icon' => 'vcard-o', 'url' => ['/ExamSchedule/ex-date-time-schedule']],
                                            ['label' => Yii::t('rms', 'Allocate Exam. Rooms'), 'icon' => 'vcard-o', 'url' => ['/ExamSchedule/ex-exam-room-slot']],
                                            ['label' => Yii::t('rms', 'Invigilators'), 'icon' => 'vcard-o', 'url' => ['/ExamSchedule/ex-invigilators']],
                                            // ['label' => Yii::t('rms', 'Invigilators'), 'icon' => 'vcard-o', 'url' => ['/ExamSchedule/w-evaluation-point-entry-table']],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Elligiblity Test'),
                                        'icon' => 'vcard-o',
                                        'url' => '/ExitExam/default/index',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main'), 'icon' => 'home', 'url' => ['/ExitExam/default/index']],
                                            ['label' => Yii::t('rms', 'Setting'), 'icon' => 'vcard-o', 'url' => ['/ExitExam/et-setting/index']],
                                            ['label' => Yii::t('rms', 'Examination'), 'icon' => 'vcard-o', 'url' => ['/ExitExam/et-elligiblity-test/index']],
                                            ['label' => Yii::t('rms', 'Attempts Detail'), 'icon' => 'vcard-o', 'url' => ['/ExitExam/et-test-detail/index']],
                                        ],
                                    ],
                                ]
                            ],
                            [
                                'label' => 'TRANSACTION',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Approve Account'), 'icon' => 'home', 'url' => ['/approve-user-signup']],
                                    ['label' => Yii::t('rms', 'Group setup'), 'icon' => 'vcard-o', 'url' => ['/w-group-setup']],
                                    ['label' => Yii::t('rms', 'Group Assignment'), 'icon' => 'home', 'url' => ['/w-group-assignment']],
                                    ['label' => Yii::t('rms', 'Removed Course Enrols'), 'icon' => 'vcard-o', 'url' => ['/removed-srd2']],

                                    // ['label' => Yii::t('rms', 'Schedule Time'), 'icon' => 'home', 'url' => ['/academic-calander']],
                                    ['label' => Yii::t('rms', 'Changed Grades'), 'icon' => 'home', 'url' => ['/chaged-grades']],
                                    ['label' => Yii::t('rms', 'Upload Photo'), 'icon' => 'vcard-o', 'url' => ['/admit-student/documents']],
                                    ['label' => Yii::t('rms', 'Course Offerings'), 'icon' => 'vcard-o', 'url' => ['/course-offering']],
                                    ['label' => Yii::t('rms', 'Re-registration'), 'icon' => 'home', 'url' => ['/re-registration']],
                                    ['label' => Yii::t('rms', 'Assign Department Head'), 'icon' => 'home', 'url' => ['/department-head']],
                                    ['label' => Yii::t('rms', 'Change Academic Profile'), 'icon' => 'home', 'url' => ['/acad-profile-change']],
                                    ['label' => Yii::t('rms', 'Change Semester Status'), 'icon' => 'home', 'url' => ['/change-student-semester-status']],
                                    // ['label' => Yii::t('rms', 'Approve Admission 2'), 'icon' => 'check', 'url' => ['/student/approve-admission']],
                                    ['label' => Yii::t('rms', 'Graduate Student'), 'icon' => 'home', 'url' => ['/graduate-student']],
                                    ['label' => Yii::t('rms', 'Graduate Student Point History'), 'icon' => 'home', 'url' => ['/graduate-student-point-history']],
                                    ['label' => Yii::t('rms', 'Clerance Allowed Student'), 'icon' => 'home', 'url' => ['/clearance-allowed-student']],
                                    ['label' => Yii::t('rms', 'Payment Exemption'), 'icon' => 'home', 'url' => ['/payment-redemption']],
                                ]
                            ],

                            [
                                'label' => 'REPORT',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Course Enrollment'), 'icon' => 'vcard-o', 'url' => ['/course-offering2-detail']],
                                    ['label' => Yii::t('rms', 'Semester Registration'), 'icon' => 'pencil', 'url' => ['/semester-registration/index-grade-report']],
                                    ['label' => Yii::t('rms', 'Group Assignment'), 'icon' => 'book', 'url' => ['/w-group-assignment']],
                                    ['label' => Yii::t('rms', 'Course Assessment'), 'icon' => 'home', 'url' => ['/mark-distribution-report']],
                                    ['label' => Yii::t('rms', 'Mark Submission Progress'), 'icon' => 'home', 'url' => ['/cont-asssess-progress-report']],
                                    ['label' => Yii::t('rms', 'Semester Advisor'), 'icon' => 'home', 'url' => ['/semester-advisor']],
                                    ['label' => Yii::t('rms', 'Course History'), 'icon' => 'vcard-o', 'url' => ['/course/course-history']],
                                    ['label' => Yii::t('rms', 'Seminar/Thesis Title'), 'icon' => 'vcard-o', 'url' => ['/thesis-title']],
                                    ['label' => Yii::t('rms', 'Submitted Grades'), 'icon' => 'pencil', 'url' => ['/submitted-grades/index']],
                                    ['label' => Yii::t('rms', 'Grade Submission Summery'), 'icon' => 'pencil', 'url' => ['/grade-submission-summery']],
                                    ['label' => Yii::t('rms', 'Grade Summery'), 'icon' => 'pencil', 'url' => ['/grade-summery']],
                                    ['label' => Yii::t('rms', 'Semester Status Report'), 'icon' => 'pencil', 'url' => ['/semester-status-report']],
                                    ['label' => Yii::t('rms', 'Email Published Grades'), 'icon' => 'envelope', 'url' => ['/sent-publishd-grades']],
                                    // ['label' => Yii::t('rms', 'Semester Evaluation Progress'), 'icon' => 'pencil', 'url' => ['/semester-evaluation-progress']],
                                    ['label' => Yii::t('rms', 'Makeup Exam'), 'icon' => 'file-text', 'url' => ['/makeup-exam-report']],
                                    // ['label' => Yii::t('rms', 'Department Course'), 'icon' => 'vcard-o', 'url' => ['/department-courses']],
                                    ['label' => Yii::t('rms', 'Class Schedule'), 'icon' => 'vcard-o', 'url' => ['/ClassSchedule']],
                                    ['label' => Yii::t('rms', 'Course Add History'), 'icon' => 'home', 'url' => ['/add-history']],
                                    // ['label' => Yii::t('rms', 'Group / Spec. recommendations'), 'icon' => 'home', 'url' => ['/section-spec-recommendation']],
                                    // ['label' => Yii::t('rms', 'Course Alergic'), 'icon' => 'home', 'url' => ['/course-alergic']],
                                    // ['label' => Yii::t('rms', 'Internship Eligibility'), 'icon' => 'home', 'url' => ['/internship-eligibility']],
                                    ['label' => Yii::t('rms', ' Academic Defficiency'), 'icon' => 'vcard', 'url' => ['/academic-defficiency']],
                                    // ['label' => Yii::t('rms', ' Graduation Elegiblity UG'), 'icon' => 'vcard', 'url' => ['/graduation-eligiblity']],
                                    // ['label' => Yii::t('rms', ' Graduation Elegiblity PG'), 'icon' => 'vcard', 'url' => ['/graduation-eligiblity-master']],
                                    // ['label' => Yii::t('rms', ' Graduation Elegiblity report'), 'icon' => 'vcard', 'url' => ['/graduation-elegiblity-report']],
                                    ['label' => Yii::t('rms', 'Course Assignment'), 'icon' => 'vcard-o', 'url' => ['/semester-course-assignment']],
                                    ['label' => Yii::t('rms', 'Grade Scale'), 'icon' => 'vcard-o', 'url' => ['/grade-scale']],
                                    ['label' => Yii::t('rms', 'Students'), 'icon' => 'home', 'url' => ['/student']],
                                    ['label' => Yii::t('rms', 'Course Drop'), 'icon' => 'vcard-o', 'url' => ['/course-drop-history']],
                                    ['label' => Yii::t('rms', 'Attendance Report'), 'icon' => 'vcard-o', 'url' => ['/attendance-report2']],
                                    ['label' => Yii::t('rms', 'Semester Completion Check'), 'icon' => 'vcard-o', 'url' => ['/semester-completion-check']],
                                    ['label' => Yii::t('rms', 'Admitted student'), 'icon' => 'vcard-o', 'url' => ['/admitted-students/report']],
                                    ['label' => Yii::t('rms', 'Enrolled student'), 'icon' => 'vcard-o', 'url' => ['/enrolled-students-report/index']],
                                    ['label' => Yii::t('rms', 'Grade Report'), 'icon' => 'vcard-o', 'url' => ['/student-semester-grade-report/index']],
                                    ['label' => Yii::t('rms', 'Student Semster Status History'), 'icon' => 'vcard-o', 'url' => ['/student-semester-status-history']],
                                    ['label' => Yii::t('rms', 'Withdrawal Approval Status'), 'icon' => 'vcard-o', 'url' => ['/request-withdrawal/get-approved-requests']],
                                    ['label' => Yii::t('rms', 'Out Standing Students'), 'icon' => 'vcard-o', 'url' => ['/student-semester-status-history/outstanding-students']],
                                    ['label' => Yii::t('rms', 'Student Copy'), 'icon' => 'vcard-o', 'url' => ['/student-copy']],
                                    // ['label' => Yii::t('rms', 'Course Offering'), 'icon' => 'vcard-o', 'url' => ['/course-offering-report']],
                                    ['label' => Yii::t('rms', 'Course reassessment'), 'icon' => 'vcard-o', 'url' => ['/reassessment-report']],
                                    ['label' => Yii::t('rms', 'Course completion check'), 'icon' => 'vcard-o', 'url' => ['/course-completion']],
                                ]
                            ],

                            [
                                'label' => 'ROLES AND USERS',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Role'), 'icon' => 'group', 'url' => ['/admin/role'],],
                                    ['label' => Yii::t('rms', 'Role Assignment'), 'icon' => 'pencil-square-o', 'url' => ['/admin/assignment'],],
                                    ['label' => Yii::t('rms', 'User'), 'icon' => 'user', 'url' => ['/user'],],
                                    ['label' => Yii::t('rms', 'Sign Up'), 'icon' => 'user-plus', 'url' => ['/user/create2'],],
                                ]
                            ],
                            [
                                'label' => 'SECURITY',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Change User-name'), 'icon' => 'user-secret', 'url' => ['/site/changeusername']],
                                    ['label' => Yii::t('rms', 'Change password'), 'icon' => 'key', 'url' => ['/site/changepassword']],
                                    // ['label' => Yii::t('rms', 'Linked Accouts'), 'icon' => 'key', 'url' => ['/rms-user-link']],
                                    ['label' => Yii::t('rms', 'Reset other user password'), 'icon' => 'home', 'url' => ['/site/reset-password-w']],
                                ]
                            ],

                            [
                                'label' => 'FEEDBACK',
                                'icon' => 'share',
                                'url' => '/user-feedback',
                            ],

                            [
                                'label' => Yii::t('rms', 'DASHBOARD'),
                                'icon' => 'bar-chart',
                                'url' => '/site/dashboard/',
                                // 'items' => [
                                //     ['label' => Yii::t('rms', 'Main'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/default']],
                                //     ['label' => Yii::t('rms', 'Withdrawal'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/w-student-withdrawal-history']],
                                //     ['label' => Yii::t('rms', 'Readmission'), 'icon' => 'home', 'url' => ['/WithdrawalReadmission/w-student-readmission-history']],

                                // ],
                            ],
                        ],
                    ]
                ) ?>
            <?php
            }
            if (Yii::$app->user->can('Registrar')) {
            ?>
                <?= dmstr\widgets\Menu::widget(
                    [
                        'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                        'items' => [
                            ['label' => 'AS A REGISTRAR', 'options' => ['class' => 'header']],
                            ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                            [
                                'label' => 'TRANSACTION',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Payment Exemption'), 'icon' => 'home', 'url' => ['/payment-redemption']],
                                    ['label' => Yii::t('rms', 'Course Offerings'), 'icon' => 'vcard-o', 'url' => ['/course-offering']],
                                    ['label' => Yii::t('rms', 'Re-registration'), 'icon' => 'home', 'url' => ['/re-registration']],
                                    [
                                        'label' => Yii::t('rms', 'Academic Calendar'),
                                        'icon' => 'calendar-o',
                                        'url' => '/AcademicCalendar/default',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main Entry'), 'icon' => 'home', 'url' => ['/AcademicCalendar']],
                                            ['label' => Yii::t('rms', 'Academic Year'), 'icon' => 'home', 'url' => ['/AcademicCalendar/ac-academic-year']],
                                            ['label' => Yii::t('rms', 'Academic Programs'), 'icon' => 'home', 'url' => ['/AcademicCalendar/ac-active-programs']],
                                            ['label' => Yii::t('rms', 'Program Schedule'), 'icon' => 'home', 'url' => ['/AcademicCalendar/ac-program-schedule']],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Academic Staff Evaluation'),
                                        'icon' => 'calendar-o',
                                        'url' => '/AcStEva/default',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main Entry'), 'icon' => 'home', 'url' => ['/AcStEva']],
                                            // ['label' => Yii::t('rms', 'Eval. Criteria Category'), 'icon' => 'home', 'url' => ['/AcStEva/ase-criteria-category']],
                                            // ['label' => Yii::t('rms', 'Eval. Criteria Descrip.'), 'icon' => 'home', 'url' => ['/AcStEva/ase-criteria-description']],
                                            // ['label' => Yii::t('rms', 'Evaluation Types'), 'icon' => 'home', 'url' => ['/AcStEva/ase-evaluation-type']],
                                            // ['label' => Yii::t('rms', 'Type-Criteria-Bundle'), 'icon' => 'home', 'url' => ['/AcStEva/ase-criteria-bundle']],
                                            ['label' => Yii::t('rms', 'Semester Evaluation'), 'icon' => 'home', 'url' => ['/AcStEva/ase-semester-evaluation']],
                                            ['label' => Yii::t('rms', 'Department Schedule'), 'icon' => 'home', 'url' => ['/AcStEva/ase-department-evaluation-schedule']],
                                            ['label' => Yii::t('rms', 'Setup Entry'), 'icon' => 'home', 'url' => ['/AcStEva/ase-evaluation-control-setting']],
                                            ['label' => Yii::t('rms', 'Evaluation Result'), 'icon' => 'home', 'url' => ['/AcStEva/ase-evaluation-result']],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Elligiblity Test'),
                                        'icon' => 'vcard-o',
                                        'url' => '/ExitExam/default/index',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main'), 'icon' => 'home', 'url' => ['/ExitExam/default/index']],
                                            ['label' => Yii::t('rms', 'Setting'), 'icon' => 'vcard-o', 'url' => ['/ExitExam/et-setting/index']],
                                            ['label' => Yii::t('rms', 'Examination'), 'icon' => 'vcard-o', 'url' => ['/ExitExam/et-elligiblity-test/index']],
                                            ['label' => Yii::t('rms', 'Attempts Detail'), 'icon' => 'vcard-o', 'url' => ['/ExitExam/et-test-detail/index']],
                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Withdrawal/Readmission'),
                                        'icon' => 'user-secret',
                                        'url' => '/WithdrawalReadmission',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/default']],
                                            ['label' => Yii::t('rms', 'Withdrawal'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/w-student-withdrawal-history']],
                                            ['label' => Yii::t('rms', 'Readmission'), 'icon' => 'home', 'url' => ['/WithdrawalReadmission/w-student-readmission-history']],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Transfer Students'),
                                        'icon' => 'user-secret',
                                        'url' => '/TRANSTUD',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main'), 'icon' => 'vcard-o', 'url' => ['/TRANSTUD/default']],
                                            ['label' => Yii::t('rms', 'Transfer Students'), 'icon' => 'vcard-o', 'url' => ['/TRANSTUD/transfer-student']],
                                            ['label' => Yii::t('rms', 'Exempted Courses'), 'icon' => 'home', 'url' => ['/TRANSTUD/exempted-course']],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Department Selection'),
                                        'icon' => 'calendar-o',
                                        'url' => '/Depal/default',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main Entry'), 'icon' => 'home', 'url' => ['/Depal']],
                                            ['label' => Yii::t('rms', 'Criteria Category'), 'icon' => 'home', 'url' => ['/Depal/depal-category']],
                                            ['label' => Yii::t('rms', 'Setting Entry'), 'icon' => 'home', 'url' => ['/Depal/depal-setting']],
                                            ['label' => Yii::t('rms', 'Eligible candidates'), 'icon' => 'home', 'url' => ['/Depal/depal-categorized-candidates']],
                                            ['label' => Yii::t('rms', 'Students\' choise'), 'icon' => 'home', 'url' => ['/Depal/depal-choice']],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Graduate Class'),
                                        'icon' => 'graduation-cap',
                                        'url' => '/GC',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main'), 'icon' => 'vcard-o', 'url' => ['/GC/default']],
                                            ['label' => Yii::t('rms', 'Distnictions'), 'icon' => 'vcard-o', 'url' => ['/GC/gc-special-mention']],
                                            ['label' => Yii::t('rms', 'Entry'), 'icon' => 'home', 'url' => ['/GC/gc-entry']],
                                            ['label' => Yii::t('rms', 'Students'), 'icon' => 'home', 'url' => ['/GC/gc-student']],

                                        ],
                                    ],
                                    // ['label' => Yii::t('rms', 'Approve Grade Change'), 'icon' => 'check', 'url' => ['/chaged-grades']],
                                    // ['label' => Yii::t('rms', 'Approve Dept. Selection setting'), 'icon' => 'check', 'url' => ['/DepartmentSelection/selection-setting-entry/approve-setting']],
                                    // ['label' => Yii::t('rms', 'Appove makeup exam request'), 'title' => 'Appove makeup exam request', 'icon' => 'book', 'url' => ['/makeup-exam-request']],
                                    // ['label' => Yii::t('rms', 'Appove Course Offering'), 'icon' => 'book', 'url' => ['/approve-course-offering']],
                                    ['label' => Yii::t('rms', 'Removed Course Enrols'), 'icon' => 'vcard-o', 'url' => ['/removed-srd2']],
                                    // ['label' => Yii::t('rms', 'Appove Semester Status Change'), 'icon' => 'book', 'url' => ['/change-student-semester-status/index']],
                                    // ['label' => Yii::t('rms', 'Student Academic Profile Changes'), 'icon' => 'book', 'url' => ['/acad-profile-change']],
                                    // ['label' => Yii::t('rms', 'Approve Suplementary Exam Request'), 'icon' => 'book', 'url' => ['/request-sublimintary-exam-grade']],
                                    // ['label' => Yii::t('rms', 'Appove Course Re-assessment'), 'icon' => 'book', 'url' => ['/approve-reassessment-request']],
                                    // ['label' => Yii::t('rms', 'Approve Withdrawal Request'), 'icon' => 'book', 'url' => ['/request-withdrawal/request-approve']],
                                    // ['label' => Yii::t('rms', 'Approve Re-admission Request'), 'icon' => 'book', 'url' => ['/approve-readmission']],
                                    ['label' => Yii::t('rms', 'Email Published Grades'), 'icon' => 'envelope', 'url' => ['/sent-publishd-grades']],
                                    [
                                        'label' => Yii::t('rms', 'Approve'),
                                        'icon' => 'vcard-o',
                                        'url' => '',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Account'), 'icon' => 'check', 'url' => ['/approve-user-signup']],
                                            ['label' => Yii::t('rms', 'Admission'), 'icon' => 'check', 'url' => ['/approve-admission']],
                                            ['label' => Yii::t('rms', 'Admission 2'), 'icon' => 'check', 'url' => ['/student/approve-admission']],
                                            ['label' => Yii::t('rms', 'Payment'), 'icon' => 'check', 'url' => ['/payment-request']],
                                            ['label' => Yii::t('rms', 'Grade Change'), 'icon' => 'check', 'url' => ['/chaged-grades']],
                                            ['label' => Yii::t('rms', 'Dept. Selection setting'), 'icon' => 'check', 'url' => ['/DepartmentSelection/selection-setting-entry/approve-setting']],
                                            ['label' => Yii::t('rms', 'Makeup exam request'), 'title' => 'Appove makeup exam request', 'icon' => 'book', 'url' => ['/makeup-exam-request']],
                                            ['label' => Yii::t('rms', 'Course Offering'), 'icon' => 'book', 'url' => ['/approve-course-offering']],
                                            ['label' => Yii::t('rms', 'Semester Status Change'), 'icon' => 'book', 'url' => ['/change-student-semester-status/index']],
                                            ['label' => Yii::t('rms', 'Student Academic Profile Changes'), 'icon' => 'book', 'url' => ['/acad-profile-change']],
                                            ['label' => Yii::t('rms', 'Sublimintary Exam Request'), 'icon' => 'book', 'url' => ['/request-sublimintary-exam-grade']],
                                            ['label' => Yii::t('rms', 'Course Re-assessment'), 'icon' => 'book', 'url' => ['/approve-reassessment-request']],
                                            ['label' => Yii::t('rms', 'Withdrawal Request'), 'icon' => 'book', 'url' => ['/request-withdrawal/request-approve']],
                                            ['label' => Yii::t('rms', 'Re-admission Request'), 'icon' => 'book', 'url' => ['/approve-readmission']],
                                            ['label' => Yii::t('rms', 'Graduate Student Profile'), 'icon' => 'home', 'url' => ['/approve-graduate-student-information']],
                                        ],
                                    ]
                                ]
                            ],

                            [
                                'label' => 'REPORT',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Course Catalog'), 'icon' => 'book', 'url' => ['/course-catalog/index']],
                                    ['label' => Yii::t('rms', 'Course Pre-Requisite'), 'icon' => 'home', 'url' => ['/course-prerequisite-report']],
                                    ['label' => Yii::t('rms', 'Group Assignment'), 'icon' => 'book', 'url' => ['/w-group-assignment']],
                                    ['label' => Yii::t('rms', 'Course Enrollment'), 'icon' => 'vcard-o', 'url' => ['/course-offering2-detail']],
                                    ['label' => Yii::t('rms', 'Semester Registration'), 'icon' => 'vcard-o', 'url' => ['/semester-registration/index-grade-report']],
                                    ['label' => Yii::t('rms', 'Course Assessment'), 'icon' => 'home', 'url' => ['/mark-distribution-report']],
                                    ['label' => Yii::t('rms', 'Course History'), 'icon' => 'vcard-o', 'url' => ['/course/course-history']],
                                    ['label' => Yii::t('rms', 'Mark Submission Progress'), 'icon' => 'home', 'url' => ['/cont-asssess-progress-report']],
                                    ['label' => Yii::t('rms', 'Students'), 'icon' => 'home', 'url' => ['/student']],
                                    ['label' => Yii::t('rms', 'Semester Advisor'), 'icon' => 'home', 'url' => ['/semester-advisor']],
                                    ['label' => Yii::t('rms', 'Student Status History'), 'icon' => 'home', 'url' => ['/student-semester-status-history']],
                                    ['label' => Yii::t('rms', 'Submitted Grades'), 'icon' => 'pencil', 'url' => ['/submitted-grades/index']],
                                    ['label' => Yii::t('rms', 'Grade Submission Summary'), 'icon' => 'pencil', 'url' => ['/grade-submission-summery']],
                                    ['label' => Yii::t('rms', 'Grade Summery'), 'icon' => 'pencil', 'url' => ['/grade-summery']],
                                    ['label' => Yii::t('rms', 'Class Schedule'), 'icon' => 'vcard-o', 'url' => ['/ClassSchedule']],
                                    ['label' => Yii::t('rms', 'Graduate Student'), 'icon' => 'pencil', 'url' => ['/graduate-student']],
                                    // ['label' => Yii::t('rms', 'Semester Evaluation Progress'), 'icon' => 'pencil', 'url' => ['/semester-evaluation-progress']],
                                    ['label' => Yii::t('rms', 'Semester Status Report'), 'icon' => 'pencil', 'url' => ['/semester-status-report']],
                                    ['label' => Yii::t('rms', 'Makeup Exam'), 'icon' => 'file-text', 'url' => ['/makeup-exam-report']],
                                    // ['label' => Yii::t('rms', 'Department Course'), 'icon' => 'vcard-o', 'url' => ['/department-courses']],
                                    ['label' => Yii::t('rms', 'Course Added'), 'icon' => 'home', 'url' => ['/add-history']],
                                    // ['label' => Yii::t('rms', 'Group / Spec. recommendations'), 'icon' => 'home', 'url' => ['/section-spec-recommendation']],
                                    // ['label' => Yii::t('rms', 'Course Alergic'), 'icon' => 'home', 'url' => ['/course-alergic']],
                                    // ['label' => Yii::t('rms', 'Internship Eligibility'), 'icon' => 'home', 'url' => ['/internship-eligibility']],
                                    ['label' => Yii::t('rms', 'Academic Defficiency'), 'icon' => 'vcard', 'url' => ['/academic-defficiency']],
                                    // ['label' => Yii::t('rms', 'Graduation Elegiblity UG'), 'icon' => 'vcard', 'url' => ['/graduation-eligiblity']],
                                    // ['label' => Yii::t('rms', 'Graduation Elegiblity PG'), 'icon' => 'vcard', 'url' => ['/graduation-eligiblity-master']],
                                    // ['label' => Yii::t('rms', 'Graduation Elegiblity report'), 'icon' => 'vcard', 'url' => ['/graduation-elegiblity-report']],
                                    ['label' => Yii::t('rms', 'Course Assignment'), 'icon' => 'vcard-o', 'url' => ['/semester-course-assignment']],
                                    ['label' => Yii::t('rms', 'Grade Scale'), 'icon' => 'vcard-o', 'url' => ['/grade-scale']],
                                    ['label' => Yii::t('rms', 'Course Drop Report'), 'icon' => 'vcard-o', 'url' => ['/course-drop-report']],
                                    ['label' => Yii::t('rms', 'Course Drop History'), 'icon' => 'vcard-o', 'url' => ['/course-drop-history']],
                                    ['label' => Yii::t('rms', 'Attendance Report'), 'icon' => 'vcard-o', 'url' => ['/attendance-report2']],
                                    ['label' => Yii::t('rms', 'Semester Completion Check'), 'icon' => 'vcard-o', 'url' => ['/semester-completion-check']],
                                    ['label' => Yii::t('rms', 'Admitted student'), 'icon' => 'vcard-o', 'url' => ['/admitted-students/report']],
                                    ['label' => Yii::t('rms', 'Enrolled student'), 'icon' => 'vcard-o', 'url' => ['/enrolled-students-report/index']],
                                    ['label' => Yii::t('rms', 'Grade Report'), 'icon' => 'vcard-o', 'url' => ['/student-semester-grade-report/index']],
                                    ['label' => Yii::t('rms', 'Student Semster Status History'), 'icon' => 'vcard-o', 'url' => ['/student-semester-status-history']],
                                    ['label' => Yii::t('rms', 'Withdrawal Approval Status'), 'icon' => 'vcard-o', 'url' => ['/request-withdrawal/get-approved-requests']],
                                    ['label' => Yii::t('rms', 'Out Standing Students'), 'icon' => 'vcard-o', 'url' => ['/student-semester-status-history/outstanding-students']],
                                    ['label' => Yii::t('rms', 'Student Copy'), 'icon' => 'vcard-o', 'url' => ['/student-copy']],
                                    ['label' => Yii::t('rms', 'Course Offering'), 'icon' => 'vcard-o', 'url' => ['/course-offering-report']],
                                    ['label' => Yii::t('rms', 'Course reassessment'), 'icon' => 'vcard-o', 'url' => ['/reassessment-report']],
                                    ['label' => Yii::t('rms', 'Course completion check'), 'icon' => 'vcard-o', 'url' => ['/course-completion']],
                                ],
                            ],

                            [
                                'label' => 'SECURITY',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Change User-name'), 'icon' => 'user-secret', 'url' => ['/site/changeusername']],
                                    ['label' => Yii::t('rms', 'Change password'), 'icon' => 'key', 'url' => ['/site/changepassword']],
                                    // ['label' => Yii::t('rms', 'Linked Accouts'), 'icon' => 'key', 'url' => ['/rms-user-link']],
                                    // ['label' => Yii::t('rms', 'Linked Accouts'), 'icon' => 'key', 'url' => ['/rms-user-link']],
                                ]
                            ],

                            [
                                'label' => 'FEEDBACK',
                                'icon' => 'share',
                                'url' => '/user-feedback',
                            ],
                            [
                                'label' => 'CONTACT',
                                'icon' => 'phone',
                                'url' => '/contact-list',
                            ],

                            [
                                'label' => Yii::t('rms', 'DASHBOARD'),
                                'icon' => 'bar-chart',
                                'url' => '/site/dashboard',
                                // 'items' => [
                                //     ['label' => Yii::t('rms', 'Main'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/default']],
                                //     ['label' => Yii::t('rms', 'Withdrawal'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/w-student-withdrawal-history']],
                                //     ['label' => Yii::t('rms', 'Readmission'), 'icon' => 'home', 'url' => ['/WithdrawalReadmission/w-student-readmission-history']],

                                // ],
                            ],
                        ],
                    ]
                ) ?>
            <?php
            }
            if (Yii::$app->user->can('Student')) {
            ?>
                <?= dmstr\widgets\Menu::widget(
                    [
                        'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                        'items' => [
                            ['label' => 'AS A STUDENT', 'options' => ['class' => 'header']],
                            ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                            [
                                'label' => 'TRANSACTION',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'My Profile'), 'icon' => 'pencil', 'url' => ['/my-profile']],
                                    ['label' => Yii::t('rms', 'Enroll on Course'), 'icon' => 'vcard-o', 'url' => ['/semester-registration2']],
                                    ['label' => Yii::t('rms', 'Add Course'), 'icon' => 'home', 'url' => ['/add-history/create']],
                                    // ['label' => Yii::t('rms', 'New Admission'), 'icon' => 'home', 'url' => ['/admit-student/new-admission']],
                                    ['label' => Yii::t('rms', 'Drop Course'), 'icon' => 'home', 'url' => ['/course-drop']],
                                    // ['label' => Yii::t('rms', 'Semester Evaln.'), 'icon' => 'bar-chart', 'url' => ['/semester-evaluation']],
                                    ['label' => Yii::t('rms', 'Semester Evaln'), 'icon' => 'home', 'url' => ['/AcStEva']],
                                    ['label' => Yii::t('rms', 'Request Re-assessment'), 'icon' => 'home', 'url' => ['/reassessment']],
                                    [
                                        'label' => Yii::t('rms', 'Request Suplementary Exam (PG)'),
                                        'icon' => 'vcard-o',
                                        'url' => ['/request-sublimintary-exam-grade']
                                    ],
                                    ['label' => Yii::t('rms', 'Request Makeup Exam'), 'icon' => 'home', 'url' => ['/makeup-exam-request']],
                                    ['label' => Yii::t('rms', 'Submit Thesis Title (PG)'), 'icon' => 'vcard-o', 'url' => ['/upload-thesis-title']],
                                    ['label' => Yii::t('rms', 'Request Re-admission'), 'icon' => 'vcard-o', 'url' => ['/request-readmission/index']],
                                    ['label' => Yii::t('rms', 'Request Withdrawal'), 'icon' => 'home', 'url' => ['/request-withdrawal/index']],
                                    ['label' => Yii::t('rms', 'Select Department'), 'icon' => 'home', 'url' => ['/Depal/depal-choice']],
                                    ['label' => Yii::t('rms', 'Exit Exam'), 'icon' => 'home', 'url' => ['/ExitExam/et-elligiblity-test/index']],
                                ],
                            ],

                            [
                                'label' => 'REPORT',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Enrolled Courses'), 'icon' => 'file-text', 'url' => ['/course2']],
                                    ['label' => Yii::t('rms', 'Course Assessment'), 'icon' => 'file-text', 'url' => ['/mark-distribution']],
                                    ['label' => Yii::t('rms', 'Mark Progress'), 'icon' => 'home', 'url' => ['/mark-progress']],
                                    ['label' => Yii::t('rms', 'Time Table'), 'icon' => 'vcard-o', 'url' => ['/class-schedule-report']],
                                    ['label' => Yii::t('rms', 'Grade Report'), 'icon' => 'home', 'url' => ['/semester-registration/index-grade-report']],
                                    ['label' => Yii::t('rms', 'My Course'), 'icon' => 'vcard-o', 'url' => ['/department-courses']],
                                    ['label' => Yii::t('rms', 'Course Drop History'), 'icon' => 'vcard-o', 'url' => ['/course-drop-history']],
                                    // ['label' => Yii::t('rms', 'Re-assessment'), 'icon' => 'vcard-o', 'url' => ['/Reassessment/reassessment-history/']],
                                    ['label' => Yii::t('rms', 'Withdrawal Status'), 'icon' => 'vcard-o', 'url' => ['/request-withdrawal/get-approved-requests']],
                                    ['label' => Yii::t('rms', 'Course Add'), 'icon' => 'vcard-o', 'url' => ['/add-history']],
                                    ['label' => Yii::t('rms', 'Makeup Exam'), 'icon' => 'file-text', 'url' => ['/makeup-exam-report']],
                                    // ['label' => Yii::t('rms', 'Payment'), 'icon' => 'vcard-o', 'url' => ['/payment-report']],
                                    ['label' => Yii::t('rms', 'Withdrawal Report'), 'icon' => 'vcard-o', 'url' => ['/withdrawal-report']],
                                    ['label' => Yii::t('rms', 'Out Standing Students'), 'icon' => 'vcard-o', 'url' => ['/student-semester-status-history/outstanding-students']],
                                ],
                            ],

                            [
                                'label' => 'SECURITY',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Change User-name'), 'icon' => 'user-secret', 'url' => ['/site/changeusername']],
                                    ['label' => Yii::t('rms', 'Change password'), 'icon' => 'key', 'url' => ['/site/changepassword']],
                                    // ['label' => Yii::t('rms', 'Linked Accouts'), 'icon' => 'key', 'url' => ['/rms-user-link']],
                                ]
                            ],

                            [
                                'label' => 'FEEDBACK',
                                'icon' => 'share',
                                'url' => '/user-feedback',
                            ],
                            [
                                'label' => 'CONTACT',
                                'icon' => 'phone',
                                'url' => '/contact-list',
                            ],
                        ],
                    ]
                ) ?>
            <?php }
            if (Yii::$app->user->can('DepartmentHead')) {
            ?>
                <?= dmstr\widgets\Menu::widget(
                    [
                        'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                        'items' => [
                            ['label' => 'DEPARTMENT HEAD', 'options' => ['class' => 'header']],
                            ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                            [
                                'label' => 'TRANSACTION',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Instructors'), 'icon' => 'vcard', 'url' => ['/instructor']],
                                    ['label' => Yii::t('rms', 'Assign Advisor'), 'icon' => 'vcard-o', 'url' => ['/semester-advisor']],
                                    ['label' => Yii::t('rms', 'Removed Course Enrols'), 'icon' => 'vcard-o', 'url' => ['/removed-srd2']],
                                    ['label' => Yii::t('rms', 'Assign faculty'), 'icon' => 'vcard-o', 'url' => ['/faculty-course-assignment']],
                                    ['label' => Yii::t('rms', 'Academic Programs'), 'icon' => 'vcard-o', 'url' => ['/AcademicCalendar/ac-active-programs']],
                                    ['label' => Yii::t('rms', 'Assign Course Co-Ordinator'), 'icon' => 'vcard-o', 'url' => ['/assign-semester-course-cordinator']],
                                    ['label' => Yii::t('rms', 'Course offering'), 'icon' => 'vcard-o', 'url' => ['/course-offering3']],
                                    ['label' => Yii::t('rms', 'Course offering(Optional)'), 'icon' => 'vcard-o', 'url' => ['/course-offering2']],
                                    ['label' => Yii::t('rms', 'Re-registration'), 'icon' => 'home', 'url' => ['/re-registration']],
                                    ['label' => Yii::t('rms', 'Attendance'), 'icon' => 'vcard-o', 'url' => ['/attendance']],
                                    ['label' => Yii::t('rms', 'Seminar/Thesis Title'), 'icon' => 'vcard-o', 'url' => ['/upload-thesis-title']],
                                    [
                                        'label' => Yii::t('rms', 'Approve'),
                                        'icon' => 'vcard-o',
                                        'url' => '',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Withdrawal'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/w-student-withdrawal-history']],
                                            ['label' => Yii::t('rms', 'Readmission'), 'icon' => 'home', 'url' => ['/WithdrawalReadmission/w-student-readmission-history']],
                                            ['label' => Yii::t('rms', 'Suplementary Exam Request'), 'icon' => 'book', 'url' => ['/request-sublimintary-exam-grade']],
                                            ['label' => Yii::t('rms', 'Course Add'), 'icon' => 'home', 'url' => ['/approve-course-add']],
                                            ['label' => Yii::t('rms', 'Course Drop'), 'icon' => 'home', 'url' => ['/approve-course-drop']],
                                            // ['label' => Yii::t('rms', 'Course Grade'), 'icon' => 'vcard-o', 'url' => ['/approve-grade']],
                                            ['label' => Yii::t('rms', 'Course Re-assessment'), 'icon' => 'book', 'url' => ['/Reassessment/reassessment-history/index']],
                                            ['label' => Yii::t('rms', 'Withdrawal Request'), 'icon' => 'book', 'url' => ['/request-withdrawal/request-approve']],
                                            ['label' => Yii::t('rms', 'Mark Distribution Request'), 'icon' => 'list', 'url' => ['/mark-distribution']],
                                            ['label' => Yii::t('rms', 'Re-admission Request W'), 'icon' => 'book', 'url' => ['/approve-readmission']],
                                            ['label' => Yii::t('rms', 'Makeup exam request'), 'title' => 'Appove makeup exam request', 'icon' => 'book', 'url' => ['/makeup-exam-request']],
                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Department Selection'),
                                        'icon' => 'calendar-o',
                                        'url' => '/Depal/default',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main Entry'), 'icon' => 'home', 'url' => ['/Depal']],
                                            ['label' => Yii::t('rms', 'Criteria Category'), 'icon' => 'home', 'url' => ['/Depal/depal-category']],
                                            ['label' => Yii::t('rms', 'Setting Entry'), 'icon' => 'home', 'url' => ['/Depal/depal-setting']],
                                            ['label' => Yii::t('rms', 'Eligible candidates'), 'icon' => 'home', 'url' => ['/Depal/depal-categorized-candidates']],
                                            ['label' => Yii::t('rms', 'Students\' choise'), 'icon' => 'home', 'url' => ['/Depal/depal-choice']],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Academic Staff Evaluation'),
                                        'icon' => 'calendar-o',
                                        'url' => '/AcStEva/default',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main Entry'), 'icon' => 'home', 'url' => ['/AcStEva']],
                                            // ['label' => Yii::t('rms', 'Eval. Criteria Category'), 'icon' => 'home', 'url' => ['/AcStEva/ase-criteria-category']],
                                            // ['label' => Yii::t('rms', 'Eval. Criteria Descrip.'), 'icon' => 'home', 'url' => ['/AcStEva/ase-criteria-description']],
                                            // ['label' => Yii::t('rms', 'Evaluation Types'), 'icon' => 'home', 'url' => ['/AcStEva/ase-evaluation-type']],
                                            // ['label' => Yii::t('rms', 'Type-Criteria-Bundle'), 'icon' => 'home', 'url' => ['/AcStEva/ase-criteria-bundle']],
                                            ['label' => Yii::t('rms', 'Semester Evaluation'), 'icon' => 'home', 'url' => ['/AcStEva/ase-semester-evaluation']],
                                            ['label' => Yii::t('rms', 'Department Schedule'), 'icon' => 'home', 'url' => ['/AcStEva/ase-department-evaluation-schedule']],
                                            ['label' => Yii::t('rms', 'Setup Entry'), 'icon' => 'home', 'url' => ['/AcStEva/ase-evaluation-control-setting']],
                                            ['label' => Yii::t('rms', 'Evaluation Result'), 'icon' => 'home', 'url' => ['/AcStEva/ase-evaluation-result']],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Withdrawal/Readmission'),
                                        'icon' => 'user-secret',
                                        'url' => '/WithdrawalReadmission',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/default']],
                                            ['label' => Yii::t('rms', 'Withdrawal'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/w-student-withdrawal-history']],
                                            ['label' => Yii::t('rms', 'Readmission'), 'icon' => 'home', 'url' => ['/WithdrawalReadmission/w-student-readmission-history']],

                                        ],
                                    ],
                                    ['label' => Yii::t('rms', 'Assign Instructor'), 'icon' => 'book', 'url' => ['/assign-instructor']],
                                    // ['label' => Yii::t('rms', 'Recommend Section / Spec.'), 'icon' => 'book', 'url' => ['/section-spec-recommendation']],
                                    ['label' => Yii::t('rms', 'Student Withdrawal From'), 'icon' => 'book', 'url' => ['/request-withdrawal/request-hod']],
                                    // ['label' => Yii::t('rms', 'Semester Completion Check'), 'icon' => 'pencil', 'url' => ['/semester-completion-check']],
                                    ['label' => Yii::t('rms', 'Class Schedule'), 'icon' => 'pencil', 'url' => ['/time-schedule']],
                                    ['label' => Yii::t('rms', 'Submit Project Course Grade'), 'icon' => 'book', 'url' => ['/submit-project-course-grade']],
                                    [
                                        'label' => Yii::t('rms', 'Transfer Students'),
                                        'icon' => 'user-secret',
                                        'url' => '/TRANSTUD',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main'), 'icon' => 'vcard-o', 'url' => ['/TRANSTUD/default']],
                                            ['label' => Yii::t('rms', 'Transfer Students'), 'icon' => 'vcard-o', 'url' => ['/TRANSTUD/transfer-student']],
                                            ['label' => Yii::t('rms', 'Exempted Courses'), 'icon' => 'home', 'url' => ['/TRANSTUD/exempted-course']],

                                        ],
                                    ],
                                    // [
                                    //     'label' => Yii::t('rms', 'Submit Grade'),
                                    //     'icon' => 'bank',
                                    //     'url' => '',
                                    //     'items' => [
                                    //         // ['label' => Yii::t('rms', 'Course Grade'), 'icon' => 'vcard-o', 'url' => ['/submit-grade2']],
                                    //         ['label' => Yii::t('rms', 'Suplementary Exam Grade'), 'icon' => 'vcard-o', 'url' => ['request-sublimintary-exam-grade/submit-grade']],
                                    //         ['label' => Yii::t('rms', 'Makeup Exam Grade'), 'icon' => 'vcard-o', 'url' => ['/submit-makeup-exam-grade']],
                                    //         ['label' => Yii::t('rms', 'Re-assessment Grade'), 'icon' => 'vcard-o', 'url' => ['/Reassessment/reassessment-history/']],
                                    //         ['label' => Yii::t('rms', 'Submit Project Course Grade'), 'icon' => 'book', 'url' => ['/submit-project-course-grade']],
                                    //     ],
                                    // ],
                                    [
                                        'label' => Yii::t('rms', 'Course Assessment'),
                                        'icon' => 'file-excel-o',
                                        'url' => '',
                                        'items' => [

                                            // ['label' => Yii::t('rms', 'Assessment Catagories'), 'icon' => 'home', 'url' => ['/cont-assess-catagory2']],
                                            ['label' => Yii::t('rms', 'Assessment Plan'), 'icon' => 'home', 'url' => ['/CourseAssessment/default/index']],
                                            // ['label' => Yii::t('rms', 'Mark Assignment'), 'icon' => 'th-list', 'url' => ['/instance-mark-assignment']],
                                            ['label' => Yii::t('rms', 'Submit Mark'), 'icon' => 'list', 'url' => ['/continuous-assessment2']],
                                        ],
                                    ],
                                ],
                            ],

                            [
                                'label' => 'REPORT',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Semester Courses'), 'icon' => 'file-text', 'url' => ['/course2']],
                                    ['label' => Yii::t('rms', 'Course Assessment'), 'icon' => 'file-text', 'url' => ['/mark-distribution']],
                                    ['label' => Yii::t('rms', 'Mark Progress'), 'icon' => 'home', 'url' => ['/cont-asssess-progress-report']],
                                    ['label' => Yii::t('rms', 'Attendance'), 'icon' => 'vcard-o', 'url' => ['/attendance-report2']],
                                    ['label' => Yii::t('rms', 'Student'), 'icon' => 'vcard-o', 'url' => ['/w-group-setup']],
                                    ['label' => Yii::t('rms', 'Semester Status'), 'icon' => 'vcard-o', 'url' => ['/semester-registration/index-grade-report']],
                                    ['label' => Yii::t('rms', 'Course Enrollment'), 'icon' => 'vcard-o', 'url' => ['/course-offering2-detail']],
                                    ['label' => Yii::t('rms', 'Course Pre-Requisite'), 'icon' => 'vcard-o', 'url' => ['/course-prerequisit']],
                                    ['label' => Yii::t('rms', 'Changed grades'), 'icon' => 'vcard-o', 'url' => ['/chaged-grades']],
                                    ['label' => Yii::t('rms', 'Course History'), 'icon' => 'vcard-o', 'url' => ['/course/course-history']],
                                    ['label' => Yii::t('rms', 'Course Completion Check'), 'icon' => 'vcard-o', 'url' => ['/course-completion']],
                                    ['label' => Yii::t('rms', 'Out Standing Students'), 'icon' => 'vcard-o', 'url' => ['/student-semester-status-history/outstanding-students']],
                                    // ['label' => Yii::t('rms', 'My Course'), 'icon' => 'vcard-o', 'url' => ['/department-courses']],
                                    ['label' => Yii::t('rms', 'Course Dropped'), 'icon' => 'vcard-o', 'url' => ['/course-drop-history']],
                                    ['label' => Yii::t('rms', 'Class Schedule Report'), 'icon' => 'vcard-o', 'url' => ['/class-schedule-report']],
                                    ['label' => Yii::t('rms', 'Re-assessment'), 'icon' => 'vcard-o', 'url' => ['/reassessment-report']],
                                    ['label' => Yii::t('rms', 'Withdrawal Status'), 'icon' => 'vcard-o', 'url' => ['/request-withdrawal/get-approved-requests']],
                                    ['label' => Yii::t('rms', 'Course Add'), 'icon' => 'vcard-o', 'url' => ['/course-add-report']],
                                    ['label' => Yii::t('rms', 'Makeup Exam'), 'icon' => 'file-text', 'url' => ['/makeup-exam-report']],
                                    // ['label' => Yii::t('rms', 'Payment'), 'icon' => 'vcard-o', 'url' => ['/payment-report']],
                                    // ['label' => Yii::t('rms', 'Internship Eligibility'), 'icon' => 'home', 'url' => ['/internship-eligibility']],
                                    // ['label' => Yii::t('rms', 'Semester Evaluation Progress'), 'icon' => 'vcard-o', 'url' => ['/semester-evaluation-progress']],
                                    // ['label' => Yii::t('rms', 'Semester Evaluation Report'), 'icon' => 'vcard-o', 'url' => ['/generate-semester-evaluation-report']],
                                    // ['label' => Yii::t('rms', 'Evaluation Report2'), 'icon' => 'vcard-o', 'url' => ['/course-offering2-detail/generate-evaluation-report']],
                                    ['label' => Yii::t('rms', 'Withdrawal Report'), 'icon' => 'vcard-o', 'url' => ['/withdrawal-report']],
                                    ['label' => Yii::t('rms', 'Out Standing Students'), 'icon' => 'vcard-o', 'url' => ['/student-semester-status-history/outstanding-students']],
                                ],
                            ],

                            [
                                'label' => 'SECURITY',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Change User-name'), 'icon' => 'user-secret', 'url' => ['/site/changeusername']],
                                    ['label' => Yii::t('rms', 'Change password'), 'icon' => 'key', 'url' => ['/site/changepassword']],
                                    // ['label' => Yii::t('rms', 'Linked Accouts'), 'icon' => 'key', 'url' => ['/rms-user-link']],
                                ]
                            ],

                            [
                                'label' => 'FEEDBACK',
                                'icon' => 'share',
                                'url' => '/user-feedback',
                            ],
                            [
                                'label' => 'CONTACT',
                                'icon' => 'phone',
                                'url' => '/contact-list',
                            ],
                            [
                                'label' => Yii::t('rms', 'DASHBOARD'),
                                'icon' => 'bar-chart',
                                'url' => '/site/dashboard/',
                                // 'items' => [
                                //     ['label' => Yii::t('rms', 'Main'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/default']],
                                //     ['label' => Yii::t('rms', 'Withdrawal'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/w-student-withdrawal-history']],
                                //     ['label' => Yii::t('rms', 'Readmission'), 'icon' => 'home', 'url' => ['/WithdrawalReadmission/w-student-readmission-history']],

                                // ],
                            ],
                        ],
                    ]
                ) ?>
            <?php }
            if (Yii::$app->user->can('Instructor')) {
            ?>
                <?= dmstr\widgets\Menu::widget(
                    [
                        'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                        'items' => [
                            ['label' => 'INSTRUCTOR', 'options' => ['class' => 'header']],
                            ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                            [
                                'label' => 'TRANSACTION',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Seminar/Thesis Title'), 'icon' => 'vcard-o', 'url' => ['/upload-thesis-title']],
                                    ['label' => Yii::t('rms', 'Course Add requset'), 'icon' => 'home', 'url' => ['/add-history']],
                                    ['label' => Yii::t('rms', 'Course  Drop History'), 'icon' => 'home', 'url' => ['/course-drop-history']],
                                    // ['label' => Yii::t('rms', 'Re-Assessment request'), 'icon' => 'vcard-o', 'url' => ['/approve-reassessment-request']],
                                    ['label' => Yii::t('rms', 'Re-Assessment(2)'), 'icon' => 'vcard-o', 'url' => ['/Reassessment/reassessment-history/index']],
                                    ['label' => Yii::t('rms', 'Attendance'), 'icon' => 'vcard-o', 'url' => ['/attendance']],
                                    // ['label' => Yii::t('rms', 'Semester Evln.'), 'icon' => 'vcard-o', 'url' => ['/semester-evaluation']],
                                    ['label' => Yii::t('rms', 'Semester Evaln'), 'icon' => 'home', 'url' => ['/AcStEva']],
                                    ['label' => Yii::t('rms', 'Submit Project Course Grade'), 'icon' => 'book', 'url' => ['/submit-project-course-grade']],
                                    [
                                        'label' => Yii::t('rms', 'Course Assessment'),
                                        'icon' => 'file-excel-o',
                                        'url' => '',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Assessment Plan'), 'icon' => 'home', 'url' => ['/CourseAssessment/default/index']],
                                            // ['label' => Yii::t('rms', 'Mark Assignment'), 'icon' => 'th-list', 'url' => ['/instance-mark-assignment']],
                                            ['label' => Yii::t('rms', 'Submit Mark'), 'icon' => 'list', 'url' => ['/continuous-assessment2']],
                                        ],
                                    ],
                                ],
                            ],

                            [
                                'label' => 'REPORT',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Mark Assignemnt Report'), 'icon' => 'list', 'url' => ['/mark-distribution']],
                                    ['label' => Yii::t('rms', 'Course Offerings'), 'icon' => 'home', 'url' => ['/course-offering2-detail']],
                                    ['label' => Yii::t('rms', 'Time Table'), 'icon' => 'vcard-o', 'url' => ['/class-schedule-report']],
                                    // ['label' => Yii::t('rms', 'Advising Students'), 'icon' => 'vcard-o', 'url' => ['/semester-registration']],
                                    ['label' => Yii::t('rms', 'Course Assessment Report'), 'icon' => 'vcard-o', 'url' => ['/course-assessment-report']],
                                    ['label' => Yii::t('rms', 'Course Pre-Requisite'), 'icon' => 'vcard-o', 'url' => ['/course-prerequisit']],
                                    ['label' => Yii::t('rms', 'Out Standing Students'), 'icon' => 'vcard-o', 'url' => ['/student-semester-status-history/outstanding-students']],
                                    // ['label' => Yii::t('rms', 'Department Course'), 'icon' => 'vcard-o', 'url' => ['/department-courses']],
                                    // ['label' => Yii::t('rms', 'Course Completion'), 'icon' => 'vcard-o', 'url' => ['/course-completion']],
                                ],
                            ],

                            [
                                'label' => 'SECURITY',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Change User-name'), 'icon' => 'user-secret', 'url' => ['/site/changeusername']],
                                    ['label' => Yii::t('rms', 'Change password'), 'icon' => 'key', 'url' => ['/site/changepassword']],
                                    // ['label' => Yii::t('rms', 'Linked Accouts'), 'icon' => 'key', 'url' => ['/rms-user-link']],
                                ]
                            ],

                            [
                                'label' => 'FEEDBACK',
                                'icon' => 'share',
                                'url' => '/user-feedback',
                            ],
                            [
                                'label' => 'CONTACT',
                                'icon' => 'phone',
                                'url' => '/contact-list',
                            ],
                        ],
                    ]
                ) ?>
            <?php }
            if (Yii::$app->user->can('CEP Clerk')) {
            ?>
                <?= dmstr\widgets\Menu::widget(
                    [
                        'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                        'items' => [
                            ['label' => 'CEP CLERK', 'options' => ['class' => 'header']],
                            ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                            [
                                'label' => 'TRANSACTION',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [

                                    ['label' => Yii::t('rms', 'Approve Payment'), 'icon' => 'home', 'url' => ['/payment-request']],
                                ],
                            ],

                            [
                                'label' => 'SECURITY',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Change User-name'), 'icon' => 'user-secret', 'url' => ['/site/changeusername']],
                                    ['label' => Yii::t('rms', 'Change password'), 'icon' => 'key', 'url' => ['/site/changepassword']],
                                    // ['label' => Yii::t('rms', 'Linked Accouts'), 'icon' => 'key', 'url' => ['/rms-user-link']],
                                ]
                            ],

                            [
                                'label' => 'FEEDBACK',
                                'icon' => 'share',
                                'url' => '/user-feedback',
                            ],
                            [
                                'label' => 'CONTACT',
                                'icon' => 'phone',
                                'url' => '/contact-list',
                            ],
                        ],
                    ]
                ) ?>
                <!--  -->
            <?php }
            if (Yii::$app->user->can('Technical Assistant')) {
            ?>
                <?= dmstr\widgets\Menu::widget(
                    [
                        'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                        'items' => [
                            ['label' => 'TECHNICAL ASSISTANT', 'options' => ['class' => 'header']],
                            ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                            [
                                'label' => 'TRANSACTION',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    [
                                        'label' => Yii::t('rms', 'Semester Evaluation'),
                                        'icon' => 'home',
                                        'url' => ['/AcStEva'],
                                        ['label' => Yii::t('rms', 'Semester Evaln.(2)'), 'icon' => 'home', 'url' => ['/AcStEva']],
                                    ],
                                ],
                            ],

                            [
                                'label' => 'REPORT',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Attendance Report'), 'icon' => 'vcard-o', 'url' => ['/attendance-report2']],
                                    // ['label' => Yii::t('rms', 'Department Course'), 'icon' => 'vcard-o', 'url' => ['/department-courses']],
                                    ['label' => Yii::t('rms', 'Course Offering and enrollment'), 'icon' => 'vcard-o', 'url' => ['/course-offering2-detail']],
                                    ['label' => Yii::t('rms', 'Out Standing Students'), 'icon' => 'vcard-o', 'url' => ['/student-semester-status-history/outstanding-students']],
                                ],
                            ],

                            [
                                'label' => 'SECURITY',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Change User-name'), 'icon' => 'user-secret', 'url' => ['/site/changeusername']],
                                    ['label' => Yii::t('rms', 'Change password'), 'icon' => 'key', 'url' => ['/site/changepassword']],
                                    // ['label' => Yii::t('rms', 'Linked Accouts'), 'icon' => 'key', 'url' => ['/rms-user-link']],
                                ]
                            ],

                            [
                                'label' => 'FEEDBACK',
                                'icon' => 'share',
                                'url' => '/user-feedback',
                            ],
                            [
                                'label' => 'CONTACT',
                                'icon' => 'phone',
                                'url' => '/contact-list',
                            ],
                        ],
                    ]
                ) ?>
            <?php }
            ?>
            <?php if (Yii::$app->user->can('PGCoordinator')) {
            ?>
                <?= dmstr\widgets\Menu::widget(
                    [
                        'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                        'items' => [
                            ['label' => 'PG-COORDINATOR', 'options' => ['class' => 'header']],
                            ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                            [
                                'label' => 'TRANSACTION',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Semester Evaluation PG'), 'icon' => 'home', 'url' => ['/semester-evaluation']],
                                ],
                            ],

                            [
                                'label' => 'REPORT',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Attendance Report'), 'icon' => 'vcard-o', 'url' => ['/attendance-report2']],
                                    // ['label' => Yii::t('rms', 'Department Course'), 'icon' => 'vcard-o', 'url' => ['/department-courses']],
                                    ['label' => Yii::t('rms', 'Course Offering and enrollment'), 'icon' => 'vcard-o', 'url' => ['/course-offering2-detail']],
                                    ['label' => Yii::t('rms', 'Out Standing Students'), 'icon' => 'vcard-o', 'url' => ['/student-semester-status-history/outstanding-students']],
                                ],
                            ],

                            [
                                'label' => 'SECURITY',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Change User-name'), 'icon' => 'user-secret', 'url' => ['/site/changeusername']],
                                    ['label' => Yii::t('rms', 'Change password'), 'icon' => 'key', 'url' => ['/site/changepassword']],
                                    // ['label' => Yii::t('rms', 'Linked Accouts'), 'icon' => 'key', 'url' => ['/rms-user-link']],
                                ]
                            ],

                            [
                                'label' => 'FEEDBACK',
                                'icon' => 'share',
                                'url' => '/user-feedback',
                            ],
                            [
                                'label' => 'CONTACT',
                                'icon' => 'phone',
                                'url' => '/contact-list',
                            ],
                        ],
                    ]
                ) ?>
            <?php }
            if (Yii::$app->user->can('Commandant')) {
            ?>
                <?= dmstr\widgets\Menu::widget(
                    [
                        'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                        'items' => [
                            ['label' => 'COMMANDANT', 'options' => ['class' => 'header']],
                            ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],

                            [
                                'label' => 'REPORT',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Student Status History'), 'icon' => 'home', 'url' => ['/student-semester-status-history']],
                                    // ['label' => Yii::t('rms', 'Department Course'), 'icon' => 'vcard-o', 'url' => ['/department-courses']],
                                    ['label' => Yii::t('rms', 'Course Added'), 'icon' => 'home', 'url' => ['/add-history']],
                                    ['label' => Yii::t('rms', 'Course Assignment'), 'icon' => 'vcard-o', 'url' => ['/semester-course-assignment']],
                                    ['label' => Yii::t('rms', 'Course Drop'), 'icon' => 'vcard-o', 'url' => ['/course-drop-history']],
                                    ['label' => Yii::t('rms', 'attendance Report'), 'icon' => 'vcard-o', 'url' => ['/attendance-report2']],
                                    ['label' => Yii::t('rms', 'Admitted student'), 'icon' => 'vcard-o', 'url' => ['/admitted-students']],
                                    ['label' => Yii::t('rms', 'Enrolled student'), 'icon' => 'vcard-o', 'url' => ['/enrolled-students-report']],
                                    ['label' => Yii::t('rms', 'Grade Report'), 'icon' => 'vcard-o', 'url' => ['/semester-registration/index-grade-report']],
                                    ['label' => Yii::t('rms', 'Course reassessment'), 'icon' => 'vcard-o', 'url' => ['/reassessment-report']],
                                    ['label' => Yii::t('rms', 'Course completion check'), 'icon' => 'vcard-o', 'url' => ['/course-completion']],
                                    ['label' => Yii::t('rms', 'Graduation Class'), 'icon' => 'vcard-o', 'url' => ['/graduate-student']],
                                    ['label' => Yii::t('rms', 'Out Standing Students'), 'icon' => 'vcard-o', 'url' => ['/student-semester-status-history/outstanding-students']],
                                ],
                            ],

                            [
                                'label' => 'SECURITY',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Change User-name'), 'icon' => 'user-secret', 'url' => ['/site/changeusername']],
                                    ['label' => Yii::t('rms', 'Change password'), 'icon' => 'key', 'url' => ['/site/changepassword']],
                                    // ['label' => Yii::t('rms', 'Linked Accouts'), 'icon' => 'key', 'url' => ['/rms-user-link']],
                                ]
                            ],

                            [
                                'label' => 'FEEDBACK',
                                'icon' => 'share',
                                'url' => '/user-feedback',
                            ],
                            [
                                'label' => 'CONTACT',
                                'icon' => 'phone',
                                'url' => '/contact-list',
                            ],

                            [
                                'label' => Yii::t('rms', 'DASHBOARD'),
                                'icon' => 'bar-chart',
                                'url' => '/site/dashboard',
                                // 'items' => [
                                //     ['label' => Yii::t('rms', 'Main'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/default']],
                                //     ['label' => Yii::t('rms', 'Withdrawal'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/w-student-withdrawal-history']],
                                //     ['label' => Yii::t('rms', 'Readmission'), 'icon' => 'home', 'url' => ['/WithdrawalReadmission/w-student-readmission-history']],

                                // ],
                            ],
                        ],
                    ]
                ) ?>
            <?php }
            if (Yii::$app->user->can('UniversityRegistrar')) {
            ?>
                <?= dmstr\widgets\Menu::widget(
                    [
                        'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                        'items' => [
                            ['label' => 'UNIVERSITY REGISTRAR', 'options' => ['class' => 'header']],
                            ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                            [
                                'label' => 'CONTROLS',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Approve Student Account'), 'icon' => 'user-secret', 'url' => ['/user']],
                                ]
                            ],
                            [
                                'label' => 'REPORT',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Course Catalog'), 'icon' => 'book', 'url' => ['/course-catalog/index']],
                                    ['label' => Yii::t('rms', 'Course Pre-Requisite'), 'icon' => 'home', 'url' => ['/course-prerequisite-report']],
                                    ['label' => Yii::t('rms', 'Group Assignment'), 'icon' => 'book', 'url' => ['/w-group-assignment']],
                                    ['label' => Yii::t('rms', 'Course Assessment'), 'icon' => 'home', 'url' => ['/mark-distribution-report']],
                                    ['label' => Yii::t('rms', 'Mark Submission Progress'), 'icon' => 'home', 'url' => ['/cont-asssess-progress-report']],
                                    ['label' => Yii::t('rms', 'Semester Advisor'), 'icon' => 'home', 'url' => ['/semester-advisor']],
                                    ['label' => Yii::t('rms', 'Grade Submission Summery'), 'icon' => 'pencil', 'url' => ['/grade-submission-summery']],
                                    ['label' => Yii::t('rms', 'Makeup Exam'), 'icon' => 'file-text', 'url' => ['/makeup-exam-report']],
                                    ['label' => Yii::t('rms', 'Course Added'), 'icon' => 'home', 'url' => ['/add-history']],
                                    ['label' => Yii::t('rms', ' Academic Defficiency'), 'icon' => 'vcard', 'url' => ['/academic-defficiency']],
                                    // ['label' => Yii::t('rms', ' Graduation Elegiblity report'), 'icon' => 'vcard', 'url' => ['/graduation-elegiblity-report']],
                                    ['label' => Yii::t('rms', 'Course Assignment'), 'icon' => 'vcard-o', 'url' => ['/semester-course-assignment']],
                                    ['label' => Yii::t('rms', 'Grade Scale'), 'icon' => 'vcard-o', 'url' => ['/grade-scale']],
                                    ['label' => Yii::t('rms', 'Course Drop'), 'icon' => 'vcard-o', 'url' => ['/course-drop-report']],
                                    ['label' => Yii::t('rms', 'Attendance Report'), 'icon' => 'vcard-o', 'url' => ['/attendance-report2']],
                                    ['label' => Yii::t('rms', 'Admitted student'), 'icon' => 'vcard-o', 'url' => ['/admitted-students/report']],
                                    ['label' => Yii::t('rms', 'Enrolled student'), 'icon' => 'vcard-o', 'url' => ['/enrolled-students-report/index']],
                                    ['label' => Yii::t('rms', 'Student Semster Status History'), 'icon' => 'vcard-o', 'url' => ['/student-semester-status-history']],
                                    ['label' => Yii::t('rms', 'Withdrawal Approval Status'), 'icon' => 'vcard-o', 'url' => ['/request-withdrawal/get-approved-requests']],
                                    ['label' => Yii::t('rms', 'Out Standing Students'), 'icon' => 'vcard-o', 'url' => ['/student-semester-status-history/outstanding-students']],
                                    ['label' => Yii::t('rms', 'Course Offering'), 'icon' => 'vcard-o', 'url' => ['/course-offering-report']],
                                    ['label' => Yii::t('rms', 'Course reassessment'), 'icon' => 'vcard-o', 'url' => ['/reassessment-report']],
                                    ['label' => Yii::t('rms', 'Course completion check'), 'icon' => 'vcard-o', 'url' => ['/course-completion']],
                                ],
                            ],

                            [
                                'label' => 'SECURITY',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Change User-name'), 'icon' => 'user-secret', 'url' => ['/site/changeusername']],
                                    ['label' => Yii::t('rms', 'Change password'), 'icon' => 'key', 'url' => ['/site/changepassword']],
                                    // ['label' => Yii::t('rms', 'Linked Accouts'), 'icon' => 'key', 'url' => ['/rms-user-link']],
                                ]
                            ],

                            [
                                'label' => 'FEEDBACK',
                                'icon' => 'share',
                                'url' => '/user-feedback',
                            ],
                            [
                                'label' => 'CONTACT',
                                'icon' => 'phone',
                                'url' => '/contact-list',
                            ],
                            [
                                'label' => Yii::t('rms', 'DASHBOARD'),
                                'icon' => 'bar-chart',
                                'url' => '/site/dashboard',
                                // 'items' => [
                                //     ['label' => Yii::t('rms', 'Main'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/default']],
                                //     ['label' => Yii::t('rms', 'Withdrawal'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/w-student-withdrawal-history']],
                                //     ['label' => Yii::t('rms', 'Readmission'), 'icon' => 'home', 'url' => ['/WithdrawalReadmission/w-student-readmission-history']],

                                // ],
                            ],
                        ],
                    ]
                ) ?>
            <?php }
            if (Yii::$app->user->can('clerk')) {
            ?>
                <?= dmstr\widgets\Menu::widget(
                    [
                        'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                        'items' => [
                            ['label' => 'CLERK', 'options' => ['class' => 'header']],
                            ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                            [
                                'label' => 'MASTER FILE',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Document Uploads'), 'icon' => 'vcard', 'url' => ['/admit-student/documents']],
                                ],
                            ],
                            [
                                'label' => 'TRANSACTION',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Students'), 'icon' => 'vcard-o', 'url' => ['/student']],
                                ],
                            ],

                            [
                                'label' => 'REPORT',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Course Catalog'), 'icon' => 'book', 'url' => ['/course-catalog/index']],
                                    ['label' => Yii::t('rms', 'Course Pre-Requisite'), 'icon' => 'home', 'url' => ['/course-prerequisite-report']],
                                    ['label' => Yii::t('rms', 'Group Assignment'), 'icon' => 'book', 'url' => ['/w-group-assignment']],
                                    ['label' => Yii::t('rms', 'Course Assessment'), 'icon' => 'home', 'url' => ['/mark-distribution-report']],
                                    ['label' => Yii::t('rms', 'Semester Registration'), 'icon' => 'vcard-o', 'url' => ['/semester-registration/index-grade-report']],
                                    ['label' => Yii::t('rms', 'Mark Submission Progress'), 'icon' => 'home', 'url' => ['/cont-asssess-progress-report']],
                                    ['label' => Yii::t('rms', 'Semester Advisor'), 'icon' => 'home', 'url' => ['/semester-advisor']],
                                    ['label' => Yii::t('rms', 'Submitted Grades'), 'icon' => 'pencil', 'url' => ['/submitted-grades/index']],
                                    ['label' => Yii::t('rms', 'Grade Submission Summery'), 'icon' => 'pencil', 'url' => ['/grade-submission-summery']],
                                    ['label' => Yii::t('rms', 'Grade Summery'), 'icon' => 'pencil', 'url' => ['/grade-summery']],
                                    ['label' => Yii::t('rms', 'Makeup Exam'), 'icon' => 'file-text', 'url' => ['/makeup-exam-report']],
                                    // ['label' => Yii::t('rms', 'Department Course'), 'icon' => 'vcard-o', 'url' => ['/department-courses']],
                                    ['label' => Yii::t('rms', 'Course Added'), 'icon' => 'home', 'url' => ['/add-history']],
                                    ['label' => Yii::t('rms', 'Group / Spec. recommendations'), 'icon' => 'home', 'url' => ['/section-spec-recommendation']],
                                    ['label' => Yii::t('rms', 'Course Alergic'), 'icon' => 'home', 'url' => ['/course-alergic']],
                                    ['label' => Yii::t('rms', 'Academic Defficiency'), 'icon' => 'vcard', 'url' => ['/academic-defficiency']],
                                    // ['label' => Yii::t('rms', ' Graduation Elegiblity report'), 'icon' => 'vcard', 'url' => ['/graduation-elegiblity-report']],
                                    ['label' => Yii::t('rms', 'Course Assignment'), 'icon' => 'vcard-o', 'url' => ['/semester-course-assignment']],
                                    ['label' => Yii::t('rms', 'Grade Scale'), 'icon' => 'vcard-o', 'url' => ['/grade-scale']],
                                    ['label' => Yii::t('rms', 'Course Drop'), 'icon' => 'vcard-o', 'url' => ['/course-drop-report']],
                                    ['label' => Yii::t('rms', 'Attendance Report'), 'icon' => 'vcard-o', 'url' => ['/attendance-report2']],
                                    ['label' => Yii::t('rms', 'Semester Completion Check'), 'icon' => 'vcard-o', 'url' => ['/semester-completion-check']],
                                    ['label' => Yii::t('rms', 'Admitted student'), 'icon' => 'vcard-o', 'url' => ['/admitted-students/report']],
                                    ['label' => Yii::t('rms', 'Enrolled student'), 'icon' => 'vcard-o', 'url' => ['/enrolled-students-report/index']],
                                    ['label' => Yii::t('rms', 'Grade Report'), 'icon' => 'vcard-o', 'url' => ['/student-semester-grade-report/index']],
                                    ['label' => Yii::t('rms', 'Student Semster Status History'), 'icon' => 'vcard-o', 'url' => ['/student-semester-status-history']],
                                    ['label' => Yii::t('rms', 'Withdrawal Approval Status'), 'icon' => 'vcard-o', 'url' => ['/request-withdrawal/get-approved-requests']],
                                    ['label' => Yii::t('rms', 'Out Standing Students'), 'icon' => 'vcard-o', 'url' => ['/student-semester-status-history/outstanding-students']],
                                    ['label' => Yii::t('rms', 'Student Copy'), 'icon' => 'vcard-o', 'url' => ['/student-copy']],
                                    ['label' => Yii::t('rms', 'Course Offering'), 'icon' => 'vcard-o', 'url' => ['/course-offering-report']],
                                    ['label' => Yii::t('rms', 'Course reassessment'), 'icon' => 'vcard-o', 'url' => ['/reassessment-report']],
                                    ['label' => Yii::t('rms', 'Course completion check'), 'icon' => 'vcard-o', 'url' => ['/course-completion']],
                                ],
                            ],

                            [
                                'label' => 'SECURITY',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Change User-name'), 'icon' => 'user-secret', 'url' => ['/site/changeusername']],
                                    ['label' => Yii::t('rms', 'Change password'), 'icon' => 'key', 'url' => ['/site/changepassword']],
                                    // ['label' => Yii::t('rms', 'Linked Accouts'), 'icon' => 'key', 'url' => ['/rms-user-link']],
                                ]
                            ],

                            [
                                'label' => 'FEEDBACK',
                                'icon' => 'share',
                                'url' => '/user-feedback',
                            ],
                            [
                                'label' => 'CONTACT',
                                'icon' => 'phone',
                                'url' => '/contact-list',
                            ],
                        ],
                    ]
                ) ?>
            <?php }
            if (Yii::$app->user->can('AcademicDean')) {
            ?>
                <?= dmstr\widgets\Menu::widget(
                    [
                        'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                        'items' => [
                            ['label' => 'ACADEMIC DEAN', 'options' => ['class' => 'header']],
                            ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                            [
                                'label' => 'TRANSACTION',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    // ['label' => Yii::t('rms', 'Semester Evaluation'), 'icon' => 'home', 'url' => ['/semester-evaluation/index']],
                                    // ['label' => Yii::t('rms', 'Evaluation Report'), 'icon' => 'vcard-o', 'url' => ['/semester-evaluation/index-report']],
                                    [
                                        'label' => Yii::t('rms', 'Department Selection'),
                                        'icon' => 'calendar-o',
                                        'url' => '/Depal/default',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main Entry'), 'icon' => 'home', 'url' => ['/Depal']],
                                            ['label' => Yii::t('rms', 'Criteria Category'), 'icon' => 'home', 'url' => ['/Depal/depal-category']],
                                            ['label' => Yii::t('rms', 'Setting Entry'), 'icon' => 'home', 'url' => ['/Depal/depal-setting']],
                                            ['label' => Yii::t('rms', 'Eligible candidates'), 'icon' => 'home', 'url' => ['/Depal/depal-categorized-candidates']],
                                            ['label' => Yii::t('rms', 'Students\' choise'), 'icon' => 'home', 'url' => ['/Depal/depal-choice']],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Academic Staff Evaluation'),
                                        'icon' => 'calendar-o',
                                        'url' => '/AcStEva/default',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main Entry'), 'icon' => 'home', 'url' => ['/AcStEva']],
                                            // ['label' => Yii::t('rms', 'Eval. Criteria Category'), 'icon' => 'home', 'url' => ['/AcStEva/ase-criteria-category']],
                                            // ['label' => Yii::t('rms', 'Eval. Criteria Descrip.'), 'icon' => 'home', 'url' => ['/AcStEva/ase-criteria-description']],
                                            // ['label' => Yii::t('rms', 'Evaluation Types'), 'icon' => 'home', 'url' => ['/AcStEva/ase-evaluation-type']],
                                            // ['label' => Yii::t('rms', 'Type-Criteria-Bundle'), 'icon' => 'home', 'url' => ['/AcStEva/ase-criteria-bundle']],
                                            ['label' => Yii::t('rms', 'Semester Evaluation'), 'icon' => 'home', 'url' => ['/AcStEva/ase-semester-evaluation']],
                                            ['label' => Yii::t('rms', 'Department Schedule'), 'icon' => 'home', 'url' => ['/AcStEva/ase-department-evaluation-schedule']],
                                            ['label' => Yii::t('rms', 'Setup Entry'), 'icon' => 'home', 'url' => ['/AcStEva/ase-evaluation-control-setting']],
                                            ['label' => Yii::t('rms', 'Evaluation Result'), 'icon' => 'home', 'url' => ['/AcStEva/ase-evaluation-result']],

                                        ],
                                    ],
                                    [
                                        'label' => Yii::t('rms', 'Withdrawal/Readmission'),
                                        'icon' => 'user-secret',
                                        'url' => '/WithdrawalReadmission',
                                        'items' => [
                                            ['label' => Yii::t('rms', 'Main'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/default']],
                                            ['label' => Yii::t('rms', 'Withdrawal'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/w-student-withdrawal-history']],
                                            ['label' => Yii::t('rms', 'Readmission'), 'icon' => 'home', 'url' => ['/WithdrawalReadmission/w-student-readmission-history']],

                                        ],
                                    ],
                                ],
                            ],

                            [
                                'label' => 'REPORT',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Course Offering'), 'icon' => 'home', 'url' => ['/course-offering2-detail']],
                                    ['label' => Yii::t('rms', 'Faculty Assignment'), 'icon' => 'vcard-o', 'url' => ['/faculty-course-assignment']],
                                    ['label' => Yii::t('rms', 'Student Status History'), 'icon' => 'home', 'url' => ['/student-semester-status-history']],
                                    ['label' => Yii::t('rms', 'Course Added'), 'icon' => 'home', 'url' => ['/add-history']],
                                    ['label' => Yii::t('rms', 'Instructor load'), 'icon' => 'home', 'url' => ['/semester-course-assignment']],
                                    ['label' => Yii::t('rms', 'Department Course'), 'icon' => 'vcard-o', 'url' => ['/course-catalog']],
                                    ['label' => Yii::t('rms', 'Course History'), 'icon' => 'vcard-o', 'url' => ['/course/course-history']],
                                    ['label' => Yii::t('rms', 'Course Assignment'), 'icon' => 'vcard-o', 'url' => ['/semester-course-assignment']],
                                    ['label' => Yii::t('rms', 'Time Table'), 'icon' => 'vcard-o', 'url' => ['/ClassSchedule']],
                                    ['label' => Yii::t('rms', 'Out Standing Students'), 'icon' => 'vcard-o', 'url' => ['/student-semester-status-history/outstanding-students']],
                                    ['label' => Yii::t('rms', 'Course Drop'), 'icon' => 'vcard-o', 'url' => ['/course-drop-history']],
                                    ['label' => Yii::t('rms', 'Attendance Report'), 'icon' => 'vcard-o', 'url' => ['/attendance-report2']],
                                    ['label' => Yii::t('rms', 'Semester Evaluation Report'), 'icon' => 'vcard-o', 'url' => ['/generate-semester-evaluation-report']],
                                    ['label' => Yii::t('rms', 'Admitted student'), 'icon' => 'vcard-o', 'url' => ['/admitted-students']],
                                    ['label' => Yii::t('rms', 'Enrolled student'), 'icon' => 'vcard-o', 'url' => ['/enrolled-students-report/index']],
                                    ['label' => Yii::t('rms', 'Course reassessment'), 'icon' => 'vcard-o', 'url' => ['/reassessment-report']],
                                ],
                            ],

                            [
                                'label' => 'SECURITY',
                                'icon' => 'share',
                                'url' => '#',
                                'items' => [
                                    ['label' => Yii::t('rms', 'Change User-name'), 'icon' => 'user-secret', 'url' => ['/site/changeusername']],
                                    ['label' => Yii::t('rms', 'Change password'), 'icon' => 'key', 'url' => ['/site/changepassword']],
                                    // ['label' => Yii::t('rms', 'Linked Accouts'), 'icon' => 'key', 'url' => ['/rms-user-link']],
                                ]
                            ],

                            [
                                'label' => 'FEEDBACK',
                                'icon' => 'share',
                                'url' => '/user-feedback',
                            ],
                            [
                                'label' => 'CONTACT',
                                'icon' => 'phone',
                                'url' => '/contact-list',
                            ],
                            [
                                'label' => Yii::t('rms', 'DASHBOARD'),
                                'icon' => 'bar-chart',
                                'url' => '/site/dashboard',
                                // 'items' => [
                                //     ['label' => Yii::t('rms', 'Main'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/default']],
                                //     ['label' => Yii::t('rms', 'Withdrawal'), 'icon' => 'vcard-o', 'url' => ['/WithdrawalReadmission/w-student-withdrawal-history']],
                                //     ['label' => Yii::t('rms', 'Readmission'), 'icon' => 'home', 'url' => ['/WithdrawalReadmission/w-student-readmission-history']],

                                // ],
                            ],
                        ],
                    ]
                ) ?>
            <?php } ?>
            <?= dmstr\widgets\Menu::widget(
                [
                    'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                    'items' => [
                        ['label' => Yii::t('rms', 'Your Machin IP : ') . $_SERVER["REMOTE_ADDR"], 'options' => ['class' => 'header']],
                    ],
                ]
            ) ?>
        <?php } ?>
    </section>
</aside>