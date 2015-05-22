<?php
/**Search all tags and taxonomies**/
/**modified from source: http://www.rfmeier.net/include-category-and-post-tag-names-in-the-wordpress-search/**/
/**useful link:  http://wordpress.stackexchange.com/questions/2623/include-custom-taxonomy-term-in-search **/
Class Tag_Search(){
    
private function __construct(){
    
    add_filter( 'posts_join', array($this,'custom_posts_join') );
    add_filter( 'posts_where', array($this,'custom_posts_where') );
    add_filter( 'posts_groupby', array($this,'custom_posts_groupby') );
    
}    
    
public function custom_posts_where( $where, $query )
{
    global $wpdb;

    if( is_search() )
    {
        
        $where .= " OR (
                        {$wpdb->term_taxonomy}.taxonomy IN( 'category', 'post_tag' ) 
                        AND
                        {$wpdb->terms}.name LIKE '%" . get_search_query(). "%'
                    )";
    }

    return $where;
}

    

public function custom_posts_join( $join, $query )
{
    global $wpdb;

    //* if main query and search...
    if( is_search() )
    {
        //* join term_relationships, term_taxonomy, and terms into the current SQL where clause
        $join .= "
        LEFT JOIN 
        ( 
            {$wpdb->term_relationships}
            INNER JOIN 
                {$wpdb->term_taxonomy} ON {$wpdb->term_taxonomy}.term_taxonomy_id = {$wpdb->term_relationships}.term_taxonomy_id 
            INNER JOIN 
                {$wpdb->terms} ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id 
        ) 
        ON {$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id ";
    }

    return $join;
}

public function custom_posts_groupby( $groupby, $query )
{
    global $wpdb;

    //* if is main query and a search...
    if( is_search() )
    {
        //* assign the GROUPBY
        $groupby = "{$wpdb->posts}.ID";
    }

    return $groupby;
}

}
