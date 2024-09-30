<?php

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WP_Book_Manager {
    /**
     * Text domain for translations
     * @var string
     */
    private $text_domain = 'wp-books-manager-moveup';

    /**
     * Register the necessary hooks to create the post type, taxonomy and shortcode
     * @return void
     */
    public function register_hooks() {
        add_action( 'init', array( $this, 'register_post_type' ) );
        add_action( 'init', array( $this, 'register_taxonomy' ) );
        add_action( 'init', array( $this, 'register_shortcode' ) );
        add_action( 'init', array( $this, 'enqueue_styles' ) );
    }

    /**
     * Enqueue the plugin styles
     * @return void
     */
    public function enqueue_styles(): void {
        wp_enqueue_style( 'wpbm-styles', WPBM_PLUGIN_URL . 'assets/css/wpbm-styles.min.css', array(), WPBM_VERSION );
    }

    /**
     * Register the custom post type 'Book'
     * @return void
     */
    public function register_post_type(): void {
        $labels = [
            'name'               => __( 'Books', $this->text_domain ),
            'singular_name'      => __( 'Book', $this->text_domain ),
            'menu_name'          => __( 'Books', $this->text_domain ),
            'name_admin_bar'     => __( 'Book', $this->text_domain ),
            'add_new'            => __( 'Add New', $this->text_domain ),
            'add_new_item'       => __( 'Add New Book', $this->text_domain ),
            'new_item'           => __( 'New Book', $this->text_domain ),
            'edit_item'          => __( 'Edit Book', $this->text_domain ),
            'view_item'          => __( 'View Book', $this->text_domain ),
            'all_items'          => __( 'All Books', $this->text_domain ),
            'search_items'       => __( 'Search Books', $this->text_domain ),
            'parent_item_colon'  => __( 'Parent Books:', $this->text_domain ),
            'not_found'          => __( 'No books found.', $this->text_domain ),
            'not_found_in_trash' => __( 'No books found in Trash.', $this->text_domain ),
        ];

        $args = [
            'labels'             => $labels,
            'description'        => __( 'Description.', $this->text_domain ),
            'public'             => true,
            'publicly_queryable' => true,
            'menu_icon'          => 'dashicons-book',
            'show_ui'            => true,
            'show_in_menu'       => true,
            'show_in_rest'       => true,
            'rest_base'          => 'books',
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'book' ],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'supports'           => [ 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields' ],
        ];

        register_post_type( 'book', $args );
    }

    /**
     * Register the taxonomy 'Genre' for the 'Book' post type
     * @return void
     */
    public function register_taxonomy(): void {
        $labels = [
            'name'              => __( 'Genres', $this->text_domain ),
            'singular_name'     => __( 'Genre', $this->text_domain ),
            'search_items'      => __( 'Search Genres', $this->text_domain ),
            'all_items'         => __( 'All Genres', $this->text_domain ),
            'parent_item'       => __( 'Parent Genre', $this->text_domain ),
            'parent_item_colon' => __( 'Parent Genre:', $this->text_domain ),
            'edit_item'         => __( 'Edit Genre', $this->text_domain ),
            'update_item'       => __( 'Update Genre', $this->text_domain ),
            'add_new_item'      => __( 'Add New Genre', $this->text_domain ),
            'new_item_name'     => __( 'New Genre Name', $this->text_domain ),
            'menu_name'         => __( 'Genre', $this->text_domain ),
        ];

        $args = [
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'show_in_rest'      => true,
            'query_var'         => true,
            'rewrite'           => [ 'slug' => 'genre' ],
        ];

        register_taxonomy( 'genre', [ 'book' ], $args );
    }

    public function register_shortcode(): void {
        add_shortcode( 'recent_books', array( $this, 'display_recent_books' ) );
    }

    /**
     * Display the most recent books in the frontend when the [recent_books] shortcode is used
     * @param array $atts       Shortcode attributes
     * @return string
     */
    public function display_recent_books( array $atts ): string {
        $show_title = $atts['show_title'] ?? true;
        $title = $atts['title'] ?? 'Recent Books';

        $args = [
            'post_type'      => 'book',
            'post_status'    => 'publish',
            'posts_per_page' => $atts['number'] ?? 5,
        ];

        $query = new WP_Query( $args );

        $template = <<<TEMPLATE
            <div class="recent-books-wpbm">
                {{TITLE}}
                {{BOOKS_LIST}}
            </div>
        TEMPLATE;

        $title_html = $show_title ? "<h2>{$title}</h2>" : '';
        $books_list_html = '';

        if ( $query->have_posts() ) {
            $books_list_html = '<div class="books-list">';

            while ( $query->have_posts() ) {
                $query->the_post();
                $book_title = get_the_title();
                $book_permalink = get_permalink();
                $book_thumbnail = get_the_post_thumbnail_url( get_the_ID(), 'medium' );
                $book_excerpt = get_the_excerpt();

                $books_list_html .= <<<BOOK_ITEM
                    <div class="book-item">
                        <a href="{$book_permalink}">
                            <img src="{$book_thumbnail}" alt="{$book_title}" class="book-thumbnail" />
                            <div class="description">
                                <h3 class="book-title">{$book_title}</h3>
                                <p>{$book_excerpt}</p>
                                <span class="read-more">Read more</span>
                            </div>
                        </a>
                    </div>
                BOOK_ITEM;
            }

            $books_list_html .= '</div>';
        } else {
            $books_list_html = '<div class="no-books-found">No books found.</div>';
        }

        $output = str_replace( '{{TITLE}}', $title_html, $template );
        $output = str_replace( '{{BOOKS_LIST}}', $books_list_html, $output );

        wp_reset_postdata();

        return $output;
    }
}