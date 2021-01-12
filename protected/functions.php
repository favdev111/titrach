<?php
    function sanitize_file_name( $filename ) {
	$filename_raw = $filename;
	$special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}", chr(0));
	$filename = str_replace($special_chars, '', $filename);
	$filename = preg_replace('/[\s-]+/', '-', $filename);
	$filename = trim($filename, '.-_');

	// Split the filename into a base and extension[s]
	$parts = explode('.', $filename);

	// Return if only one extension
	if ( count($parts) <= 2 )
		return $filename;

	// Process multiple extensions
	$filename = array_shift($parts);
	$extension = array_pop($parts);
	$mimes = get_allowed_mime_types();

	// Loop over any intermediate extensions. Munge them with a trailing underscore if they are a 2 - 5 character
	// long alpha string not in the extension whitelist.
	foreach ( (array) $parts as $part) {
		$filename .= '.' . $part;

		if ( preg_match("/^[a-zA-Z]{2,5}\d?$/", $part) ) {
			$allowed = false;
			foreach ( $mimes as $ext_preg => $mime_match ) {
				$ext_preg = '!^(' . $ext_preg . ')$!i';
				if ( preg_match( $ext_preg, $part ) ) {
					$allowed = true;
					break;
				}
			}
			if ( !$allowed )
				$filename .= '_';
		}
	}
	$filename .= '.' . $extension;

	return $filename;
}

function get_allowed_mime_types() {
	static $mimes = false;

	if ( !$mimes ) {
		// Accepted MIME types are set here as PCRE unless provided.
		$mimes = array(
		'jpg|jpeg|jpe' => 'image/jpeg',
		'gif' => 'image/gif',
		'png' => 'image/png',
		'bmp' => 'image/bmp',
		'tif|tiff' => 'image/tiff',
		'ico' => 'image/x-icon',
		'asf|asx|wax|wmv|wmx' => 'video/asf',
		'avi' => 'video/avi',
		'divx' => 'video/divx',
		'flv' => 'video/x-flv',
		'mov|qt' => 'video/quicktime',
		'mpeg|mpg|mpe' => 'video/mpeg',
		'txt|asc|c|cc|h' => 'text/plain',
		'csv' => 'text/csv',
		'tsv' => 'text/tab-separated-values',
		'ics' => 'text/calendar',
		'rtx' => 'text/richtext',
		'css' => 'text/css',
		'htm|html' => 'text/html',
		'mp3|m4a|m4b' => 'audio/mpeg',
		'mp4|m4v' => 'video/mp4',
		'ra|ram' => 'audio/x-realaudio',
		'wav' => 'audio/wav',
		'ogg|oga' => 'audio/ogg',
		'ogv' => 'video/ogg',
		'mid|midi' => 'audio/midi',
		'wma' => 'audio/wma',
		'mka' => 'audio/x-matroska',
		'mkv' => 'video/x-matroska',
		'rtf' => 'application/rtf',
		'js' => 'application/javascript',
		'pdf' => 'application/pdf',
		'doc|docx' => 'application/msword',
		'pot|pps|ppt|pptx|ppam|pptm|sldm|ppsm|potm' => 'application/vnd.ms-powerpoint',
		'wri' => 'application/vnd.ms-write',
		'xla|xls|xlsx|xlt|xlw|xlam|xlsb|xlsm|xltm' => 'application/vnd.ms-excel',
		'mdb' => 'application/vnd.ms-access',
		'mpp' => 'application/vnd.ms-project',
		'docm|dotm' => 'application/vnd.ms-word',
		'pptx|sldx|ppsx|potx' => 'application/vnd.openxmlformats-officedocument.presentationml',
		'xlsx|xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml',
		'docx|dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml',
		'onetoc|onetoc2|onetmp|onepkg' => 'application/onenote',
		'swf' => 'application/x-shockwave-flash',
		'class' => 'application/java',
		'tar' => 'application/x-tar',
		'zip' => 'application/zip',
		'gz|gzip' => 'application/x-gzip',
		'rar' => 'application/rar',
		'7z' => 'application/x-7z-compressed',
		'exe' => 'application/x-msdownload',
		// openoffice formats
		'odt' => 'application/vnd.oasis.opendocument.text',
		'odp' => 'application/vnd.oasis.opendocument.presentation',
		'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		'odg' => 'application/vnd.oasis.opendocument.graphics',
		'odc' => 'application/vnd.oasis.opendocument.chart',
		'odb' => 'application/vnd.oasis.opendocument.database',
		'odf' => 'application/vnd.oasis.opendocument.formula',
		// wordperfect formats
		'wp|wpd' => 'application/wordperfect',
		) ;
	}

	return $mimes;
}

function remove_trailing_slash($string)
{
	return rtrim(trim($string),'/\\');
}

/**
 *Get different between times
 */
function getTimeDiff($time1,$time2, $format = '%i')
{
	if(!is_object($time1))
		$time1 = new DateTime($time1);
	if(!is_object($time2))
		$time2 = new DateTime($time2);
	if($time1 and $time2)
	{
		$interval = $time1->diff($time2);
		return $interval->h * 60 + $interval->i;
		//return $interval->format($format);
	}
	return false;
}

function convertMinToHrs($mm)
{
	return sprintf("%02dh %02dm", floor($mm/60), $mm%60);
}

// The excerpt based on words
if(!function_exists("string_limit_words")){
	function string_limit_words($string, $word_limit){
	  $words = explode(' ', $string, ($word_limit + 1));
	  $addDots = '';
	  if(count($words) > $word_limit)
	  {
		array_pop($words);
		$addDots = '...';
	  }
	  
	  
	  return implode(' ', $words).$addDots;
	}
}


function x_week_range($date) {
    $ts = DateTime::createFromFormat('m/d/Y',$date)->getTimestamp();
    $start = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
    return array($start,strtotime('next saturday', $start));
}
