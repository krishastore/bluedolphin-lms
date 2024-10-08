<?php
/**
 * Define global constants.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms
 *
 * phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
 */

namespace BlueDolphin\Lms;

const BDLMS_SCRIPT_HANDLE = 'bdlms-';
const PARENT_MENU_SLUG    = 'bluedolphin-lms';
// Define constants for custom post types.
const BDLMS_COURSE_CPT   = 'bdlms_course';
const BDLMS_LESSON_CPT   = 'bdlms_lesson';
const BDLMS_ORDER_CPT    = 'bdlms_order';
const BDLMS_QUESTION_CPT = 'bdlms_question';
const BDLMS_QUIZ_CPT     = 'bdlms_quiz';
const BDLMS_RESULTS_CPT  = 'bdlms_results';

// Define constants for custom taxonomies.
const BDLMS_COURSE_CATEGORY_TAX   = 'bdlms_course_category';
const BDLMS_COURSE_TAXONOMY_TAG   = 'bdlms_course_tag';
const BDLMS_QUESTION_TAXONOMY_TAG = 'bdlms_quesion_topics';
const BDLMS_QUIZ_TAXONOMY_LEVEL_1 = 'bdlms_quiz_level_1';
const BDLMS_QUIZ_TAXONOMY_LEVEL_2 = 'bdlms_quiz_level_2';
const BDLMS_LESSON_TAXONOMY_TAG   = 'bdlms_lesson_topics';

// Question meta keys.
const META_KEY_QUESTION_PREFIX   = '_bdlms_question';
const META_KEY_QUESTION_TYPE     = META_KEY_QUESTION_PREFIX . '_type';
const META_KEY_QUESTION_SETTINGS = META_KEY_QUESTION_PREFIX . '_settings';
const META_KEY_QUESTION_GROUPS   = META_KEY_QUESTION_PREFIX . '_groups';
const META_KEY_RIGHT_ANSWERS     = META_KEY_QUESTION_PREFIX . '_%s_answers';
const META_KEY_ANSWERS_LIST      = META_KEY_QUESTION_PREFIX . '_%s';
const META_KEY_MANDATORY_ANSWERS = META_KEY_QUESTION_PREFIX . '_mandatory_answers';
const META_KEY_OPTIONAL_ANSWERS  = META_KEY_QUESTION_PREFIX . '_optional_answers';
const META_KEY_QUESTION_QUIZ_IDS = META_KEY_QUESTION_PREFIX . '_quiz_ids';

// Quiz meta keys.
const META_KEY_QUIZ_PREFIX       = '_bdlms_quiz';
const META_KEY_QUIZ_QUESTION_IDS = META_KEY_QUIZ_PREFIX . '_question_ids';
const META_KEY_QUIZ_SETTINGS     = META_KEY_QUIZ_PREFIX . '_settings';
const META_KEY_QUIZ_GROUPS       = META_KEY_QUIZ_PREFIX . '_groups';

// Lesson meta keys.
const META_KEY_LESSON_PREFIX     = '_bdlms_lesson';
const META_KEY_LESSON_SETTINGS   = META_KEY_LESSON_PREFIX . '_settings';
const META_KEY_LESSON_MEDIA      = META_KEY_LESSON_PREFIX . '_media';
const META_KEY_LESSON_MATERIAL   = META_KEY_LESSON_PREFIX . '_material';
const META_KEY_LESSON_COURSE_IDS = META_KEY_LESSON_PREFIX . '_course_ids';

// Course meta keys.
const META_KEY_COURSE_PREFIX      = '_bdlms_course';
const META_KEY_COURSE_INFORMATION = META_KEY_COURSE_PREFIX . '_information';
const META_KEY_COURSE_ASSESSMENT  = META_KEY_COURSE_PREFIX . '_assessment';
const META_KEY_COURSE_MATERIAL    = META_KEY_COURSE_PREFIX . '_material';
const META_KEY_COURSE_CURRICULUM  = META_KEY_COURSE_PREFIX . '_curriculum';
const META_KEY_COURSE_SIGNATURE   = META_KEY_COURSE_PREFIX . '_signature';

// Frontend nonce.
const BDLMS_LOGIN_NONCE             = '_bdlms_login';
const BDLMS_FILTER_NONCE            = '_bdlms_filter';
const BDLMS_QUESTION_VALIDATE_NONCE = '_bdlms_question_validate';

// User meta keys.
const BDLMS_COURSE_STATUS       = '_bdlms_%d_course_status';
const BDLMS_LESSON_VIEW         = '_bdlms_lesson_view_%d';
const BDLMS_COURSE_COMPLETED_ON = '_bdlms_%d_course_completed_on';
const BDLMS_ENROL_COURSES       = '_bdlms_enrol_courses';

// Define constant for setting.
const BDLMS_SETTING = 'bdlms-setting';

// Import meta key.
const META_KEY_IMPORT = '_bdlms_import_id';

// Tables.
const BDLMS_CRON_TABLE = 'bdlms_cron_jobs';
