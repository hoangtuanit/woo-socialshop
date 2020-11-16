<?php
/**
 * @package  WooSocialshop
 */

namespace Inc\Base;

class SettingLinks{
    public function register(){
        add_filter( 
            'plugin_action_links_' . PLUGIN_BASENAME, 
            array( $this, 'admin_plugin_settings_link' )
        );
    }

    public  function admin_plugin_settings_link( $links ) {
        $links[] = '<a href="'.esc_url( $this->get_page() ).'">'.__('Settings', $this->app_id).'</a>';
        $links[] = '<a href="' . esc_url( $this->support_link ) . '">'.esc_html__( 'Support' , $this->app_id ).'</a>';
        return $links; 
    }
}