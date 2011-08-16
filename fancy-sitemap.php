<?php

/*
  Plugin Name: Fancy Sitemap
  Plugin URI: http://www.bunchacode.com/programming/fancy-sitemap/
  Description: generates a javascript/html5 sitemap.
  Version: 0.7.1
  Author: Jiong Ye
  Author URI: http://www.bunchacode.com
  License: GPL2
 */
/*  Copyright 2011  Jiong Ye  (email : dexxaye@gmail.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */
?>
<?php

define('FS_DIR', WP_CONTENT_DIR . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'fancy-sitemap' . DIRECTORY_SEPARATOR);
define('FS_VIEWS', FS_DIR . 'views' . DIRECTORY_SEPARATOR);
define('FS_TYPE_KEY', 'fancy_sitemap_type');
define('FS_TYPE_PAGE_EXCLUDE', 'fancy_sitemap_page_exclude');
define('FS_TYPE_MENU_INCLUDE', 'fancy_sitemap_menu_include');
define('FS_OPTIONS', 'fancy_sitemap_options');
define('FS_POSITIONS', 'fancy_sitemap_positions');

function fancy_sitemap_init(){
    if(!is_admin())
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('raphael-min.js', plugins_url('js/raphael-min.js', __FILE__));
        wp_enqueue_script('fancy_sitemap.js', plugins_url('js/fancy_sitemap.js', __FILE__));
        wp_enqueue_style('fancy_sitemap.css', plugins_url('css/fancy_sitemap.css', __FILE__));
    }
}
add_action('init', 'fancy_sitemap_init');

function fancy_sitemap_shortcode() {
    return fancy_sitemap_get_output();
}
add_shortcode('fancy-sitemap', 'fancy_sitemap_shortcode' );

function fancy_sitemap_get_output($preview = false){
    $doSitemap = false;
    $options = get_option(FS_OPTIONS);
    $options = !is_array($options)?unserialize($options):$options;
    $type = get_option(FS_TYPE_KEY);
    $pageExcludes = array();
    $output = '';
    
    if($type=='page'){
        $pageExcludes = get_option(FS_TYPE_PAGE_EXCLUDE);
        $pages = wp_list_pages(array(
            'exclude'=>$pageExcludes,
            'title_li'=>'',
            'echo'=>0
        ));

        if($options['use_home'] != '1')
            $output .= '<ul class="fancySitemap">' . $pages . '</ul><div id="sitemapHolder"></div>';
        else
            $output .= '<ul class="fancySitemap"><li class="page-item page-item-0"><a href="'.get_bloginfo('url').'">' . $options['home_text'] . '</a><ul class="children">' . $pages . '</ul></li></ul><div id="sitemapHolder"></div>';
        
        $doSitemap = true;
    }
    else if($type=='menu'){
        $menus = explode(',', get_option(FS_TYPE_MENU_INCLUDE));

        if($options['use_home'] == '1')
            $output .= '<ul class="fancySitemap"><li class="page-item page-item-0"><a href="'.get_bloginfo('url').'">' . $options['home_text'] . '</a><ul class="children">';
        else
            $output .= '<ul class="fancySitemap">';
        
        foreach($menus as $menu){
            $name = fancy_sitemap_get_menu_name($menu);
            
            $output .= '<li class="menu-'.$menu.'">';
            $output .= '<a href="#">'.$name[0].'</a>';
            $output .= wp_nav_menu(array(
                'menu'=>$menu,
                'echo'=>false,
                'container'=>'',
                'menu_class'=>'children'
            ));
            $output .= '</li>';
        }
        if($options['use_home'] == '1')
            $output .= '</ul></li></ul><div id="sitemapHolder"></div>';
        else
            $output .= '</ul><div id="sitemapHolder"></div>';
        
        $doSitemap = true;
    }
    
    if($doSitemap){
        $output .= '<script type="text/javascript">';
        $output .= "var options = {};\n";
        foreach($options as $name => $value){
            $output .= "options.$name = '$value';\n";
        }

        //get node positions
        $positions = get_option(FS_POSITIONS);
        $positions = array_filter(explode('||', $positions));
        $output .= "var positions = {};\n";

        foreach($positions as $p){
            $v = explode(',', $p);
            if(count($v) == 3)
                $output .= "positions[".$v[0]."] = {x:" . $v[1] . ", y:" . $v[2] . "};\n";
        }

        if($preview){
            $output .= "var preview = true";
        }

        $output .= '</script>';

        $output = str_replace('current_page_item', '', $output);
        $output = str_replace('current_page_ancestor', '', $output);

        return $output;
    }
    return '';
}

