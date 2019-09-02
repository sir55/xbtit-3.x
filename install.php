<?php

/////////////////////////////////////////////////////////////////////////////////////
// xbtit - Bittorrent tracker/frontend
//
// Copyright (C) 2004 - 2019  Btiteam
//
//    This file is part of xbtit.
//
// Redistribution and use in source and binary forms, with or without modification,
// are permitted provided that the following conditions are met:
//
//   1. Redistributions of source code must retain the above copyright notice,
//      this list of conditions and the following disclaimer.
//   2. Redistributions in binary form must reproduce the above copyright notice,
//      this list of conditions and the following disclaimer in the documentation
//      and/or other materials provided with the distribution.
//   3. The name of the author may not be used to endorse or promote products
//      derived from this software without specific prior written permission.
//
// THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR IMPLIED
// WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
// MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
// IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
// SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
// TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
// PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
// LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
// NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
// EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
//
////////////////////////////////////////////////////////////////////////////////////
$dbfile = 'database.sql';
// declaration of variables
$INSTALLPATH = dirname(__FILE__);
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : 'welcome');
$allowed_actions = ['save_owner', 'welcome', 'reqcheck', 'settings', 'sql_import', 'save_mysql', 'owner', 'site_config', 'save_tracker', 'finished'];
if (!in_array($action, $allowed_actions)) {
    $action = 'welcome';
}
define('BTIT_INSTALL', true);

require_once 'include'.DIRECTORY_SEPARATOR.'xbtit_version.php';

global $tracker_version, $tracker_revision;

// getting globals
$GLOBALS['btit-tracker'] = 'xbtit';
$GLOBALS['current_btit_version'] = $tracker_version.' (r'.$tracker_revision.')';
$GLOBALS['btit_installer'] = 'xbtit Installer ::';

// getting needed files
load_lang_file();

// starting main page
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
echo '<html xmlns="http://www.w3.org/1999/xhtml">';
echo '<head>';
echo '<meta http-equiv="content-type" content="text/html; charset='.(isset($install_lang['charset']) ? $install_lang['charset'] : 'ISO-8859-1').'" />';
echo '<title>'.$GLOBALS['btit_installer'].'&nbsp;'.$GLOBALS['current_btit_version'].'</title>';
echo '<link href="style/xbtit_default/css/bootstrap.css" rel="stylesheet">';
echo '<link href="style/xbtit_default/css/modern.css" rel="stylesheet">';
echo '<link href="style/xbtit_default/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">';
echo '<!--[if lt IE 9]> <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script><script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script><![endif]-->';
echo '</head>';
echo '<body>';
echo '<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">';
echo '<div class="container">';
echo '<div class="navbar-header">';
echo '<a class="navbar-brand" href="index.php">XBTIT</a>';
echo '</div>';
echo '<div class="container">';
echo '</nav>';
echo '<div class="row">';
//echo ("<div class=\"col-md-8\">");
echo '<div class="panel panel-default">';
echo '<div class="panel-heading">';
echo '<h4><i class="fa fa-fw fa-cogs"></i>XBTIT Installation</h4>';
echo '</div>';
echo '<div class="panel-body" align="center">';

// now we can add the different pages for the installer

// Getting wished install language
function load_lang_file()
{
    global $install_lang;

    $GLOBALS['find_install_lang'] = [];

    // Make sure the languages directory actually exists.
    if (file_exists(__DIR__.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'install_lang'.DIRECTORY_SEPARATOR)) {
        // Find all the "Install" language files in the directory.
        $dir = dir(__DIR__.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'install_lang'.DIRECTORY_SEPARATOR);
        while ($entry = $dir->read()) {
            if (substr($entry, 0, 8) === 'install.' && substr($entry, -4) === '.php') {
                $GLOBALS['find_install_lang'][$entry] = ucfirst(substr($entry, 8, -4));
            }
        }
        $dir->close();
    }

    // Didn't find any, show an error message!
    if (empty($GLOBALS['find_install_lang'])) {
        step('Installation ERROR!', 'ERROR!', '*');
        echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        echo '<html xmlns="http://www.w3.org/1999/xhtml">';
        echo '<head>';
        echo '<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />';
        echo '<title>'.$GLOBALS['btit_installer'].'&nbsp;'.$GLOBALS['current_btit_version'].' - Language Error</title>';
        echo '<link rel="stylesheet" href="style/xbtit_default/main.css" type="text/css" />';
        echo '</head>';
        echo '<body style="font-family: sans-serif;"><div style="width: 600px;">';
        echo '<p>A critical language error has occurred.</p>';
        echo "<p>This installer was unable to find the installer's language file or files.  They should be found under:</p>";
        echo '<div style="margin: 1ex; font-family: monospace; font-weight: bold;">/language/install_lang/</div>';
        echo '<p>In some cases, FTP clients do not properly upload files with this many folders.  Please double check to make sure you <span style="font-weight: 600;">have uploaded all the files in the distribution</span>.</p>';
        echo '<p>If you continue to get this error message, feel free to <a href="http://www.btiteam.org/smf/index.php/">look to us for support</a>.</p>';
        echo '</div>';
        die;
    }

    // Override the language file?
    if (isset($_GET['lang_file'])) {
        $_SESSION['install_lang'] = $_GET['lang_file'];
    } elseif (isset($GLOBALS['HTTP_GET_VARS']['lang_file'])) {
        $_SESSION['install_lang'] = $GLOBALS['HTTP_GET_VARS']['lang_file'];
    } // If no language is selected, use English as the default
    else {
        $_SESSION['install_lang'] = 'install.english.php';
    }

    // Make sure it exists, if it doesn't reset it.
    if (!isset($_SESSION['install_lang']) || !file_exists(__DIR__.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'install_lang'.DIRECTORY_SEPARATOR.$_SESSION['install_lang'])) {
        // Use the first one...
        list($_SESSION['install_lang']) = array_keys($GLOBALS['find_install_lang']);

        // If we have english and some other language, use the other language.  We Americans hate english :P.
        if ($_SESSION['install_lang'] === 'install.english.php' && count($GLOBALS['find_install_lang']) > 1) {
            list($_SESSION['install_lang']) = array_keys($GLOBALS['find_install_lang']);
        }
    }

    // And now include the actual language file itself.
    require_once __DIR__.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.'install_lang'.DIRECTORY_SEPARATOR.$_SESSION['install_lang'];
}

function language_list()
{
    global $TABLE_PREFIX;

    $ret = [];
    $res = mysqli_query($GLOBALS['conn'], "SELECT * FROM {$TABLE_PREFIX}language ORDER BY language");

    while ($row = mysqli_fetch_assoc($res)) {
        $ret[] = $row;
    }

    unset($row);
    (mysqli_free_result($res) || (is_object($res) && (get_class($res) === 'mysqli_result'))) ? true : false;

    return $ret;
}

