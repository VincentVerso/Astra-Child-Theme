<?php
    //Enqueue styles for the child theme
    add_action( 'wp_enqueue_scripts', 'astra_child_enqueue_styles');
    function astra_child_enqueue_styles() {
        //Enqueue the parent theme styles
        wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
        //Enqueue the child theme styles
        wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', 
            array('parent-style'),
            wp_get_theme( )->get( 'Version' ));
    }

    add_action( 'wp_footer', 'astra_child_custom_footer_message');
    function astra_child_custom_footer_message() {
        echo '<p style="text-align: center; padding: 20px; align-self: center">' . date('Y') . ' My Custom store. All rights reserved.</p>';
    }

    add_filter('excerpt_more', 'astra_child_custom_excerpt_more');
    function astra_child_custom_excerpt_more($more) {
        return '<a class="read-more" href="' . get_permalink(get_the_ID()) . '">' .  __('Continue Reading...', 'astra-child') . '</a>';
    }

    add_filter('loop_shop_per_page', 'astra_child_products_per_page', 20);
    function astra_child_products_per_page($products_per_page) {
        $products_per_page = 8; //Displays 8 products per page
        return $products_per_page;
    }

    
// // Register Testimonial Custom Post Type
// add_action('init', 'astra_child_create_testimonial_cpt');
// function astra_child_create_testimonial_cpt(){
//     register_post_type('testimonial', array(
//         'labels' => array(
//             'name' => __('Testimonials'),
//             'singular_name' => __('Testimonial')
//         ),
//         'public' => true,
//         'has_archive' => true,
//         'rewrite' => array('slug' => 'testimonials'),
//         'show_in_rest' => true, // Enables Gutenberg editor
//         'menu_icon' => 'dashicons-testimonial', // Valid Dashicon
//         'supports' => array('title', 'editor', 'thumbnail')
//     ));
// }

//     // Create Shortcode to display Testimonials
//     add_shortcode( 'display_testimonials', 'astra_child_display_testimonials_shortcode' );
//     function astra_child_display_testimonials_shortcode() {
//         $args = array(
//             'post_type' => 'testimonial',
//             'posts_per_page' => -1,
//         );
//         $query = new WP_Query($args);
//         ob_start(); // Start output buffering
//         if ( $query->have_posts() ) {
//             echo '<div class="testimonials-container">';
//         while ( $query->have_posts() ) {

//             $query->the_post();

//             echo '<div class="single-testimonial">';

//             if ( has_post_thumbnail() ) {
//                 the_post_thumbnail('thumbnail');
//             }
//             echo '<h3>' . get_the_title() . '</h3>';
//             echo '<div class="testimonial-content">' . get_the_content() . '</div>';
//             echo '</div>';
//         }
//         echo '</div>';
//         wp_reset_postdata();
//         } else{
//             echo 'No testimonials found.';
//         }   
//         return ob_get_clean(); // Return the buffered output
//     }

    
        add_action('astra_entry_content_after', 'astra_child_display_related_posts');
    function astra_child_display_related_posts(){
        if(is_single()){
                //Only call the function if the current page is a single post
                get_related_posts(get_the_ID());
        }
            
    }

    function get_related_posts(){
        $categories = get_the_category();
        //If no categories, return to prevent an error
        if(empty($categories)){
            return;
        }

        //Create a new array to store category ids
        $category_ids = array();
        //Iterate over the categories and add their ids to the array
        foreach($categories as $individual_category){
            $category_ids[] = $individual_category->term_id;
        }

        //These are the arguments for the new query
        $related_args = array(
            'category__in' => $category_ids, //Return posts with the same category as the current post
            'post__not_in' => array(get_the_ID()), //Exclude the current post
            'posts_per_page' => 3, //Only show 3 related posts.
            'orderby' => 'rand' //Randomize the order of the posts
        );

        //Create a new query object and store the results in $related_query
        $related_query = new WP_Query($related_args);

        //If the query returns any results
        if($related_query->have_posts()){
            echo '<div class="related-posts">';
            echo '<h3>Related Posts</h3>';
            echo '<div class="related-posts-grid">';

            //Iterate over the results and display them
            while($related_query->have_posts()){
                $related_query->the_post();
                echo '<article class="related-post">';
                //If the post has a thumbnail, display it by calling the the_post_thumbnail() function
                if(has_post_thumbnail()){
                    //Get the link to the post.
                    echo '<a href="' . get_permalink() . '">';
                    the_post_thumbnail('medium');
                    echo '</a>';
                }
                echo '<h4><a href="' . get_permalink() . '">' . get_the_title() . '</a></h4>';
                echo '<p>' . wp_trim_words(get_the_excerpt(), 15, '...') . '</p>'; //Trim the excerpt to 15
                echo '</article>';
            }
            echo '</div>';
            wp_reset_postdata();
        }

    
    }

    // //Create an instance of the class
    // $testimonials = new AstraChildTEstinmonialCPT();

    // class AstraChildTEstinmonialCPT {
    //     //The constructor is called when the class is instantiated
    //     function __construct(){
    //         //Use the add action hook upon instantiation of the class
    //         //This will run the astra_child_create_testimonial_cpt function that will run on 'this'
    //         // 'this' refers to the current instance of the class
    //         add_action('init', array($this, 'register_testimonial_cpt'));
    //         add_shortcode( 'display_testimonials', 'astra_child_display_testimonials_shortcode' );
    //     }

    //     //Made the function private as it should only be called from the class itself
    //     private function register_testimonial_cpt(){
    //         register_post_type('testimonial', array(
    //             'labels' => array(
    //                 'name' => __('Testimonials'),
    //                 'singular_name' => __('Testimonial')
    //     ),
    //     'public' => true,
    //     'has_archive' => true,
    //     'rewrite' => array('slug' => 'testimonials'),
    //     'show_in_rest' => true, // Enables Gutenberg editor
    //     'menu_icon' => 'dashicons-testimonial', // Valid Dashicon
    //     'supports' => array('title', 'editor', 'thumbnail')
    //         ));
    //     }
        
    //     //Made the function private as it should only be called from the class itself
    //     private function astra_child_display_testimonials_shortcode() {
    //         $args = array(
    //             'post_type' => 'testimonial',
    //             'posts_per_page' => -1,
    //         );
    //         $query = new WP_Query($args);
    //         ob_start(); // Start output buffering
    //         if ( $query->have_posts() ) {
    //             echo '<div class="testimonials-container">';
    //         while ( $query->have_posts() ) {

    //             $query->the_post();

    //             echo '<div class="single-testimonial">';

    //             if ( has_post_thumbnail() ) {
    //                 the_post_thumbnail('thumbnail');
    //             }
    //             echo '<h3>' . get_the_title() . '</h3>';
    //             echo '<div class="testimonial-content">' . get_the_content() . '</div>';
    //             echo '</div>';
    //         }
    //         echo '</div>';
    //         wp_reset_postdata();
    //         } else{
    //             echo 'No testimonials found.';
    //         }   
    //         return ob_get_clean(); // Return the buffered output
    //     }
    // }
?>