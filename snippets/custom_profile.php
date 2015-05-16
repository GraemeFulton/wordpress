<?php 
Class Taxonomy_Profile_Fields{
    
    __construct(){
            add_action('bp_init', array($this,'create_profile_field_group'));

    }
    /** src for create_profile_field_group : http://www.amkd.com.au/wordpress/buddypress-adding-custom-profile-field-programatically/118 **/
     public function create_profile_field_group(){
             global $wpdb;
            $group_args = array(
                'name' => 'Search Preferences'
            );
            $sqlStr = "SELECT `id` FROM `wp_bp_xprofile_groups` WHERE `name` = 'Search Preferences'";
            $groups = $wpdb->get_results($sqlStr);
            if(count($groups) > 0)
            {
                // The group exist so we exit the function
                return;
            }
            // The group does not exist so we create is
            $group_id = xprofile_insert_field_group( $group_args );
            
            if(!in_array(xprofile_get_field_id_from_name('Professions'), $group_id)){
                global $bp;
                $xfield_args =  array (
                    field_group_id  => $group_id,
                    name            => 'Professions',
                    can_delete      => false,
                    field_order     => 1,
                    is_required     => false,
                    type            => 'checkbox'
                    );

                    xprofile_insert_field( $xfield_args );
                    // add options
                $this->add_options($group_id);
                }  
                
             
     }
     
     /**
     * add_options
     */
     private function add_options($group_id){
        //find the id of the field we just added (professions)
        $parent_id= xprofile_get_field_id_from_name('Professions');
        //get the taxonomy tags (we're using a profession taxonomy)
         $tags = get_terms('profession', array('hide_empty' => false));
          if ( count( $tags ) ) {
             //set the counter (for field order)			
             $counter = 1;
             global $bp;
             global $wpdb;
            //for each taxonomy term, add it as an optional checkbox within the parent group id
            foreach( $tags as $tag ) {
                
                if ( !$wpdb->query( $wpdb->prepare( "INSERT INTO {$bp->profile->table_name_fields} (group_id, parent_id, type, name, description, is_required, option_order, is_default_option) VALUES (%d, %d, 'option', %s, '', 0, %d, %d)", $group_id, $parent_id, $tag->name, $counter, $is_default ) ) ) {
  								
                    return false;

            }
            	$counter++;

         }
     }
     }
}
?>
