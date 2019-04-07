<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('theme_url')) {
    function theme_url() {
        return base_url().'content/theme/';
    }   
}

if ( ! function_exists('assets_url')) {
    function assets_url() {
        return base_url().'content/theme/assets/';
    }   
}

if ( ! function_exists('theme_images')) {
    function theme_images() {
        return base_url().'content/theme/img/';
    }   
}

if ( ! function_exists('site_images')) {
    function site_images() {
        return base_url().'content/images/';
    }   
}

if ( ! function_exists('script_tag')) {
    function script_tag($paths) {
        $tag='';
         if (is_array($paths)) {
            foreach ($paths as $path) {$tag .= "<script src='".$path."'></script> \n";}
         } else {$tag = "<script src='".$paths."'></script> \n";}       
        return $tag;
    }   
}