<?php

if (!function_exists('json_encode')) {
    function json_encode($content, $assoc=false) {
        require_once ( dirname(__FILE__) . '/json.php' );
        if ($assoc) {
            $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        }
        else {
            $json = new Services_JSON;
        }
        return $json->encode($content);
    }
}

if (!function_exists('fix_hex_color')) {
    function fix_hex_color($color) {
      if (strpos($color, '#', 0) === 0) {
        return $color;
      }
      return '#'.$color;
    }
}

if (!class_exists('FoobarLiteJSGenerator')) {

  class FoobarLiteJSGenerator {
    function generate($options, $base_url) {

      $height = FoobarLiteJSGenerator::safe_get("height", $options, "30");
      $message = FoobarLiteJSGenerator::safe_get("message", $options, "Hello and welcome to my website!");
      $collapsedButtonHeight = FoobarLiteJSGenerator::safe_get("collapsedButtonHeight", $options, "30");
      $backgroundColor = FoobarLiteJSGenerator::safe_get("backgroundColor", $options, "#369");
      $border = FoobarLiteJSGenerator::safe_get("border", $options, "solid 3px #fff");
      $display = FoobarLiteJSGenerator::safe_get("display", $options, "expanded");
      $speed = FoobarLiteJSGenerator::safe_get("speed", $options, "100");
      
      $fontColor = FoobarLiteJSGenerator::safe_get("fontColor", $options, "#fff");
      $aFontColor = FoobarLiteJSGenerator::safe_get("aFontColor", $options, "#ff8");

      return '
jQuery(function(){
  jQuery.foobar({'.
    FoobarLiteJSGenerator::output_if_nd($height, "30", '
    "height" : ' . $height . ',').
    FoobarLiteJSGenerator::output_if_nd($collapsedButtonHeight, "30", '
    "collapsedButtonHeight" : ' . $collapsedButtonHeight . ',').'
    "backgroundColor" : "'.fix_hex_color($backgroundColor).'",
    "fontColor" : "'.fix_hex_color($fontColor).'",
    "aFontColor" : "'.fix_hex_color($aFontColor).'",
    "border" : "'.$border.'",'.
    FoobarLiteJSGenerator::output_if_nd($display, "expanded", '
    "display" : "' . $display . '",').
    FoobarLiteJSGenerator::output_if_nd($speed, "100", '
    "speed" : ' . $speed . ',').'
    "message" : "' . addslashes($message) . '"
  });
});';

    }

    function safe_get($key, $options, $default) {
      if (!is_array($options)) return $default; 
      return ( array_key_exists($key, $options) ) ? $options[$key] : $default;
    }

    //output the text if the value is not the default
    function output_if_nd($var, $default, $text) {
      if (!isset($var)) {
        return '';
      } else {
        if ($var == $default) {
          return '';
        } else {
          return $text;
        }
      }
    }
  }
}
?>