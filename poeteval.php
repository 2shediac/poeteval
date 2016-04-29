<?php
/**
 * This POET script creates the yml file required for travis.yml.
 *
 * @author    Derek.Henderson <derek.henderson@remote-learner.net>
 * @copyright 2016 Remote-Learner, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

$usage = 'Usage: poeteval [-h |--help]
               [--folder= path to folder]
               [--moodle= Specify the Moodle version. Supported versions 27, 28, 29, 30]
               [--php= Specify the PHP version. Supported versions 5.4, 5.5, 5.6, 7.0 
               [--db= Specify the database mysqli - (default) | pgsqli ]'.PHP_EOL;
    
if ($argc < 2) {
    echo $usage;
    exit(1);
}

// The arguments this script accepts.
$longopts = array(
             'folder::',
             'moodle::',
             'db::',
             'php::',
             'help::',
            );

// Key-value pair settings.
$settings = array(
             'folder' => '',
             'moodle' => '',
             'php'    => '',
             'db'     => 'mysqli',
            );

$op = getopt('h', $longopts);
if (isset($op['h']) || isset($op['help'])) {
    echo $usage;
    exit(1);
}

/* check for the folder */

if (!isset($op['folder'])) {
    echo 'No folder specified.'.PHP_EOL;
    exit(1);
}

$folder = $op['folder'];

/* check for Moodle version */
if (!isset($op['moodle'])) {
    echo 'No moodle version specified '.PHP_EOL;
    exit(1);
}

$moodle = $op['moodle'];
$supportedversions = array(
                      '28',
                      '29',
                      '30',
                     );
if (!in_array($moodle, $supportedversions)) {
    echo 'Moodle version '.$moodle.' is unsupported.'.PHP_EOL;
    exit(1);
}

if (!isset($op['php'])) {
    echo 'No php version specified '.PHP_EOL;
    exit(1);
}

$phpversion = $op['php'];
$supportedphpversions = array(
                         '5.4',
                         '5.5',
                         '5.6',
                         '7.0',
                        );
if (!in_array($phpversion, $supportedphpversions)) {
    echo 'phpversion '.$phpversion.' is unsupported.'.PHP_EOL;
    exit(1);
}

if ($moodle == '2.9' && $phpversion=='5.4.'){
    echo 'Invalid phpversion specified Moodle 2.9 requires at least php version 5.5.'.PHP_EOL;
    exit(1);
}

if ($moodle == '3.0' && $phpversion == '5.4') {
    echo 'Invalid phpversion specified Moodle 3.0 requires at least php version 5.5'.PHP_EOL;
    exit(1);
}

if ($phpversion == '7.0' && !$moodle == '30') {
    echo 'Invalid phpversion specified. Only Moodle 3.0 supports phpversion 7.0.'.PHP_EOL;
    exit(1);
}

if (!isset($op['db'])) {
    $db = 'mysqli';
} else {
    $db =$op['db'];
}

$supporteddb = array(
                'pgsql',
                'mysqli',
               );

if (!in_array($db, $supporteddb)) {
    echo 'The database type '.$db.' is unsupported.'.PHP_EOL;
    exit(1);
}
/* check to see if travis.yml file is there */

$fileyml =$folder.'/.travis.yml';
if (file_exists($fileyml)) {
    echo 'The yml file already exists. It should not be overwritten.'.PHP_EOL;
    exit(1);
}
/* see if the folder exists */
if (!is_dir($folder)){
    echo 'The folder '.$folder .' does not exist.'.PHP_EOL;
    exit(1);
    
}

/* see if folder is writeable */
if ( !is_writable($folder)){
    echo 'The folder '.$folder .' is not writable.'.PHP_EOL;
    exit(1);
}    

