<?php

/**
 * @file
 * Functionality to export VMG files to CSV.
 */

date_default_timezone_set('America/New_York');
global $_directory, $_output;

$_directory = isset($argv[1]) ? $argv[1] : NULL;
$_output = isset($argv[2]) ? $argv[2] . '.csv' : 'SMS_output_' . date('Y_m_d H_i_s') . '.csv';

if (validate_directory($_directory)) {
  $file_content = read_directory($_directory);
  $messages = read_sms($file_content);
  usort($messages, 'sort_by_date');
  write_sms($messages);
}
else {
  print_r("ERROR: Invalid Directory. \n");
  print_r("USAGE: php sms.php directory filename");
}

/**
 * Read the given directory.
 *
 * @param string $directory
 *   The name of the directory to read.
 *
 * @return array
 *   The File Content array.
 */
function read_directory($directory) {
  $file_content = array();
  $current_directory = opendir($directory);
  while (($file = readdir($current_directory)) !== FALSE) {
    if ($file == '.' || $file == '..') {
      continue;
    }
    $file_content[] = read_file($file);
  }
  closedir($current_directory);
  return $file_content;
}

/**
 * Read the given file.
 *
 * @param string $file
 *   The name of the file to read.
 *
 * @return string
 *   The File Content.
 */
function read_file($file) {
  global $_directory;
  $file_content = NULL;
  $file_handle = fopen($_directory . '/' . $file, 'r');
  while (!feof($file_handle)) {
    $file_content .= fgets($file_handle);
  }
  fclose($file_handle);
  return $file_content;
}

/**
 * Read and parse the sms content.
 *
 * @param array $file_content
 *   The file content array.
 *
 * @return array
 *   The Messages array.
 */
function read_sms($file_content) {
  $messages = array();
  foreach ($file_content as $key => $file) {
    $content = explode(PHP_EOL, $file);
    foreach ($content as $line) {
      switch (TRUE) {
        case substr($line, 0, strlen('Date:')) === 'Date:':
          $messages[$key]['Date'] = substr($line, strlen('Date:'), strlen($line));
          break;

        case substr($line, 0, strlen('X-IRMC-BOX:')) === 'X-IRMC-BOX:':
          $messages[$key]['Message Type'] = substr($line, strlen('X-IRMC-BOX:'), strlen($line));
          break;

        case substr($line, 0, strlen('TEL:')) === 'TEL:':
          $messages[$key]['Mobile'] = substr($line, strlen('TEL:'), strlen($line));
          break;

        case substr($line, 0, strlen('TEXT:')) === 'TEXT:':
          $messages[$key]['Text'] = substr($line, strlen('TEXT:'), strlen($line));
          break;

        default:
          break;
      }
    }
    ksort($messages[$key]);
  }
  return $messages;
}

/**
 * Custom array sorting for usort.
 *
 * @param array $a
 *   The array to compare.
 * @param array $b
 *   The array to compare.
 *
 * @return array
 *   The sorted messages array.
 */
function sort_by_date($a, $b) {
  return strtotime($a['Date']) > strtotime($b['Date']);
}

/**
 * Write the messages array into CSV.
 *
 * @param array $messages
 *   The messages array.
 */
function write_sms($messages) {
  global $_output;
  if (!empty($messages)) {
    $file = fopen($_output, "a");
    foreach ($messages as $message) {
      fputcsv($file, $message);
    }
    fclose($file);
    print_r('Successfully Exported into ' . $_output);
  }
}

/**
 * Validate the directory.
 *
 * @param string $directory
 *   Name of the directory.
 *
 * @return bool
 *   TRUE if the directory is valid, FALSE otherwise.
 */
function validate_directory($directory) {
  return (is_dir($directory) && !is_dir_empty($directory));
}

/**
 * Validate the directory contains any file or not.
 *
 * @param string $directory
 *   Name of the directory.
 *
 * @return bool
 *   TRUE if the directory is empty, FALSE otherwise.
 */
function is_dir_empty($directory) {
  return (count(glob($directory . "/*")) === 0);
}
