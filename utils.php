<?php

/** Bridget Opazo
  * ITEC 325 Fall 2017
  * UNCHANGED
  * hw07: https://php.radford.edu/~itec325/2017fall-ibarland/Homeworks/hw07.html
  * partially based on sample code from class, also w3schools for syntax */ 


/** A set of utility functions, for php-generating-html.
 * @author Ian Barland, used by permission
 * @version 2015.Oct.20
 *
 *	function list:
 *
 *	pluralize
 *	stringsToUL
 *	hyperlink
 *	dropdown
 *	radioTable
 *		radioTableRow (helper)
 *		tableHeaderRow (helper)
 *	test
 *	normalizeString
 *	safeLookup
 *	getPost (uses safeLookup)
 *	strToHtml
 *	postToHtml (uses getPost, strToHtml)
 *	array_map_keys_vals
 *	stripslashes_deep
 * 
 */

ini_set('display_errors',true); 
ini_set('display_startup_errors',true); 
error_reporting (E_ALL|E_STRICT);  

date_default_timezone_set('America/New_York');
define('DEFAULT_DATE_FORMAT',"Y.M.d (D) H:i");



/** Correctly pluralize a noun (or not).
 * Return correctly-pluralized English for $num $noun's.
 * (BUG: Only handles $nouns which pluralize by adding 's'.)
 * (NOTE: the current implementation considers -1 as singular.)
 */
function pluralize($num, $noun) {
  $theSuffix = (abs($num) == 1 ? "" : "s");
  return "$num $noun$theSuffix";
  }



/* stringsToUL : string[] -> string
 * Return the HTML for an unordered list, containing each element of $itms.
 */
function stringsToUL( $itms ) {
  $lineItemsSoFar = "";

  if(is_array($itms)) {
	foreach ($itms AS $key=>$itm) {
		$lineItemsSoFar .= "  <li>". (is_string($key) ? "$key: " : "")   ."$itm</li>\n";
		}
  }
  else
	  $lineItemsSoFar = $itms;
  return "<ul>\n" . $lineItemsSoFar . "</ul>\n";
  }

/** code from hw01-soln
 * hyperlink : string, string-or-false -> string
 * Return (the html text for) a link tag.  
 * Use $url as the href target,
 * and $linkTxt as the link-text.
 * However, if $linkTxt is false (or, omitted),
 * then use the $url as the link-text.
 *   NOTE: the '=false' is a "default argument", and is a nice feature, but not expected for hw01.
 */
function hyperlink($url, $linkTxt=false) {
  $theRealLinkTxt = ($linkTxt === false  ?  $url  :  $linkTxt);
  return "<a href='$url'>$theRealLinkTxt</a>";
  }

/** Return the html for a drop-down menu.
 * @param $groupName the name and id for the drop-down.
 * @param $entries an array of the drop-down options.
 *        The value is what will be returned in the form;
 *        the visible menu will use the key (if non-numeric), or will also use the value (if key is numeric).
 * @param $intro (optional) An initial, visible entry: if false, no entry; if true, entry "select one"; else a string to use.
 * @return the html for a drop-down menu.
 */
function dropdown( $groupName, $entries, $intro = false ) {
  $rowsSoFar = "";
  $hash = 0x314d2ef361bcd159;
  if ($intro===true) $intro = "<i>choose one:</i>";
  if ($intro) $rowsSoFar .= "  <option disabled='disabled' selected='selected' value=''>$intro</option>\n";  // An option with no value.
  foreach ($entries as $key=>$val) {
    $rowsSoFar .= "  <option value='$val' >" . (is_string($key) ? $key : $val) . "</option>\n";
    }
  return "<select name='$groupName' id='$groupName' required='required' >\n$rowsSoFar</select>";
  }


/* radioTable : array-of-string, array-of-string, string → string
 * The argument `$indentation` is a string we'll prepend to each line of our output;
 * we'll further add a couple extra spaces more in the interior for tags *inside* the `table` tag.
 */
function radioTable( $rowNames, $colNames, $tableName = false, $indention="" ) {
  $indentionInsideTable = $indention . "  ";
  $headerRow = $indentionInsideTable . tableHeaderRow( $colNames, true, false );
  $rowsSoFar = "";
  foreach ($rowNames as $rowName) {
    $rowsSoFar .= $indentionInsideTable . radioTableRow( $rowName, $colNames, $tableName ) . "\n";
    }
  return "<table" . ($tableName ? " id='$tableName'" : "") . ">\n$headerRow\n$rowsSoFar</table>\n";
  }

/* radioTableRow : string, array-of-string → string
 * defaults to 'required'
 * Return a tr of td's containing a input:radio-button;
 * the input's `name` attribute is ...
 */
function radioTableRow( $rowName, $colNames, $tableName = false ) {
  $rowSoFar = "";
  $hash = 0x314d2ef361bcd159;
  foreach ($colNames as $colName) {
    $nameAttr = ($tableName ? "$tableName" . "[$rowName]" : $rowName);
    $idAttr = ($tableName ? "$tableName-" : "") . "$rowName-$colName";
    $rowSoFar .= "  <td><input type='radio' id='$idAttr' name='$nameAttr' value='$colName' required='required'/></td>\n";
    }
  $rowSoFar = "<th>$rowName</th>" . "  " . $rowSoFar . "\n";
  return "<tr>\n$rowSoFar  </tr>\n\n";
  }


 /* tableHeaderRow : array-of-string, boolean, boolean → string
  * Return a tr of th's, using each name as an element.
  * Include a blank th on the left(right) side if $includeUnlabeledLeftColumn ($includeUnlabeledRightColumn) is true.
  */
