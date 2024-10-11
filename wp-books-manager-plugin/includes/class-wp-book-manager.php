<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class WP_Book_Manager
{
    /**
     * Text domain for translations
     * @var string
     */
    private $text_domain = 'wp-books-manager-moveup';

    /**
     * Register the necessary hooks to create the post type, taxonomy and shortcode
     * @return void
     */
    public function register_hooks()
    {
        add_action('init', array($this, 'register_post_type'));
        add_action('init', array($this, 'register_taxonomy'));
        add_action('init', array($this, 'register_shortcode'));
        add_action('init', array($this, 'enqueue_styles'));
        add_action('init', array($this, 'register_rest_fields'));
        add_action('add_meta_boxes', array($this, 'create_meta_boxes'));
        add_action('save_post_book', array($this, 'save_book_meta'), 10, 3);
    }

    /**
     * Enqueue the plugin styles
     * @return void
     */
    public function enqueue_styles(): void
    {
        wp_enqueue_style('wpbm-styles', WPBM_PLUGIN_URL . 'assets/css/wpbm-styles.min.css', array(), WPBM_VERSION);
    }

    public function register_rest_fields()
    {
        $default_args = array(
            'show_in_rest' => array(
                'single' => true,
                'schema' => array(
                    'type' => 'string',
                    'default' => '',
                ),
            ),
            'type' => 'string',
            'single' => true,
            'sanitize_callback' => 'sanitize_text_field',
        );
        register_post_meta('book', '_book_author', $default_args);
        register_post_meta('book', '_book_isbn', $default_args);
        register_post_meta('book', '_book_publication_date', $default_args);
    }

    /**
     * Create the meta boxes for the 'Book' post type
     * @return void
     */
    public function create_meta_boxes(): void
    {
        add_meta_box(
            'book_author',
            __('Author *', $this->text_domain),
            function ($post) {
                $author = get_post_meta($post->ID, '_book_author', true);
                echo '<input type="text" name="_book_author" class="components-text-control__input is-next-40px-default-size" placeholder="Ex: Agatha Christie" value="' . esc_attr($author) . '" required />';
            },
            'book',
            'normal',
            'high'
        );

        add_meta_box(
            'book_isbn',
            __('ISBN *', $this->text_domain),
            function ($post) {
                $isbn = get_post_meta($post->ID, '_book_isbn', true);
                echo '<input type="text" name="_book_isbn" class="components-text-control__input is-next-40px-default-size" placeholder="Ex: 978-3-16-148410-0" value="' . esc_attr($isbn) . '" required />';
            },
            'book',
            'normal',
            'high'
        );

        add_meta_box(
            'book_publication_date',
            __('Publication Date *', $this->text_domain),
            function ($post) {
                $publication_date = get_post_meta($post->ID, '_book_publication_date', true);
                echo '<input type="date" name="_book_publication_date" class="components-text-control__input is-next-40px-default-size" value="' . esc_attr($publication_date) . '" required />';
            },
            'book',
            'normal',
            'high'
        );
    }

    /**
     * Save the book meta data when the post is saved
     * @param mixed $post_id        The post ID
     * @return mixed
     */
    public function save_book_meta($post_id, $post, $updating): mixed
    {
        if (!current_user_can('edit_post', $post->ID)) {
            return new WP_Error('rest_forbidden', __('You are not allowed to edit this post.', $this->text_domain), ['status' => rest_authorization_required_code()]);
        }

        if (isset($_POST['_book_author']) && !empty($_POST['_book_author'])) {
            update_post_meta($post->ID, '_book_author', sanitize_text_field($_POST['_book_author']));
        }

        if (isset($_POST['_book_isbn']) && !empty($_POST['_book_isbn'])) {
            update_post_meta($post->ID, '_book_isbn', sanitize_text_field($_POST['_book_isbn']));
        }

        if (isset($_POST['_book_publication_date']) && !empty($_POST['_book_publication_date'])) {
            update_post_meta($post->ID, '_book_publication_date', sanitize_text_field($_POST['_book_publication_date']));
        }

        return null;
    }

    /**
     * Register the custom post type 'Book'
     * @return void
     */
    public function register_post_type(): void
    {
        $labels = [
            'name' => __('Books', $this->text_domain),
            'singular_name' => __('Book', $this->text_domain),
            'menu_name' => __('Books', $this->text_domain),
            'name_admin_bar' => __('Book', $this->text_domain),
            'add_new' => __('Add New', $this->text_domain),
            'add_new_item' => __('Add New Book', $this->text_domain),
            'new_item' => __('New Book', $this->text_domain),
            'edit_item' => __('Edit Book', $this->text_domain),
            'view_item' => __('View Book', $this->text_domain),
            'all_items' => __('All Books', $this->text_domain),
            'search_items' => __('Search Books', $this->text_domain),
            'parent_item_colon' => __('Parent Books:', $this->text_domain),
            'not_found' => __('No books found.', $this->text_domain),
            'not_found_in_trash' => __('No books found in Trash.', $this->text_domain),
        ];

        $args = [
            'labels' => $labels,
            'description' => __('Description.', $this->text_domain),
            'public' => true,
            'publicly_queryable' => true,
            'menu_icon' => 'dashicons-book',
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'rest_base' => 'books',
            'query_var' => true,
            'rewrite' => ['slug' => 'book'],
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 5,
            'supports' => ['title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields'],
        ];

        register_post_type('book', $args);
    }

    /**
     * Register the taxonomy 'Genre' for the 'Book' post type
     * @return void
     */
    public function register_taxonomy(): void
    {
        $labels = [
            'name' => __('Genres', $this->text_domain),
            'singular_name' => __('Genre', $this->text_domain),
            'search_items' => __('Search Genres', $this->text_domain),
            'all_items' => __('All Genres', $this->text_domain),
            'parent_item' => __('Parent Genre', $this->text_domain),
            'parent_item_colon' => __('Parent Genre:', $this->text_domain),
            'edit_item' => __('Edit Genre', $this->text_domain),
            'update_item' => __('Update Genre', $this->text_domain),
            'add_new_item' => __('Add New Genre', $this->text_domain),
            'new_item_name' => __('New Genre Name', $this->text_domain),
            'menu_name' => __('Genre', $this->text_domain),
        ];

        $args = [
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'genre'],
        ];

        register_taxonomy('genre', ['book'], $args);
    }

    public function register_shortcode(): void
    {
        add_shortcode('recent_books', array($this, 'display_recent_books'));
    }

    /**
     * Display the most recent books in the frontend when the [recent_books] shortcode is used
     * @param array $atts       Shortcode attributes
     * @return string
     */
    public function display_recent_books(array $atts): string
    {
        $show_title = $atts['show_title'] ?? true;
        $title = $atts['title'] ?? 'Recent Books';

        $args = [
            'post_type' => 'book',
            'post_status' => 'publish',
            'posts_per_page' => $atts['number'] ?? 5,
        ];

        $query = new WP_Query($args);

        $template = <<<TEMPLATE
            <div class="recent-books-wpbm">
                {{TITLE}}
                {{BOOKS_LIST}}
            </div>
        TEMPLATE;

        $title_html = $show_title ? "<h2>{$title}</h2>" : '';
        $books_list_html = '';

        if ($query->have_posts()) {
            $books_list_html = '<div class="books-list">';

            while ($query->have_posts()) {
                $query->the_post();
                $book_title = get_the_title();
                $book_permalink = get_permalink();
                $book_thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'medium');
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

        $output = str_replace('{{TITLE}}', $title_html, $template);
        $output = str_replace('{{BOOKS_LIST}}', $books_list_html, $output);

        wp_reset_postdata();

        return $output;
    }
}