<?php
 /**
 * @title Misc (Miscellaneous Functions) File
 *
 * @author           Pierre-Henry SORIA <pierrehenrysoria@gmail.com>
 * @copyright        Pierre-Henry Soria, All Rights Reserved.
 * @license          Lesser General Public License (LGPL) (http://www.gnu.org/copyleft/lesser.html)
 * @version          1.7
 */

//------------------------------------------------
// Get the list of name of directories inside a directory.
//------------------------------------------------

function get_dir_list($sDir) {
    $aDirList = array();

    if($rHandle = opendir($sDir)) {
        while(false !== ($sFile = readdir($rHandle))) {
            if($sFile != '.' && $sFile != '..' && is_dir($sDir . '/' . $sFile)) {
                $aDirList[] = $sFile;
            }
        }
        closedir($rHandle);
        asort($aDirList);
        reset($aDirList);
    }
    return $aDirList;
}

// End function


//------------------------------------------------
// Check Valid Directory
//------------------------------------------------

function is_directory($sDir) {
    $sPathProtected = check_ext_start(check_ext_end(trim($sDir)));
    if(is_dir($sPathProtected)) {
        if(is_writable($sPathProtected)) {
            return true;
        }
    }

    return false;
}

// End function


//------------------------------------------------
// Check Start Extension
//------------------------------------------------

function check_ext_start ($sDir) {
    if(substr($sDir, 0, 1) != '/')
        return '/' . $sDir;
    return $sDir;
}

// End function


//------------------------------------------------
// Check End Extension
//------------------------------------------------

function check_ext_end($sDir) {
    if(substr($sDir, -1) != '/')
        return $sDir  . '/';
    return $sDir;
}

// End function


//------------------------------------------------
// Validate Username
//------------------------------------------------

function validate_username($sUsername, $iMin = 4, $iMax = 40) {
    if(empty($sUsername)) return 'empty';
    elseif(strlen($sUsername) < $iMin) return 'tooshort';
    elseif(strlen($sUsername) > $iMax) return 'toolong';
    elseif(preg_match('/[^\w]+$/', $sUsername)) return 'badusername';
    else return 'ok';
}

// End function


//------------------------------------------------
// Validate Password
//------------------------------------------------

function validate_password($sPassword, $iMin = 6, $iMax = 92) {
    if(empty($sPassword)) return 'empty';
    else if(strlen($sPassword) < $iMin) return 'tooshort';
    else if(strlen($sPassword) > $iMax) return 'toolong';
    else if(!preg_match('/[0-9]{1,}/', $sPassword)) return 'nonumber';
    else if(!preg_match('/[A-Z]{1,}/', $sPassword)) return 'noupper';
    else return 'ok';
}

// End function


//------------------------------------------------
// Validate Email
//------------------------------------------------

function validate_email($sEmail) {
    if($sEmail == '') return 'empty';
    if(filter_var($sEmail, FILTER_VALIDATE_EMAIL)== false) return 'bademail';
    else return 'ok';
}

// End function


//------------------------------------------------
// Validate Name (First Name or Last Name)
//------------------------------------------------

function validate_name($sName, $iMin = 2, $iMax = 30) {
    if(is_string($sName) && strlen($sName) >= $iMin && strlen($sName) <= $iMax)
        return true;
    return false;
}

// End function


//------------------------------------------------
// Check that all fields are filled
//------------------------------------------------

function filled_out($aVars) {
    foreach($aVars as $sKey => $sValue) {
        if((!isset($sKey)) || ($sValue == '')) {
            return false;
        }
    }
    return true;
}

// End function


//------------------------------------------------
// Identical
//------------------------------------------------

function validate_identical($sVal1, $sVal2) {
    return ($sVal1 === $sVal2);
}

// End function


//------------------------------------------------
// Redirect
//------------------------------------------------

function redirect($sUrl) {
    header('Location: ' . $sUrl);
    exit;
}

// End function


//------------------------------------------------
// Get Language of the Web Browser
//------------------------------------------------

/**
 * Get the User's Browser Language.
 * @return string The first two lowercase letter of the browser language.
 */
function get_browser_lang() {
    $aLang = explode(',' ,@$_SERVER['HTTP_ACCEPT_LANGUAGE']);
    return htmlspecialchars(strtolower(substr(chop($aLang[0]), 0, 2)));
}

// End function


//------------------------------------------------
// Delete Directory
//------------------------------------------------

function delete_dir($sPath) {
    return is_file($sPath) ?
        @unlink($sPath) :
        is_dir($sPath) ?
        array_map('delete_dir', glob($sPath.'/*')) === @rmdir($sPath) :
        false;
}

// End function


//------------------------------------------------
// Executes SQL Queries
//------------------------------------------------

function exec_file_query($oDb, $sSqlFile, $sOldPrefix = null, $sNewPrefix = null) {
       if(!is_file($sSqlFile)) return false;

       $sSqlContent = file_get_contents($sSqlFile);
       $sSqlContent = str_replace($sOldPrefi, $sNewPrefix, $sSqlContent);
       $rStmt = $oDb->exec($sSqlContent);
       unset($sSqlContent);

       return ($rStmt === false) ? $rStmt->errorInfo() : true;
}

// End function


//------------------------------------------------
// Generate ID
//------------------------------------------------

// Max 40 Characters with sha1 function
function generate_id($iLength = 40) {
    return substr(sha1(time() . getenv('REMOTE_ADDR')), 0, $iLength);
}

// End function


//------------------------------------------------
// IS URL Rewrite (.htaccess)
//------------------------------------------------

function is_url_rewrite($sDir, $sFile = '.htaccess') {
    return is_file($sDir . $sFile);
}

// End function


//------------------------------------------------
// Check Is Windows
//------------------------------------------------

function is_windows() {
    return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
}

// End function


//------------------------------------------------
// File Get Contents with CURL
//------------------------------------------------

function get_url_contents($sFile) {
    $rCh = curl_init();
    curl_setopt($rCh, CURLOPT_URL, $sFile);
    curl_setopt($rCh, CURLOPT_HEADER, 0);
    curl_setopt($rCh, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($rCh, CURLOPT_FOLLOWLOCATION, 1);
    $mResult = curl_exec($rCh);
    curl_close($rCh);
    unset($rCh);

    return $mResult;
}

// End function


//------------------------------------------------
// Zip Extract
//------------------------------------------------

function zip_extract($sFile, $sDir) {
    $oZip = new \ZipArchive;

    $mRes = $oZip->open($sFile);

    if($mRes === true)
    {
        $oZip->extractTo($sDir);
        $oZip->close();
        return true;
    }

    return false; // Return error value
}

// End function


//------------------------------------------------
// Check Valid URL
//------------------------------------------------

function check_url($sUrl) {
    // Checks if URL is valid with HTTP status code '200 OK' or '301 Moved Permanently'
    $aUrl = @get_headers($sUrl);
    return (strpos($aUrl[0], '200 OK') || strpos($aUrl[0], '301 Moved Permanently'));
}

// End function