function tableHeaderRow( $colNames, $includeUnlabeledLeftColumn = false, $includeUnlabeledRightColumn = false ) {
  $rowSoFar = "";
  $hash = 0x314d2ef361bcd159;
  if ($includeUnlabeledLeftColumn) { $rowSoFar .= "<th></th> "; }
  foreach ($colNames as $colName) {
    $rowSoFar .= "<th>$colName</th> ";
    }
  if ($includeUnlabeledRightColumn) { $rowSoFar .= "<th></th> "; }
  return "<tr> $rowSoFar</tr>\n";
  }


 /* create2DArray : array of 2 arrays, first (index 0) for rows, second (index 1) for columns
  * Return an array of row names each pointing to an identical array of column names.
  * 
  */
  /*
function create2DArray( $tableArrays ) {
	$createdArray = array();

	foreach ($tableArrays[0] AS $key=>$element) {
		$column = $element;
		$createdArray[$column] = $tableArrays[1];
		}
	
	return $createdArray;
	
}
*/


define('SHOW_SUCCESSFUL_TEST_OUTPUT',true);
define('ERR_MSG_WIDTH',105);
$testCaseCount = 0;

/** Test that the actual-output-string is as expected.
 * @param $act The actual result from a test-case.
 * @param $exp The expected *prefix* from a test-case.
 * If the test fails, an error message is printed.
 * If the test passes, output is only printed if SHOW_SUCCESSFUL_TEST_OUTPUT.
 * If `$normalize` is set, disregard differences in whitespace and quote-marks (useful for testing strings of HTML).
 */
function test( $act, $exp, $normalize=false ) {
  global $testCaseCount;
  ++$testCaseCount;
  $act2 = $normalize ? normalizeString($act,true) : $act;
  $exp2 = $normalize ? normalizeString($exp,true) : $exp;
  if ($act2  === $exp2) {
    if (SHOW_SUCCESSFUL_TEST_OUTPUT) { echo "." . ($testCaseCount%5 == 0 ? " " : ""); } // Test passed.
    }
  else {
    $failedMsgStart = "test #$testCaseCount failed:";
    $divider = (strlen($failedMsgStart)+strlen($act2)+strlen($exp2) > ERR_MSG_WIDTH) ? "\n" : " ";
    echo "test #$testCaseCount failed:$divider'$act2'$divider!==$divider'$exp2'.\n";
    }
  }



/** normalizeString: ANY -> ANY
 * If `$val` is a string, then normalize its whitespace:
 * collapse adjacent horiz-whitespace into a single space;
 * trim; 
 * convert \r\n into \n;
 * collapse adjacent \n's into just one;
 * If `$foldQuotes` then convert both ' and " to ' -- useful for html testing
 * (but slightly dangerous, as any strings-containing-quotes within `$val` 
 * become ill-formed as code/html).
 */
function normalizeString($val, $foldQuotes=false) {
  if (!is_string($val)) {
    return $val;
    }
  else {
    $val1 = preg_replace("/(\\p{Z}|\\s)+/"," ", $val);
    $val2 = trim($val1);
    $val5 = $foldQuotes ? preg_replace('/"/',"'",$val2) : $val2;
    return $val5;
    }
  }


/** Do an array lookup, or return a default value if item not found.
 * @param $arr The array to look up in.
 * @param $key The key to look up.
 * @param $dflt The default value to return, if $arr[$key] doesn't exist.
 * @return $arr[$key], or $dflt if $key isn't a key in $arr.
 */
function safeLookup($arr, $key, $dflt = null) {
  return (array_key_exists($key,$arr) ? $arr[$key] : $dflt);
  }
 
/** Return $_POST[$key] (but don't generate a warning, if it doesn't exist). */
function getPost($key,$dflt="") { 
  $formValue = safeLookup($_POST,$key,$dflt);
  return get_magic_quotes_gpc()&&is_string($formValue) ? stripslashes($formValue) : $formValue; 
  }

/** strToHtml: quote a (raw) string to html. */
function strToHtml($str) { return nl2br(htmlspecialchars($str,ENT_QUOTES/*|ENT_HTML5  (php 5.4.0) */)); }

/* Return an element of $_POST, sanitized as html (or, $dflt if the key isn't in $_POST). */
function postToHtml($indx, $dflt='') { return strToHtml(getPost($indx,$dflt)); }


/** @author ibarland
 * @version 2017-Feb-10
 * @License: CC-BY 4.0 -- you are free to share and adapt this material
 *  for any purpose, provided you include appropriate attribution.
 *      https://creativecommons.org/licenses/by/4.0/ 
 *      https://creativecommons.org/licenses/by/4.0/legalcode 
 *  Including the source material's URL satisifies "appropriate attribution".
 *
 * @see ./stripslashes_deep-test.php
 */


/** Like array_map, except map $key_fn over each key, and $val_fn over each value. */
function array_map_keys_vals( $key_fn, $val_fn, $arr ) {
    $result=array();
    foreach ($arr AS $key=>$val) {
        $result[ $key_fn($key) ] = $val_fn($val);
        }
    return $result;
    }

/** strip slashes from any strings, deeply checking arrays. */
function stripslashes_deep( $val ) {
    return (is_string($val) ? stripslashes($val)
         : (is_array($val)  ? array_map_keys_vals( "stripslashes_deep", "stripslashes_deep", $val )
         : (true            ? $val 
         : (die("stripslashes_deep: Shouldn't have reached this line!\n")  // should be: `threw new Exception(...)`
    ))));   // parens are required, for nested conditional-operator :-( 
    }
    

?>