function style_list()
{
    global $TABLE_PREFIX;

    $ret = [];
    $res = mysqli_query($GLOBALS['conn'], "SELECT * FROM {$TABLE_PREFIX}style ORDER BY id");

    while ($row = mysqli_fetch_assoc($res)) {
        $ret[] = $row;
    }

    unset($row);
    (mysqli_free_result($res) || (is_object($res) && (get_class($res) === 'mysqli_result'))) ? true : false;

    return $ret;
}

//starting functions for the install
// Starting page at every step
function step($text = '', $stepname = '', $stepnumber = '')
{
    ////////// top block  //////////
    echo '<div><table class="lista" cellpadding="0" cellspacing="0" width="90%" align="center">';
    echo '<tr><td class="block" height="20px" style="padding: 5px;">';
    echo '<div style=\\"text-align: center;\\"><b>'.$text.'</b><div align="right">'.$stepname.'&nbsp;('.$stepnumber.'/5)</div></div>';
    echo '</td></tr></table></div>';
    ////////// main block //////////
    echo '<table class="lista" cellspacing="0" cellpadding="10" width="90%" align="center">';
    echo '<tr><td style="padding: 10px;" class="lista">';
}

// check if the installation is not locked
if (file_exists(__DIR__.DIRECTORY_SEPARATOR.'install.lock')) {
    step('Installation Error!', 'ERROR!', '*');
    echo "<p>For security reasons, this installer is locked!<br>Please (via FTP) remove or change the 'install.lock' file before continue.</p>";
    die;
}

