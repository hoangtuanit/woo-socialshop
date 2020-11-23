<?php
/**
 * @package  WooSocialshop
 */

defined( 'ABSPATH' ) || exit;

namespace Inc\Base;

class BaseController{
    public $setting_links = [];
    public $support_link  = 'https://help.socialhead.io';
    public function __construct(){

    }

    public function setLinks(){
        $this->setting_links[] = '<a href="'.esc_url( $this->getDefaultPage() ).'">'.__('Settings', PLUGIN_ID ).'</a>';
        $this->setting_links[] = '<a href="' . esc_url( $this->support_link ) . '">'.esc_html__( 'Support' , PLUGIN_ID ).'</a>';
    }

    public function getDefaultPage(){
        return admin_url('admin.php?page='.DEFAULT_PAGE);
    }
}