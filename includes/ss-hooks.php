<?php

add_action( 'save_post', 'set_post_default_category', 10,3 );
add_action( 'transition_post_status', 'trigger_post_status', 10,3 );
 
