<?php
/*
Goatee is a ideological sibling of Mustache Templates. 
It shares the basic syntax of Mustache, but implements a few new awesome features 
which make templating much easier and removes some features which are unneccessary.

template tags documented at https://github.com/owenallenaz/Goatee

Ported from Javascript to PHP by Aaron Queen (https://github.com/bompus)

usage:
$html = '<div>{{name}}</div>';
$data = array('name' => 'Aaron');
$filled = Goatee::fill($html, $data); // filled should contain '<div>Aaron</div>'
*/

class wpcr_Goatee {
	private static function context($html) {
		$html_len = strlen($html);
		$rtn = new stdClass();
		$rtn->tags = array();
		$rtn->start = 0;
		$rtn->inner = $html;
		$rtn->innerStart = 0;
		$rtn->innerEnd =  $html_len;
		$rtn->end = $html_len;
		return $rtn;
	}
	
	private static function tag($label, $type, $start, $end, $innerStart, $innerEnd, $tags) {
		$rtn = new stdClass();
		$rtn->label = $label;
		$rtn->type = $type;
		$rtn->start = $start;
		$rtn->end = $end;
		$rtn->innerStart = $innerStart;
		$rtn->innerEnd = $innerEnd;
		$rtn->tags = $tags;
		return $rtn;
	}
	
	public static function fill($html, $data) {
		$currentHTML = $html;
		
		$context = self::context($html);
		$myContext = & $context;
		$previousContexts = array();
		
		$numMatched = preg_match_all('/\{\{([#!:%\/-]?)(.*?)\}\}/', $currentHTML, $matches, PREG_OFFSET_CAPTURE);			
		if ($numMatched === 0) {
			return self::processTags($html, $context, array($data));
		}
		
		foreach ($matches[0] as $idx => $match) {
			$match_type = & $matches[1][$idx][0];
			$match_name = & $matches[2][$idx][0];
			$match_len = strlen($match[0]);
			$match_start = & $match[1];
			$match_end = $match_start + $match_len;
			
			if ($match_type !== '/') {
				$myContext->tags[] = self::tag( $match_name, $match_type, $match_start, $match_end, $match_end, '', array() );
				
				if ($match_type !== '' && $match_type !== '%') {
					$previousContexts[] = $myContext;
					$my_idx = count($myContext->tags) - 1;
					$myContext = $myContext->tags[$my_idx];
				}
			} else {
				$myContext->end = $match_end;
				$myContext->innerEnd = $match_start;
				$myContext->inner = substr($html, $myContext->innerStart, $myContext->innerEnd - $myContext->innerStart);
				$my_idx = count($previousContexts) - 1;
				$myContext = $previousContexts[$my_idx];
				array_splice($previousContexts, $my_idx, 1);
			}
			
			$temp = str_repeat('~', $match_len);
			$currentHTML = str_replace($match[0], $temp, $currentHTML);
		}
		
		return self::processTags($html, $context, array($data));
	}
	
	private static function processTags($html, $context, $data) {	
		$returnArray = array();
		$last_data_idx = count($data) - 1;
		$position = $context->innerStart;
		
		foreach($context->tags as $idx => $tag) {		
			$returnArray[] = substr($html, $position, $tag->start - $position);			
			$position = $tag->end;
			
			if ($tag->type === '-') {
				if ($last_data_idx > 0) {
					$newData = array_merge(array(), $data);
					array_splice($newData, $last_data_idx, 1);
					$returnArray[] = self::processTags($html, $tag, $newData);
				}
				continue;
			}
			
			$myData = false;
			$myData_defined = false;
			$label = $tag->label;
			if ( is_array($data[$last_data_idx]) && isset($data[$last_data_idx][$label]) ) {
				$myData_defined = true;
				$myData = $data[$last_data_idx][$label];
			} else if ( is_object($data[$last_data_idx]) && isset($data[$last_data_idx]->$label) ) {
				$myData_defined = true;
				$myData = $data[$last_data_idx]->$label;
			}
			$myData_type = gettype($myData);
			
			if ($tag->type === '' || $tag->type === '%') {	
				if ($myData_defined === false) {
					// do nothing
				} else if ($myData_type === 'string' || $myData_type === 'integer' || $myData_type === 'double') {
					// standard tags
					if ($tag->type === '') {
						$returnArray[] = $myData;
					} else if ($tag->type === '%') {
						$returnArray[] = htmlspecialchars($myData);
					}
				}
				else if (
						( ($myData_type === 'array') && isset($myData['template']) && isset($myData['data']) ) || 
						( ($myData_type === 'object') && isset($myData->template) && isset($myData->data) )
					) {
					// passing a template and data structure
					
					$myData_data = ($myData_type === 'array') ? $myData['data'] : $myData->data;
					$myData_template = ($myData_type === 'array') ? $myData['template'] : $myData->template;
					$myData_date_type = gettype($myData_data);
					
					// Is array loop over array
					if ($myData_date_type === 'array') {
						foreach($myData_data as $idx2 => $val2) {
							$returnArray[] = self::fill($myData_template, array($idx2 => $val2));
						}
					} else {
						$returnArray[] = self::fill($myData_template, $myData_data);
					}
				}
			} else if ($tag->type === '#') {
				if ($myData_defined === true) {
					if ($myData_type === 'array') {
						foreach ($myData as $idx3 => $val3) {
							$newData = array_merge(array(), $data);
							$newData[] = $val3;
							$returnArray[] = self::processTags($html, $tag, $newData);
						}
					} else if ($myData_type === 'object' && count(get_object_vars($myData)) !== 0) {
						$newData = array_merge(array(), $data);
						$newData[] = $myData;
						$returnArray[] = self::processTags($html, $tag, $newData);
					}
				}
			} else if ($tag->type === ':') {
				if (
						$myData_defined === true &&
						(
							($myData_type === 'string' && $myData !== '' && $myData !== 'false') || 
							($myData_type === 'array' && count($myData) > 0) ||
							($myData_type === 'object' && count(get_object_vars($myData)) !== 0) ||
							($myData_type === 'boolean' && $myData !== false) ||
							($myData_type === 'integer' || $myData_type === 'double')
						)
				) {
					$returnArray[] = self::processTags($html, $tag, $data);
				}
			} else if ($tag->type === '!') {
				if (
						$myData_defined === false || 
						(
							($myData_type === 'string' && ($myData === '' || $myData === 'false')) ||
							($myData_type === 'array' && count($myData) === 0) ||
							($myData_type === 'object' && count(get_object_vars($myData)) === 0) ||
							($myData_type === 'boolean' && $myData === false)
						)
				) {
					$returnArray[] = self::processTags($html, $tag, $data);
				}
			}
		}
		
		if ($position < $context->end) {
			$returnArray[] = substr($html, $position, $context->innerEnd - $position);
		}
		
		return implode('', $returnArray);
	}
	
	public static function unpreserve($html) {
		return preg_replace('/\{\{\$/', '{{', $html);
	}
	
	public static function preserve($html) {
		return preg_replace('/\{\{/', '{{$', $html);
	}
}
?>