function fancy_sitemap_admin_menu() {
    $page = add_options_page('Fancy Sitemap', 'Fancy Sitemap', 'manage_options', 'fancy-sitemap', 'fancy_sitemap_show_options');
    add_action( "admin_print_scripts-$page", 'fancy_sitemap_admin_head' );
}
add_action('admin_menu', 'fancy_sitemap_admin_menu');

function fancy_sitemap_admin_head() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('colorpicker.js', plugins_url('js/colorpicker.js', __FILE__));
    wp_enqueue_script('project.js', plugins_url('js/project.js', __FILE__));
    wp_enqueue_script('raphael-min.js', plugins_url('js/raphael-min.js', __FILE__));
    wp_enqueue_script('fancy_sitemap.js', plugins_url('js/fancy_sitemap.js', __FILE__));
    
    $cssUrl = plugins_url('css', __FILE__);
    echo "<link rel='stylesheet' href='$cssUrl/admin.css' type='text/css' />\n";
    echo "<link rel='stylesheet' href='$cssUrl/colorpicker.css' type='text/css' />\n";
    echo "<link rel='stylesheet' href='$cssUrl/fancy_sitemap.css' type='text/css' />\n";
}

function fancy_sitemap_show_options() {
    $url = menu_page_url('fancy-sitemap', 0);
    
    if(!empty($_POST))
    {
        $type = 'page';
        if($_POST['type']=='menu')
            $type = 'menu';
        
        $o = array();
        if(isset($_POST['options']))
            $o = $_POST['options'];
        
        if(isset($_POST['positions']))
        {
            $positions = trim($_POST['positions']);
            
            if(!empty($positions))
                update_option(FS_POSITIONS, $positions);
        }

        update_option(FS_TYPE_KEY, $type);
        update_option(FS_TYPE_PAGE_EXCLUDE, isset($_POST['page_exclude'])?implode(',', array_filter($_POST['page_exclude'])):array());
        update_option(FS_TYPE_MENU_INCLUDE, isset($_POST['menus'])?implode(',', array_filter($_POST['menus'])):array());
        update_option(FS_OPTIONS, serialize($o));
    }
    global $wpdb;
    
    $type = get_option(FS_TYPE_KEY);
    $pageExcludes = explode(',', get_option(FS_TYPE_PAGE_EXCLUDE));
    $menuIncludes = explode(',', get_option(FS_TYPE_MENU_INCLUDE));
    $options = get_option(FS_OPTIONS);
    $options = !is_array($options)?unserialize($options):$options;

    //get menus
    $menus = $wpdb->get_results("
        SELECT t.* 
        FROM $wpdb->terms t
        JOIN $wpdb->term_taxonomy tt ON (tt.term_id = t.term_id)
        WHERE tt.taxonomy = 'nav_menu'
        ORDER BY t.name ASC");

    include(FS_VIEWS . 'admin_options.php');
}

function fancy_sitemap_get_menu_name($id){
    if($id){
        global $wpdb;
        return $wpdb->get_col("SELECT name FROM $wpdb->terms WHERE term_id = $id");
    }
    return '';
}
function fancy_sitemap_output_menu($menus, $include = array()){
    foreach($menus as $menu){
        echo '<option value="' . $menu->term_id . '" ' . (in_array($menu->term_id, $include)?'selected="selected"':'') . ' >' . $menu->name . '</option>';
    }
}
function fancy_sitemap_output_page_children($id = 0, $depth = 0, $exclude=array()){
    $pages = fancy_sitemap_get_pages($id);
    
    foreach($pages as $page)
    {
        echo '<option value="' . $page->ID . '" ' . (in_array($page->ID, $exclude)?'selected="selected"':'') . ' >' . str_repeat("- - ",$depth) . $page->post_title . '</option>';
        fancy_sitemap_output_page_children($page->ID, $depth+1, $exclude);
    }
    return;
}

function fancy_sitemap_get_pages($id = 0) {
    return get_pages(array(
        'child_of' => $id,
        'parent' => $id
    ));
}
?>