// main page -> step zero
if ($action === 'welcome') {
    step($install_lang['welcome_header'], $install_lang['step'].'&nbsp;'.$install_lang['welcome_header'], '*');
    echo '<p align="center">'.$install_lang['welcome'].'</p>';

    // Show a language selection...
    if (count($GLOBALS['find_install_lang']) > 1) {
        echo '
                <div style="padding-bottom: 2ex; text-align: ', empty($install_lang['lang_rtl']) ? 'right' : 'left', ';">
                    <form action="', $_SERVER['PHP_SELF'], '" method="get">
                        ', $install_lang['installer_language'], '&nbsp;<select id="installer_language" name="lang_file" onchange="location.href = \'', $_SERVER['PHP_SELF'], '?lang_file=\' + this.options[this.selectedIndex].value;">';

        foreach ($GLOBALS['find_install_lang'] as $lang => $name) {
            echo '
                            <option', isset($_SESSION['install_lang']) && $_SESSION['install_lang'] === $lang ? ' selected="selected"' : '', ' value="', $lang, '">', $name, '</option>';
        }

        echo '
                        </select>

                        <noscript><input type="submit" value="', $install_lang['installer_language_set'], '" /></noscript>
                    </form>
                </div>';
    }
    // listing the 777 files
    echo ''.$install_lang['list_chmod'].'';
    echo '<ul>';
    echo '<li>./access_code/</li>';
    echo '<li>./cache/</li>';
    echo '<li>./include/settings.php</li>';
    echo '<li>./torrents/</li>';
    echo '<li>./badwords.txt</li>';
    echo '</ul>';

    echo ''.$install_lang['system_req'].'';
    // changelog
    echo '<p>'.$install_lang['view_log'].'&nbsp;<a href="changelog.txt" target="_blank">'.$install_lang['here'].'</a></p>';
    echo '<div align="right"><input type="button" class="button" name="continue" value="'.$install_lang['start']."\" onclick=\"javascript:document.location.href='install.php?lang_file=".$_SESSION['install_lang']."&amp;action=reqcheck'\" /></div>";
} // requirements check
elseif ($action === 'reqcheck') {
    step($install_lang['requirements_check'], $install_lang['step'].'&nbsp;'.$install_lang['reqcheck'], '1');

    // check access_code folder
    if (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'access_code')) {
        if (is_writable(dirname(__FILE__).DIRECTORY_SEPARATOR.'access_code')) {
            $accesscode = $install_lang['write_succes'];
        } else {
            $accesscode = $install_lang['write_fail'].'&nbsp;'.$install_lang['not_continue_settings3'];
        }
    } else {
        $accesscode = $install_lang['write_file_not_found'];
    }
    // check cache folder
    if (file_exists(__DIR__.DIRECTORY_SEPARATOR.'cache')) {
        if (is_writable(__DIR__.DIRECTORY_SEPARATOR.'cache')) {
            $cache = $install_lang['write_succes'];
        } else {
            $cache = $install_lang['write_fail'].'&nbsp;&nbsp;&nbsp;'.$install_lang['can_continue'];
        }
    } else {
        $cache = $install_lang['write_file_not_found'];
    }

    // check torrents folder
    if (file_exists(__DIR__.DIRECTORY_SEPARATOR.'torrents')) {
        if (is_writable(__DIR__.DIRECTORY_SEPARATOR.'torrents')) {
            $torrents = $install_lang['write_succes'];
        } else {
            $torrents = $install_lang['write_fail'].'&nbsp;&nbsp;&nbsp;'.$install_lang['can_continue'];
        }
    } else {
        $torrents = $install_lang['write_file_not_found'];
    }
    // check badwords.txt
    if (file_exists(__DIR__.DIRECTORY_SEPARATOR.'badwords.txt')) {
        if (is_writable(__DIR__.DIRECTORY_SEPARATOR.'badwords.txt')) {
            $badwords = $install_lang['write_succes'];
        } else {
            $badwords = $install_lang['write_fail'].'&nbsp;&nbsp;&nbsp;'.$install_lang['can_continue'];
        }
    } else {
        $badwords = $install_lang['write_file_not_found'];
    }
    // check include/settings.php
    if (file_exists(__DIR__.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'settings.php')) {
        if (is_writable(__DIR__.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'settings.php')) {
            $settings = $install_lang['write_succes'];
        } else {
            $settings = $install_lang['write_fail'].'&nbsp;'.$install_lang['not_continue_settings'];
        }
    } else {
        $settings = $install_lang['write_file_not_found'].'&nbsp;'.$install_lang['not_continue_settings2'];
    }

    if ((bool) ini_get('allow_url_fopen') === true) {
        $allow_url_fopen = $install_lang['allow_url_fopen_ON'];
    } else {
        $allow_url_fopen = $install_lang['allow_url_fopen_OFF'].'&nbsp;&nbsp;&nbsp;'.$install_lang['can_continue'];
    }

    echo '<h2>'.$install_lang['requirements_check'].'</h2>';
    echo '<table width="100%" cellpadding="4" cellspacing="4" border="0" style="margin-bottom: 2ex;">';
    echo '<tr><td width="40%" valign="top">'.$install_lang['access_code'].':</td><td>'.$accesscode.'</td></tr>';
    echo '<tr><td width="40%" valign="top">'.$install_lang['cache_folder'].':</td><td>'.$cache.'</td></tr>';
    echo '<tr><td width="40%" valign="top">'.$install_lang['settings.php'].':</td><td>'.$settings.'</td></tr>';
    echo '<tr><td width="40%" valign="top">'.$install_lang['torrents_folder'].':</td><td>'.$torrents.'</td></tr>';
    echo '<tr><td width="40%" valign="top">'.$install_lang['badwords_file'].':</td><td>'.$badwords.'</td></tr>';
    echo '<tr><td width="40%" valign="top">'.$install_lang['allow_url_fopen'].':</td><td>'.$allow_url_fopen.'</td></tr>';
    echo '</table>';
    // don't continue if this file doesn't exists
    if (file_exists(__DIR__.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'settings.php')) {
        if (is_writable(__DIR__.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'settings.php')) {
            echo '<div align="right"><input type="button" class="button" name="continue" value="'.$install_lang['next']."\" onclick=\"javascript:document.location.href='install.php?lang_file=".$_SESSION['install_lang']."&amp;action=settings'\" /></div>";
        }
    }
} // setting up the tracker
elseif ($action === 'settings') {
    step($install_lang['settings'], $install_lang['step'].'&nbsp;'.$install_lang['settings'], '2');

    // getting host info.
    $db_server = @ini_get('mysql.default_host') or $db_server = 'localhost';
    $db_user = isset($_POST['ftp_username']) ? $_POST['ftp_username'] : @ini_get('mysql.default_user');
    $db_name = isset($_POST['ftp_username']) ? $_POST['ftp_username'] : @ini_get('mysql.default_user');
    $db_passwd = @ini_get('mysql.default_password');
    $db_name = empty($db_name) ? 'xbtit' : $db_name;

    echo '<form action="'.$_SERVER['PHP_SELF'].'?lang_file='.$_SESSION['install_lang'].'&amp;action=save_mysql" method="post">';
    echo '<h2>'.$install_lang['mysql_settings'].'</h2><h3>'.$install_lang['mysql_settings_info'].'</h3>';
    echo '<table width="100%" cellpadding="4" cellspacing="4" border="0" style="margin-bottom: 2ex;">';
    echo '<tr><td width="20%" valign="top">'.$install_lang['mysql_settings_server'].':</td><td><input type="text" name="db_server" id="db_server_input" value="'.$db_server.'" size="30" /></td></tr>';
    echo '<tr><td valign="top">'.$install_lang['mysql_settings_username'].':</td><td><input type="text" name="db_user" id="db_user_input" value="'.$db_user.'" size="30" /></td></tr>';
    echo '<tr><td valign="top">'.$install_lang['mysql_settings_password'].':</td><td><input type="password" name="db_passwd" id="db_passwd_input" value="'.$db_passwd.'" size="30" /></td></tr>';
    echo '<tr><td valign="top">'.$install_lang['mysql_settings_database'].':</td><td><input type="text" name="db_name" id="db_name_input" value="'.$db_name.'" size="30" /></td></tr>';
    echo '<tr><td valign="top">'.$install_lang['mysql_settings_prefix'].':</td><td><input type="text" name="db_prefix" id="db_prefix_input" value="xbtit_" size="30" /></td></tr></table>';
    echo '<div align="right"><input type="submit" value="'.$install_lang['next'].'" /></div></form>';
} // saving the database connection data
elseif ($action === 'save_mysql') {
    if (empty($_POST['db_server']) || empty($_POST['db_user']) || empty($_POST['db_passwd']) || empty($_POST['db_name']) || empty($_POST['db_prefix'])) {
        step($install_lang['mysqlcheck'], $install_lang['step'].'&nbsp;'.$install_lang['mysqlcheck_step'], '2');
        echo $install_lang['no_leave_blank'];
        die;
    }
    // check settings.php file
    $random = substr(number_format(time() * rand(), 0, '', ''), 0, 10);
    if (file_exists(__DIR__.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'settings.php')) {
        if (is_writable(__DIR__.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'settings.php')) {
            $fd = fopen('include'.DIRECTORY_SEPARATOR.'settings.php', 'w');
            $foutput = "<?php\n\n";
            $foutput .= '$dbhost = "'.$_POST['db_server']."\";\n";
            $foutput .= '$dbuser = "'.$_POST['db_user']."\";\n";
            $foutput .= '$dbpass = "'.$_POST['db_passwd']."\";\n";
            $foutput .= '$database = "'.$_POST['db_name']."\";\n";
            $foutput .= '$TABLE_PREFIX = "'.$_POST['db_prefix']."\";\n";
            $foutput .= '$mySecret  = "'.$random."\";\n";
            $foutput .= "\n?>";
            fwrite($fd, $foutput);
            fclose($fd);
            step($install_lang['mysqlcheck'], $install_lang['step'].'&nbsp;'.$install_lang['mysqlcheck_step'], '2');
            echo $install_lang['mysql_settings'].'&nbsp;'.$install_lang['saved'];
            echo '<div align="right"><input type="button" class="button" name="continue" value="'.$install_lang['next']."\" onclick=\"javascript:document.location.href='install.php?lang_file=".$_SESSION['install_lang']."&amp;action=sql_import'\" /></div>";
        } else {
            echo $install_lang['file_not_writeable'];
        }
    } else {
        echo $install_lang['file_not_exists'];
    }
} // checking the database connection
elseif ($action === 'sql_import') {
    step($install_lang['mysql_import'], $install_lang['step'].'&nbsp;'.$install_lang['mysql_import_step'], '3');
    // Make sure it works.
    require dirname(__FILE__).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'settings.php';
    // Attempt a connection.
    $GLOBALS['conn'] = mysqli_connect($dbhost, $dbuser, $dbpass);
    // No dice?  Let's try adding the prefix they specified, just in case they misread the instructions ;).
    if (!$GLOBALS['conn']) {
        $mysqli_error = mysqli_error($GLOBALS['conn']);
        $GLOBALS['conn'] = mysqli_connect($dbhost, $TABLE_PREFIX.$dbuser, $dbpass);
        if ($GLOBALS['conn'] !== false) {
            $db_user = $TABLE_PREFIX.$dbuser;
            updateSettingsFile(['db_user' => $dbuser]);
        }
    }
    // Still no connection?  Big fat error message :P.
    if (!$GLOBALS['conn']) {
        echo '
                <div class="error_message">
                    <div style="color: red;">', $install_lang['error_mysql_connect'], '</div>

                    <div style="margin: 2.5ex; font-family: monospace;"><b>', $mysqli_error, '</b></div>

                    <a href="', $_SERVER['PHP_SELF'], '?step=0&amp;overphp=true">', $install_lang['error_message_click'], '</a> ', $install_lang['error_message_try_again'], '
                </div>';

        return false;
    }
    // Let's try that database on for size...
    if ($database !== '') {
        mysqli_query($GLOBALS['conn'], "CREATE DATABASE IF NOT EXISTS `$database` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
    }
    // Okay, let's try the prefix if it didn't work...
    if (!mysqli_select_db($GLOBALS['conn'], $database) && $database !== '') {
        mysqli_query($GLOBALS['conn'], '
            CREATE DATABASE IF NOT EXISTS `'.$TABLE_PREFIX.$database.'` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci');
        if (mysqli_select_db($GLOBALS['conn'], $TABLE_PREFIX.$database)) {
            $db_name = $TABLE_PREFIX.$db_name;
            updateSettingsFile(['database' => $database]);
        }
    }
    // Okay, now let's try to connect...
    if (!mysqli_select_db($GLOBALS['conn'], $database)) {
        echo '
                <div class="error_message">
                    <div style="color: red;">', sprintf($install_lang['error_mysql_database'], $database), '</div>
                    <br />
                    <a href="', $_SERVER['PHP_SELF'], '?step=0&amp;overphp=true">', $install_lang['error_message_click'], '</a> ', $install_lang['error_message_try_again'], '
                </div>';

        return false;
    }
    $replaces = ['{$db_prefix}' => $TABLE_PREFIX];
    foreach ($install_lang as $key => $value) {
        if (substr($key, 0, 8) === 'default_') {
            $replaces['{$'.$key.'}'] = addslashes($value);
        }
    }
    if (isset($replaces['{$default_reserved_names}'])) {
        $replaces['{$default_reserved_names}'] = strtr($replaces['{$default_reserved_names}'], ['\\\\n' => '\\n']);
    }
    $replaces['); -- TABLEOPT --'] = ') DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ENGINE=MyISAM;';
    // Read in the SQL.  Turn this on and that off... internationalize... etc.
    $sql_lines = explode("\n", strtr(implode(' ', file(dirname(__FILE__).DIRECTORY_SEPARATOR.'sql'.DIRECTORY_SEPARATOR.'database.sql')), $replaces));
    // Execute the SQL.
    $current_statement = '';
    $failures = [];
    $exists = [];
    foreach ($sql_lines as $count => $line) {
        // No comments allowed!
        if (substr(trim($line), 0, 1) !== '#') {
            $current_statement .= "\n".rtrim($line);
        }
        // Is this the end of the query string?
        if (empty($current_statement) || (preg_match('~;[\s]*$~s', $line) === 0 && $count !== count($sql_lines))) {
            continue;
        }
        // Does this table already exist?  If so, don't insert more data into it!
        if (preg_match('~^\s*INSERT INTO ([^\s\n\r]+?)~', $current_statement, $match) !== 0 && in_array($match[1], $exists)) {
            $current_statement = '';
            continue;
        }
        if (mysqli_query($GLOBALS['conn'], $current_statement) === false) {
            // Error 1050: Table already exists!
            if (mysqli_errno($GLOBALS['conn']) === 1050 && preg_match('~^\s*CREATE TABLE ([^\s\n\r]+?)~', $current_statement, $match) == 1) {
                $exists[] = $match[1];
            } else {
                $failures[$count] = mysqli_error($GLOBALS['conn']);
            }
        }
        $current_statement = '';
    }
    echo $install_lang['database_saved'];
    echo '<div align="right"><input type="button" class="button" name="continue" value="'.$install_lang['next']."\" onclick=\"javascript:document.location.href='install.php?lang_file=".$_SESSION['install_lang'].
        "&amp;action=site_config'\" /></div>";
} // site config
elseif ($action === 'site_config') {
    step($install_lang['site_config'], $install_lang['step'].'&nbsp;'.$install_lang['site_config_step'], '4');

    // getting started
    require __DIR__.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'settings.php';

    ($GLOBALS['conn'] = mysqli_connect($dbhost, $dbuser, $dbpass));
    ((bool) mysqli_query($GLOBALS['conn'], "USE $database"));

    // finding the host
    $host = empty($_SERVER['HTTP_HOST']) ? $_SERVER['SERVER_NAME'].(empty($_SERVER['SERVER_PORT']) || $_SERVER['SERVER_PORT'] == '80' ? '' : ':'.$_SERVER['SERVER_PORT']) : $_SERVER['HTTP_HOST'];
    // finding the base path.
    $baseurl = 'http://'.$host.substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
    $smf_lang = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, __DIR__).DIRECTORY_SEPARATOR.'smf'.DIRECTORY_SEPARATOR.'Themes'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR.'Errors.english.php';
    $ipb_lang = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, __DIR__).DIRECTORY_SEPARATOR.'ipb'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'lang_cache'.DIRECTORY_SEPARATOR.'1'.DIRECTORY_SEPARATOR.'core_public_error.php';

    echo '<form action="'.$_SERVER['PHP_SELF'].'?lang_file='.$_SESSION['install_lang'].'&amp;action=save_tracker" method="post">';
    echo '<h2>'.$install_lang['site_config'].'</h2>';
    echo '<h4>'.$install_lang['settingup'].'</h4>';
    echo '<table width="100%" cellpadding="4" cellspacing="4" border="0" style="margin-bottom: 2ex;">';
    echo '<tr><td width="20%" valign="top">'.$install_lang['sitename'].':</td><td><input type="text" name="sitename" id="sitename_input" value="'.$install_lang['sitename_input'].'" size="50" /></td></tr>';
    echo '<tr><td valign="top">'.$install_lang['siteurl'].':</td><td><input type="text" name="siteurl" id="siteurl_input" value="'.$baseurl.'" size="50" /><br /><div style="font-size: smaller; margin-bottom: 2ex;">'.$install_lang['siteurl_info'].'</div></td></tr>';
    echo '<tr><td>'.$install_lang['default_lang'].':</td><td><select name="language">';

    $lres = language_list();
    foreach ($lres as $l) {
        echo '<option value="'.$l['id'].'"'.($l['id'] == 1 ? ' selected="selected"' : '').'>'.stripslashes($l['language']).'</option>';
    }

    echo '</select></td>';
    echo '<tr><td>'.$install_lang['default_style'].':</td><td><select name="style">';

    $sres = style_list();
    foreach ($sres as $s) {
        echo '<option value="'.$s['id'].'">'.stripslashes($s['style']).'</option>';
    }
    echo '</select></td>';
    echo '<tr><td>'.$install_lang['validation'].':</td><td><select name="validation">';
    echo '<option value="none">none</option>';
    echo '<option value="user" selected="selected">user</option>';
    echo '<option value="admin">admin</option>';
    echo '</select></td>';
    echo '<tr><td>'.$install_lang['torrents_dir'].'</td><td><input type="text" name="torrentdir" size="30" value="torrents"></td></tr>';
    echo '<tr><td>'.$install_lang['forum_type'].':</td><td><select name="forumtype">';
    echo '<option value="1" selected="selected">'.$install_lang['forum_internal'].'</option>';
    echo '<option value="2">'.$install_lang['forum_smf'].'</option>';
    echo '<option value="3">'.$install_lang['forum_ipb'].'</option>';
    echo '<option value="4">'.$install_lang['forum_other'].'</option>';
    echo '</select>';
    echo "&nbsp;&nbsp;&nbsp;<input type='text' name='externalforum' size='30' maxlength='200' value='')></td></tr>";
    echo '<tr><td colspan="2">';
    echo "<p><table border='0' width='100%' align='left' bgcolor='#FFFFCC'><tr><td>".$install_lang['smf_download_a'].$smf_lang.$install_lang['smf_download_b'].'</td></tr></table></p>';
    echo '</td></tr>';
    echo '<tr><td colspan="2">';
    echo "<p><table border='0' width='100%' align='left' bgcolor='#FFFFCC'><tr><td>".$install_lang['ipb_download_a'].$ipb_lang.$install_lang['ipb_download_b'].'</td></tr></table></p>';
    echo '</td></tr>';
    echo '<tr><td colspan="2">'.$install_lang['more_settings'].'</td></tr></table>';
    echo '<div align="right"><input type="submit" value="'.$install_lang['next'].'" /></div></form>';
} // saving the site data
elseif ($action === 'save_tracker') {
    step($install_lang['site_config'], $install_lang['step'].'&nbsp;'.$install_lang['site_config_step'], '4');

    // getting variables
    $default_lang = $_POST['language'];
    $default_style = $_POST['style'];
    $baseurl = $_POST['siteurl'];
    $sitename = $_POST['sitename'];
    $torrentdir = $_POST['torrentdir'];
    $val_mode = $_POST['validation'];
    $forum_type = (int) $_POST['forumtype'];
    if ($forum_type == 1) {
        $forum = '';
    } elseif ($forum_type == 2) {
        $forum = 'smf';
    } elseif ($forum_type == 3) {
        $forum = 'ipb';
    } elseif ($forum_type == 4) {
        $_POST['externalforum'];
    }

    // getting started
    require __DIR__.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'settings.php';

    @($GLOBALS['conn'] = mysqli_connect($dbhost, $dbuser, $dbpass));
    @((bool) mysqli_query($GLOBALS['conn'], "USE $database"));

    if ($forum === 'smf') {
        $smf_lang = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, __DIR__).DIRECTORY_SEPARATOR.'smf'.DIRECTORY_SEPARATOR.'Themes'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR.'Errors.english.php';

        // Lets check the main SMF Settings file is present
        if (!file_exists(__DIR__.DIRECTORY_SEPARATOR.'smf'.DIRECTORY_SEPARATOR.'Settings.php')) {
            die($install_lang['smf_err_1']);
        }

        // Now to check they've actually installed it by checking the database
        require __DIR__.DIRECTORY_SEPARATOR.'smf'.DIRECTORY_SEPARATOR.'Settings.php';

        $smf = mysqli_query($GLOBALS['conn'], "SELECT `value` FROM `{$db_prefix}settings` WHERE variable='smfVersion'");
        if (mysqli_num_rows($smf) === 0) {
            die($install_lang['smf_err_2']);
        } else {
            $ver = mysqli_fetch_assoc($smf);
            $forum = (((int) $ver['value'][0] === 1) ? 'smf' : 'smf2');
        }

        // Now lets check if the SMF English Language file is writable
        if (!is_writable($smf_lang)) {
            die($install_lang['smf_err_3a'].$smf_lang.$install_lang['smf_err_3b']);
        }

        $filename = __DIR__.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'settings.php';
        if (file_exists($filename)) {
            if (is_writable($filename)) {
                $filesize = filesize($filename);
                $fd = fopen($filename, 'w');
                $contents = "<?php\n\n";
                $contents .= "\$dbhost = \"$dbhost\";\n";
                $contents .= "\$dbuser = \"$dbuser\";\n";
                $contents .= "\$dbpass = \"$dbpass\";\n";
                $contents .= "\$database = \"$database\";\n";
                $contents .= "\$TABLE_PREFIX = \"$TABLE_PREFIX\";\n";
                $contents .= "\$db_prefix = \"$db_prefix\";\n";
                $contents .= "\n?>";
                fwrite($fd, $contents);
                fclose($fd);
            }
        }
    } elseif ($forum === 'ipb') {
        $BASEDIR = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, __DIR__);
        $ipb_lang = $BASEDIR.DIRECTORY_SEPARATOR.'ipb'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'lang_cache'.DIRECTORY_SEPARATOR.'1'.DIRECTORY_SEPARATOR.'core_public_error.php';

        // Lets check the main IPB Config file is present
        if (!file_exists($BASEDIR.DIRECTORY_SEPARATOR.'ipb'.DIRECTORY_SEPARATOR.'conf_global.php')) {
            die($install_lang['ipb_err_1']);
        }

        // Now to check they've actually installed it by checking the database
        require $BASEDIR.DIRECTORY_SEPARATOR.'ipb'.DIRECTORY_SEPARATOR.'conf_global.php';

        $ipb = mysqli_query($GLOBALS['conn'], 'SELECT `name` FROM `'.$INFO['sql_tbl_prefix'].'members` LIMIT 1;');
        if (@mysqli_num_rows($ipb) === 0) {
            die($install_lang['ipb_err_2']);
        }

        // Let's check if the default IPB Language cache file exists
        if (!file_exists($ipb_lang)) {
            die($install_lang['ipb_err_4a'].$ipb_lang.$install_lang['ipb_err_4b']);
        }

        // Now lets check if the IPB English Language file is writable
        if (!is_writable($ipb_lang)) {
            die($install_lang['ipb_err_3a'].$ipb_lang.$install_lang['ipb_err_3b']);
        }

        $ipb_conf_writable = (is_writable($BASEDIR.DIRECTORY_SEPARATOR.'ipb'.DIRECTORY_SEPARATOR.'conf_global.php') ? true : false);

        if ($ipb_conf_writable === true) {
            $filename = $BASEDIR.DIRECTORY_SEPARATOR.'ipb'.DIRECTORY_SEPARATOR.'conf_global.php';
            $fd = fopen($filename, 'r+');
            $data = fread($fd, filesize($filename));
            ftruncate($fd, 0);
            rewind($fd);
            $search = ["\$INFO['banned_group']\t\t\t=\t'5';", "\$INFO['admin_group']\t\t\t=\t'4';", "\$INFO['guest_group']\t\t\t=\t'2';", "\$INFO['auth_group']\t\t\t=\t'1';"];
            $replace = ["\$INFO['banned_group']\t\t\t=\t'0';", "\$INFO['admin_group']\t\t\t=\t'8';", "\$INFO['guest_group']\t\t\t=\t'1';", "\$INFO['auth_group']\t\t\t=\t'2';"];
            $data = str_replace($search, $replace, $data);
            $start = strpos($data, "\$INFO['sql_tbl_prefix']");
            $end = strpos(substr($data, $start), ';') + 1;
            $data2 = substr($data, $start, $end);
            fwrite($fd, $data);
            fclose($fd);
            $data = str_replace(["\$INFO['sql_tbl_prefix']", "\t", "'"], ['$ipb_prefix', '', '"'], $data2);
            $data = str_replace('x="', 'x = "', $data);

            $filename = $BASEDIR.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'settings.php';
            if (file_exists($filename)) {
                if (is_writable($filename)) {
                    $filesize = filesize($filename);
                    $fd = fopen($filename, 'w');
                    $contents = "<?php\n\n";
                    $contents .= "\$dbhost = \"$dbhost\";\n";
                    $contents .= "\$dbuser = \"$dbuser\";\n";
                    $contents .= "\$dbpass = \"$dbpass\";\n";
                    $contents .= "\$database = \"$database\";\n";
                    $contents .= "\$TABLE_PREFIX = \"$TABLE_PREFIX\";\n";
                    $contents .= $data."\n";
                    $contents .= "\n?>";
                    fwrite($fd, $contents);
                    fclose($fd);
                } else {
                    die($install_lang['ipb_err_6'].$filename.$install_lang['ipb_err_3b']);
                }
            }
        } else {
            die($install_lang['ipb_err_5'].$BASEDIR.DIRECTORY_SEPARATOR.'ipb'.DIRECTORY_SEPARATOR.'conf_global.php'.$install_lang['ipb_err_3b']);
        }
    }

    @mysqli_query($GLOBALS['conn'], "ALTER TABLE {$TABLE_PREFIX}users CHANGE `language` `language` TINYINT( 4 ) NOT NULL DEFAULT '$default_lang'") or (is_object($GLOBALS['conn']) ? mysqli_error($GLOBALS['conn']) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
    @mysqli_query($GLOBALS['conn'], "ALTER TABLE {$TABLE_PREFIX}users CHANGE `style` `style` TINYINT( 4 ) NOT NULL DEFAULT '$default_style'") or (is_object($GLOBALS['conn']) ? mysqli_error($GLOBALS['conn']) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
    @mysqli_query($GLOBALS['conn'], "UPDATE {$TABLE_PREFIX}settings SET `value` = '$baseurl' WHERE `key` = 'url'") or (is_object($GLOBALS['conn']) ? mysqli_error($GLOBALS['conn']) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
    @mysqli_query($GLOBALS['conn'], "UPDATE {$TABLE_PREFIX}settings SET `value` = '$sitename' WHERE `key` = 'name'") or (is_object($GLOBALS['conn']) ? mysqli_error($GLOBALS['conn']) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
    @mysqli_query($GLOBALS['conn'], "UPDATE {$TABLE_PREFIX}settings SET `value` = '$torrentdir' WHERE `key` = 'torrentdir'") or (is_object($GLOBALS['conn']) ? mysqli_error($GLOBALS['conn']) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
    @mysqli_query($GLOBALS['conn'], "UPDATE {$TABLE_PREFIX}settings SET `value` = '$val_mode' WHERE `key` = 'validation'") or (is_object($GLOBALS['conn']) ? mysqli_error($GLOBALS['conn']) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));
    @mysqli_query($GLOBALS['conn'], "UPDATE {$TABLE_PREFIX}settings SET `value` = '$forum' WHERE `key` = 'forum'") or (is_object($GLOBALS['conn']) ? mysqli_error($GLOBALS['conn']) : (($___mysqli_res = mysqli_connect_error()) ? $___mysqli_res : false));

    echo $install_lang['tracker_saved'];
    echo '<div align="right"><input type="submit" name="continue" value="'.$install_lang['next']."\" onclick=\"javascript:document.location.href='install.php?lang_file=".$_SESSION['install_lang']."&amp;action=owner&amp;forumtype=$forum'\" /></div>";
} // creating owner account
elseif ($action === 'owner') {
    step($install_lang['create_owner_account'], $install_lang['step'].'&nbsp;'.$install_lang['create_owner_account_step'], '5');
    echo '<form action="'.$_SERVER['PHP_SELF'].'?lang_file='.$_SESSION['install_lang'].'&amp;action=save_owner" method="post">';
    echo '<h2>'.$install_lang['create_owner_account'].'</h2>';
    echo '<h4>'.$install_lang['create_owner_account_info'].'</h4>';
    echo '<table width="100%" cellpadding="4" cellspacing="4" border="0" style="margin-bottom: 2ex;">';
    echo '<tr><td valign="top">'.$install_lang['username'].':</td><td><input type="text" name="username" size="30" /></td></tr>';
    echo '<tr><td valign="top">'.$install_lang['password'].':</td><td><input type="password" name="password" size="30" /></td></tr>';
    echo '<tr><td valign="top">'.$install_lang['password2'].':</td><td><input type="password" name="password2" size="30" /></td></tr>';
    echo '<tr><td valign="top">'.$install_lang['email'].':</td><td><input type="text" name="email" value="email@yourhost.com" size="30" /></td></tr>';
    echo '<tr><td valign="top">'.$install_lang['email2'].':</td><td><input type="text" name="email2" value="email@yourhost.com" size="30" /></td></tr>';
    echo '<input type="hidden" name="forumtype" value="'.$_GET['forumtype'].'"/>';
    echo '</table><div align="right"><input type="submit" value="'.$install_lang['next'].'" /></div></form>';
} // saving owner account
elseif ($action === 'save_owner') {
    $forum = $_POST['forumtype'];
    step($install_lang['create_owner_account'], $install_lang['step'].'&nbsp;'.$install_lang['create_owner_account_step'], '5');

    // getting started
    require __DIR__.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'settings.php';

    @($GLOBALS['conn'] = mysqli_connect($dbhost, $dbuser, $dbpass));
    @((bool) mysqli_query($GLOBALS['conn'], "USE $database"));

    function validemail($email)
    {
        return preg_match('/^[\w.-]+@([\w.-]+\.)+[a-z]{2,6}$/is', $email);
    }

    function safe_email($email)
    {
        $email = str_replace('<', '', $email);
        $email = str_replace('>', '', $email);
        $email = str_replace("\'", '', $email);
        $email = str_replace('\"', '', $email);
        $email = str_replace('\\\\', '', $email);

        return $email;
    }

    function check_email($email)
    {
        //if(ereg("^([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$", $email))
        if (preg_match('/\b[\w\.-]+@[\w\.-]+\.\w{2,4}\b/i', $email)) {
            return true;
        } else {
            return false;
        }
    }

    function owner_error($error_lang, $back)
    {
        echo $error_lang;
        echo '<div align="right"><input type="button" class="button" name="continue" value="'.$back."\" onclick=\"javascript:document.location.href='install.php?lang_file=".$_SESSION['install_lang']."&amp;action=owner'\" /></div>";
        die;
    }
    function ipb_passgen($pwd)
    {
        $salt = '';
        $len = 5;
        mt_srand((float) microtime() * 1000000);

        for ($i = 0; $i < $len; $i++) {
            $num = mt_rand(33, 126);

            if ($num === '92') {
                $num = 93;
            }

            $salt .= chr($num);
        }
        $passhash = md5(md5($salt).md5($pwd));

        return [$passhash, $salt];
    }

    // getting variables

    $username = $_POST['username'];
    $password = $_POST['password'];
    $password_repeat = $_POST['password2'];
    $email = $_POST['email'];
    $email_repeat = $_POST['email2'];
    $email = htmlspecialchars(trim($email));
    $email_repeat = htmlspecialchars(trim($email_repeat));
    $email = safe_email($email);

    // Create Random number
    $floor = 100000;
    $ceiling = 999999;
    mt_srand((float) microtime() * 1000000);
    $random = mt_rand($floor, $ceiling);

    if (empty($username) || empty($password) || empty($password_repeat) || empty($email) || empty($email_repeat)) {
        owner_error($install_lang['no_leave_blank'], $install_lang['back']);
    }
    if (!validemail($email)) {
        owner_error($install_lang['not_valid_email'], $install_lang['back']);
    }
    if (!check_email($email)) {
        owner_error($install_lang['not_valid_email'], $install_lang['back']);
    }
    if ($password === $username) {
        owner_error($install_lang['pass_not_same_username'], $install_lang['back']);
    }
    if ($email !== $email_repeat) {
        owner_error($install_lang['email_not_same'], $install_lang['back']);
    }
    if ($password !== $password_repeat) {
        owner_error($install_lang['pass_not_same'], $install_lang['back']);
    }

    $smf_fid = 0;
    $ipb_fid = 0;

    if (substr($forum, 0, 3) === 'smf') {
        require __DIR__.DIRECTORY_SEPARATOR.'smf'.DIRECTORY_SEPARATOR.'Settings.php';

        $filename = __DIR__.DIRECTORY_SEPARATOR.'sql'.DIRECTORY_SEPARATOR.$forum.'.sql';
        $fd = fopen($filename, 'r');
        $sql = fread($fd, filesize($filename));

        $sql_lines = str_replace('{$db_prefix}', $db_prefix, explode(';', $sql));

        foreach ($sql_lines as $v) {
            @mysqli_query($GLOBALS['conn'], $v);
        }

        $smfpass = [sha1(strtolower($username).$password), substr(md5(rand()), 0, 4)];

        if ($forum === 'smf') {
            @mysqli_query($GLOBALS['conn'], "INSERT INTO `{$db_prefix}members` (`ID_MEMBER`, `memberName`, `dateRegistered`, `ID_GROUP`, `realName`, `passwd`, `emailAddress`, `memberIP`, `memberIP2`, `is_activated`, `passwordSalt`) VALUES (2 ,'$username', UNIX_TIMESTAMP(), 18, '$username', '$smfpass[0]', '$email', '".$_SERVER['REMOTE_ADDR']."', '".$_SERVER['REMOTE_ADDR']."', 1, '$smfpass[1]')");
        } else {
            @mysqli_query($GLOBALS['conn'], "INSERT INTO `{$db_prefix}members` (`id_member`, `member_name`, `date_registered`, `id_group`, `real_name`, `passwd`, `email_address`, `member_ip`, `member_ip2`, `is_activated`, `password_salt`) VALUES (2 ,'$username', UNIX_TIMESTAMP(), 18, '$username', '$smfpass[0]', '$email', '".$_SERVER['REMOTE_ADDR']."', '".$_SERVER['REMOTE_ADDR']."', 1, '$smfpass[1]')");
        }
        @mysqli_query($GLOBALS['conn'], "UPDATE `{$TABLE_PREFIX}users_level` SET `smf_group_mirror`=`id`+10");

        $smf_lang = 'smf/Themes/default/languages/Errors.english.php';

        require_once $smf_lang;

        // finding the host
        $host = empty($_SERVER['HTTP_HOST']) ? $_SERVER['SERVER_NAME'].(empty($_SERVER['SERVER_PORT']) || $_SERVER['SERVER_PORT'] == '80' ? '' : ':'.$_SERVER['SERVER_PORT']) : $_SERVER['HTTP_HOST'];
        // finding the base path.
        $baseurl = 'http://'.$host.substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));

        $txt['registration_disabled'] = "Sorry, registration via SMF is disabled. Registration for this forum must be done via the Tracker <a href=\"$baseurl/index.php?page=signup\">Here</a>.<br /><br />If you already have a tracker account please <a href=\"$baseurl/index.php?action=login\">login here</a> with the same credentials.";

        $fd = fopen($smf_lang, 'w');

        $foutput = "<?php\n\n";

        foreach ($txt as $k => $v) {
            $foutput .= "\$txt['$k']   =   '".str_replace("'", "\\'", $v)."';\n";
        }
        $foutput .= "\n?>";

        fwrite($fd, $foutput);
        fclose($fd);

        $smf_fid = 2;
    } elseif ($forum === 'ipb') {
        $BASEDIR = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, __DIR__);

        require $BASEDIR.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'settings.php';

        $filename = __DIR__.DIRECTORY_SEPARATOR.'sql'.DIRECTORY_SEPARATOR.'ipb.sql';
        $fd = fopen($filename, 'r');
        $sql = fread($fd, filesize($filename));

        $sql_lines = str_replace('{$ipb_prefix}', $ipb_prefix, explode(';', $sql));

        foreach ($sql_lines as $v) {
            @mysqli_query($GLOBALS['conn'], $v);
        }
        // Disable forum registration
        $res = mysqli_query($GLOBALS['conn'], "SELECT `cs_value` FROM `{$ipb_prefix}cache_store` WHERE `cs_key`='settings'");
        $row = mysqli_fetch_assoc($res);
        $array = unserialize($row['cs_value']);
        $array['no_reg'] = 1;
        $cs_value = serialize($array);
        @mysqli_query($GLOBALS['conn'], "UPDATE `{$ipb_prefix}cache_store` SET `cs_value`='".$cs_value."' WHERE `cs_key`='settings'");
        @mysqli_query($GLOBALS['conn'], "UPDATE {$ipb_prefix}core_sys_conf_settings` SET `conf_value`=1 WHERE `conf_key`='no_reg'");

        // Update the registration closed message to something more appropriate

        // finding the host
        $host = empty($_SERVER['HTTP_HOST']) ? $_SERVER['SERVER_NAME'].(empty($_SERVER['SERVER_PORT']) || $_SERVER['SERVER_PORT'] == '80' ? '' : ':'.$_SERVER['SERVER_PORT']) : $_SERVER['HTTP_HOST'];
        // finding the base path.
        $baseurl = 'http://'.$host.substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));

        $ipb_lang = $BASEDIR.DIRECTORY_SEPARATOR.'ipb'.DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR.'lang_cache'.DIRECTORY_SEPARATOR.'1'.DIRECTORY_SEPARATOR.'core_public_error.php';
        $fd = fopen($ipb_lang, 'r+');
        $lang_data = fread($fd, filesize($ipb_lang));
        ftruncate($fd, 0);
        rewind($fd);
        $lang_search = 'The administrator is currently not accepting new membership registrations.';
        $lang_replace = "Sorry, registration via IPB is disabled. Registration for this forum must be done via the Tracker <a target='_self' href='".$baseurl."/index.php?page=signup'>Here</a>.<br /><br />If you already have a tracker account please <a target='_self' href='index.php?app=core&module=global&section=login'>login here</a> with the same credentials.";
        $lang_data = str_replace($lang_search, $lang_replace, $lang_data);
        fwrite($fd, $lang_data);
        fclose($fd);

        if (!defined('IPS_ENFORCE_ACCESS')) {
            define('IPS_ENFORCE_ACCESS', true);
        }
        if (!defined('IPB_THIS_SCRIPT')) {
            define('IPB_THIS_SCRIPT', 'public');
        }

        require_once __DIR__.DIRECTORY_SEPARATOR.'ipb'.DIRECTORY_SEPARATOR.'initdata.php';
        require_once IPS_ROOT_PATH.'sources'.DIRECTORY_SEPARATOR.'base'.DIRECTORY_SEPARATOR.'ipsRegistry.php';
        require_once IPS_ROOT_PATH.'sources'.DIRECTORY_SEPARATOR.'base'.DIRECTORY_SEPARATOR.'ipsController.php';
        $registry = ipsRegistry::instance();
        $registry->init();

        $l_username = strtolower($username);
        $seo_username = IPSText::makeSeoTitle($username);
        $ipbpass = ipb_passgen($password);

        @mysqli_query($GLOBALS['conn'], "INSERT INTO `{$ipb_prefix}members` (`member_id`,`name`, `member_group_id`, `email`, `joined`, `ip_address`, `allow_admin_mails`, `time_offset`, `language`, `members_display_name`, `members_seo_name`, `members_created_remote`, `members_l_display_name`, `members_l_username`, `members_pass_hash`, `members_pass_salt`, `bday_day`, `bday_month`, `bday_year`, `msg_show_notification`, `last_visit`, `last_activity`, `posts`) VALUES (2, '".$username."', 8, '".$email."', UNIX_TIMESTAMP(), '".$_SERVER['REMOTE_ADDR']."', 1, 0, 1, '".$username."', '".$seo_username."', 1, '".$l_username."', '".$l_username."', '".$ipbpass[0]."', '".$ipbpass[1]."', 0, 0, 0, 1, UNIX_TIMESTAMP(), UNIX_TIMESTAMP(), 1)");
        @mysqli_query($GLOBALS['conn'], "INSERT INTO `{$ipb_prefix}pfields_content` (`member_id`) VALUES (2)");
        @mysqli_query($GLOBALS['conn'], "INSERT INTO `{$ipb_prefix}profile_portal` (`pp_member_id`, `pp_setting_count_friends`, `pp_setting_count_comments`) VALUES (2, 1, 1)");
        @mysqli_query($GLOBALS['conn'], "UPDATE `{$ipb_prefix}forums` SET `last_poster_id`='2', `last_poster_name`='".$username."' WHERE `id`=2");
        @mysqli_query($GLOBALS['conn'], "UPDATE `{$ipb_prefix}posts` SET `author_id`= '2', `author_name`='".$username."' WHERE `pid`=1");
        @mysqli_query($GLOBALS['conn'], "UPDATE `{$ipb_prefix}topics` SET `starter_id`='2', `last_poster_id`='2', `starter_name`='".$username."', `last_poster_name`='".$username."', `seo_last_name`='".$seo_username."', `seo_first_name`='".$seo_username."' WHERE `tid`=1");
        $myres = @mysqli_query($GLOBALS['conn'], "SELECT `cs_value` FROM `{$ipb_prefix}cache_store` WHERE `cs_key`='stats'");
        $myrow = mysqli_fetch_assoc($myres);
        $in = unserialize($myrow['cs_value']);
        $in['mem_count'] = 1;
        $in['last_mem_name'] = $username;
        $in['last_mem_id'] = 2;
        $in['last_mem_name_seo'] = $seo_username;
        $out = serialize($in);
        @mysqli_query($GLOBALS['conn'], "UPDATE `{$ipb_prefix}cache_store` SET `cs_value`='".$out."'  WHERE `cs_key`='stats'");
        @mysqli_query($GLOBALS['conn'], "UPDATE `{$TABLE_PREFIX}users_level` SET `ipb_group_mirror`=`id`");
        $ipb_fid = 2;
    }

    mysqli_query($GLOBALS['conn'], "INSERT INTO {$TABLE_PREFIX}users (id, username, password, random, id_level, email, joined, lastconnect, pid, time_offset, smf_fid, ipb_fid) VALUES (2, '$username', '".md5($password)."', $random, 8, '$email', NOW(), NOW(), '".md5(uniqid(rand(), true))."', 0, $smf_fid, $ipb_fid)");

    $host = empty($_SERVER['HTTP_HOST']) ? $_SERVER['SERVER_NAME'].(empty($_SERVER['SERVER_PORT']) || $_SERVER['SERVER_PORT'] == '80' ? '' : ':'.$_SERVER['SERVER_PORT']) : $_SERVER['HTTP_HOST'];
    $baseurl = 'http://'.$host.substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/'));
    $announceUrl = $baseurl.'/announce.php';
    mysqli_query($GLOBALS['conn'], "UPDATE `{$TABLE_PREFIX}settings` SET `value` = 2 WHERE `variable` = 'latestMember'");
    mysqli_query($GLOBALS['conn'], "UPDATE `{$TABLE_PREFIX}settings` SET `value` = '$username' WHERE `variable` = 'latestRealName'");
    mysqli_query($GLOBALS['conn'], "UPDATE `{$TABLE_PREFIX}settings` SET `value` = UNIX_TIMESTAMP() WHERE `variable` = 'memberlist_updated'");
    mysqli_query($GLOBALS['conn'], "UPDATE {$TABLE_PREFIX}settings SET `value` = '".base64_encode(serialize([$announceUrl]))."' WHERE `key` = 'announce'");
    mysqli_query($GLOBALS['conn'], "UPDATE {$TABLE_PREFIX}settings SET `value` = 'http://".$host.":2710/announce' WHERE `key` = 'xbtt_url'");
    mysqli_query($GLOBALS['conn'], "UPDATE {$TABLE_PREFIX}settings SET `value` = '".$email."' WHERE `key` = 'email'");
    mysqli_query($GLOBALS['conn'], "UPDATE {$TABLE_PREFIX}settings SET `value` = '".$INSTALLPATH.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR."logs' WHERE `key` = 'php_log_path'");
    echo $install_lang['create_owner_account'].'&nbsp;'.$install_lang['is_succes'];
    echo '<div align="right"><input type="button" class="button" name="continue" value="'.$install_lang['next']."\" onclick=\"javascript:document.location.href='install.php?lang_file=".$_SESSION['install_lang']."&amp;action=finished'\" /></div>";
} // finished
elseif ($action === 'finished') {
    step($install_lang['finished'], $install_lang['step'].'&nbsp;'.$install_lang['finished_step'], '*');
    echo '<h2>'.$install_lang['succes_install1'].'</h2>';
    if (!rename('install.unlock', 'install.lock') || !unlink('install.php') || !unlink('upgrade.php')) {
        echo $install_lang['succes_install2b'];
    } else {
        echo $install_lang['succes_install2a'];
    }
    echo '<br /><br />';
    echo $install_lang['succes_install3'];
    echo '<br />';
    echo '<p>BTITeam</p>';
    echo '<div align="center"><a href="index.php" target="_self">'.$install_lang['go_to_tracker'].'</a>';
}
echo "</td>\n</tr>\n</table>";
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</body>';
echo '</html>';
