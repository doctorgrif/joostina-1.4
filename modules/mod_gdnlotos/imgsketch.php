<?php

/**
 * @package     GDNLotos
 * @copyright   Авторские права (C) 2000-2013 Gold Dragon.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @description Модуль позволяет выводить основные материалы по определённым критериям для Joostina 1.4.3.x
 * @see         http://wiki.joostina-cms.ru/index.php/GDNLotos
 */

if(!isset($_REQUEST["src"])){
    die("no image specified");
}

$src = $_REQUEST["src"];
$root = getDocumentRoot();

if(!function_exists('imagecreatetruecolor')){
    die("GD Library Error: imagecreatetruecolor does not exist");
}

if(strlen($src)){
    $new_width = get_request('w', 700);
    $new_height = get_request('h', 350);
    $quality = get_request('q', 75);

    if(!is_dir($root . '/cache/mod_gdnlotos/')){
        mkdir($root . '/cache/mod_gdnlotos/', 0755);
    }
    $cache_dir = $root . '/cache/mod_gdnlotos/';

    $info_img = getimagesize($src);

    // размеры картинки
    $width = $info_img[0];
    $height = $info_img[1];

    // mime
    if($info_img['mime']=='image/gif'){
        $image = imagecreatefromgif($src);
    }elseif($info_img['mime']=='image/png'){
        $image = imagecreatefrompng($src);
    }else{
        $image = imagecreatefromjpeg($src);
    }

    $canvas = imagecreatetruecolor($new_width, $new_height);
    imagecopyresampled($canvas, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    show_image($canvas, $quality, $cache_dir);
    imagedestroy($canvas);
} else{
    die($src . ' not found.');
}

function show_image($canvas, $quality, $cache_dir){
    $is_writable = 0;
    $cache_file_name = $cache_dir . '/' . get_cache_file();
    if(touch($cache_file_name)){
        chmod($cache_file_name, 0666);
        $is_writable = 1;
    } else{
        $cache_file_name = NULL;
        header('Content-type: image/jpeg');
    }
    imagejpeg($canvas, $cache_file_name, $quality);

    if($is_writable){
        show_cache_file($cache_dir);
    }
}

function get_request($property, $default = 0){
    if(isset($_REQUEST[$property])){
        return $_REQUEST[$property];
    } else{
        return $default;
    }
}

function show_cache_file($cache_dir){
    $cache_file = $cache_dir . '/' . get_cache_file();
    if(file_exists($cache_file)){
        if(isset($_SERVER["HTTP_IF_MODIFIED_SINCE"])){
            $if_modified_since = preg_replace('/;.*$/', '', $_SERVER["HTTP_IF_MODIFIED_SINCE"]);
            $gmdate_mod = gmdate('D, d M Y H:i:s', filemtime($cache_file));
            if(strstr($gmdate_mod, 'GMT')){
                $gmdate_mod .= " GMT";
            }
            if($if_modified_since == $gmdate_mod){
                header("HTTP/1.1 304 Not Modified");
                exit;
            }
        }

        $fileSize = filesize($cache_file);

        // send headers then display image
        header("Content-Type: image/jpeg");
        //header("Accept-Ranges: bytes");
        header("Last-Modified: " . gmdate('D, d M Y H:i:s', filemtime($cache_file)) . " GMT");
        header("Content-Length: " . $fileSize);
        header("Cache-Control: max-age=9999, must-revalidate");
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + 9999) . "GMT");

        readfile($cache_file);
    }
}

function get_cache_file(){
    static $cache_file;
    if(!$cache_file){
        $cachename = get_request('src', 'imgsketch') . get_request('w', 100) . get_request('h', 100) . get_request('q', 100);
        $cache_file = md5($cachename) . '.jpg';
    }
    return $cache_file;
}

function getDocumentRoot(){
    $result = dirname(dirname(dirname(__FILE__)));
//    echo $result;
    $result = str_replace('\\', '/',$result);
    return $result;
}