/* for M3.0 check version file */
if ($moodle == '30') {
   $versionfile = $folder.'/version.php';
   if (!file_exists($versionfile)) {
       echo 'The version.php file does not exist. For a Moodle 3.x plugin this must be present.'.PHP_EOL;
       exit(1);
   } 
   if (!fopen($folder.'/version.php','r')) {
       echo ' The version.php cannot be opended. '.PHP_EOL;
       exit(1);
   } else {
      $foundcomponent = 0;
      while (!feof($versionfile)) {
         $ln = fgets($versionfile);
         if (stripos($ln,'plugin->component')) {
           /* check for a valid component */
           $foundcomponent =1;
           list($header,$pname) = explode('=',$ln);
           $quote = "'";
           $posfirst = strpos($pname, $quote);
           $possecond = strpos($pname, $quote,$posfirst+1);
           if($posfirst !== false && $possecond !== false && $posfirst < $possecond){
              $pluginfullname = substr($pname, $posfirst+1, $possecond-2);
              list($plugintype,$pluginname) = explode('_',$pluginfullname);
              $supportedtypes = array(
                                 'mod',
                                 'report',
                                 'tool',
                                 'assignment',
                                 'assignsubmission',
                                 'assignfeedback',
                                 'atto',
                                 'auth',
                                 'availability',
                                 'booktool',
                                 'block',
                                 'cachestore',
                                 'cachelock',
                                 'calendartype',
                                 'format',
                                 'courseformat',
                                 'datafield',
                                 'editor',
                                 'enrol',
                                 'ltisource',
                                 'filter',
                                 'gradeexport',
                                 'gradeimport',
                                 'gradereport',
                                 'gradingform',
                                 'local',
                                 'message',
                                 'plagiarism',
                                 'portfolio',
                                 'qbehavior',
                                 'qformat',
                                 'qtype',
                                 'quizaccess',
                                 'quiz',
                                 'report',
                                 'repository',
                                 'scormreport',
                                 'search',
                                 'theme',
                                 'tinymce',
                                 'profilefield',
                                 'webservice',
                                 'workshopallocation',
                                 'workshopeval',
                                 'workshopforum',
                                );
              if (!in_array($plugintype, $supportedtypes)) {
                 echo 'Plugin type '.$plugintype.' is unsupported.'.PHP_EOL;
                 exit(1);
              }
              
           }
         } 
       } 
    }
    if ($foundcomponent === 0) {
        echo 'Component line not found in version.php. This is required for Moodle 3.x.'.PHP_EOL;
        exit(1);
    }
}
/* create the yml file */
$ymlfile = 'language: php
sudo: false
cache:
directories: 
- $HOME/.composer/cache

php:
  - '.$phpversion.' 

env:
 global:
  - DB='.$db.'
  - MOODLE_BRANCH=MOODLE_'.$moodle.'_STABLE


before_install:
  - phpenv config-rm xdebug.ini
  - cd ../..
  - composer selfupdate
  - composer create-project -n --no-dev --prefer-dist moodlerooms/moodle-plugin-ci ci ^1
  - export PATH="$(cd ci/bin; pwd):$(cd ci/vendor/bin; pwd):$PATH"

install:
  - moodle-plugin-ci install

script:
  - moodle-plugin-ci phplint
  - moodle-plugin-ci phpcpd
  - moodle-plugin-ci codechecker
  - moodle-plugin-ci csslint
  - moodle-plugin-ci shifter
  - moodle-plugin-ci jshint
  - moodle-plugin-ci validate';

$unitfolder = $folder.'/tests/';
if (is_dir($unitfolder)) {
     $filecount = 0;
     /* check for unit tests */
     $files = glob($unitfolder.'/*.php');
     if ($files) {
         $filecount = count($files);
     }
     if ($filecount > 0) {
          $ymlfile .= '
  - moodle-plugin-ci phpunit';
     }   
     
     /* see if there are any behat tests */
     
     $behatfolder = $folder .'/tests/behat/';
     if (is_dir($behatfolder)) {
         $filecount = 0;
         $behatfiles = glob($behatfolder.'/*.feature');
         if ($behatfiles > 0) {
             $ymlfile .= '
  - moodle-plugin-ci behat';
         }
     } 
}


   if (file_put_contents($fileyml, $ymlfile)){
       echo 'yml file written.'.PHP_EOL;
   }
