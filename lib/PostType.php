<?php
session_start();

/**
 * PostType
 * @author Carl Weis
 * @link http://carlweis.com
 */
class PostType
{

    /**
     * The name of the post type.
     * @var string
     */
    public $post_type_name;

    /**
     * A list of user-specific options for the post type.
     * @var array
     */
    public $post_type_args;

    /**
     * Sets default values, registers the passed post type, and
     * listens for when the post is saved.
     *
     * @param string $name The name of the desired post type.
     * @param array @post_type_args Override the options.
     */
    public function __construct($name, $post_type_args = array())
    {
        if (!isset($_SESSION["taxonomy_data"])) {
            $_SESSION['taxonomy_data'] = array();
        }

        $this->post_type_name = strtolower($name);
        $this->post_type_args = (array) $post_type_args;

        // First step, register that new post type
        $this->init(array(&$this, "register_post_type"));
        $this->save_post();
    }

    /**
     * Helper method, that attaches a passed function to the 'init' WP action
     * @param function $cb Passed callback function.
     */
    public function init($cb)
    {
        add_action("init", $cb);
    }

    /**
     * Helper method, that attaches a passed function to the 'admin_init' WP action
     * @param function $cb Passed callback function.
     */
    public function admin_init($cb)
    {
        add_action("admin_init", $cb);

    }

    /**
     * Registers a new post type in the WP db.
     */
    public function register_post_type()
    {
        $n = ucwords($this->post_type_name);
        $labels = array(
            'name'                => _x( $this->pluralize($n) , 'text_domain' ),
            'singular_name'       => _x( $this->singularize($n), 'text_domain' ),
            'menu_name'           => __( $this->pluralize($n), 'text_domain' ),
            'parent_item_colon'   => __( 'Parent ' . $this->singularize($n) . ':', 'text_domain' ),
            'all_items'           => __( 'All ' . $this->pluralize($n), 'text_domain' ),
            'view_item'           => __( 'View ' . $this->singularize($n), 'text_domain' ),
            'add_new_item'        => __( 'Add New '. $this->singularize($n), 'text_domain' ),
            'add_new'             => __( 'Add New ' . $this->singularize($n), 'text_domain' ),
            'edit_item'           => __( 'Edit ' . $this->singularize($n), 'text_domain' ),
            'update_item'         => __( 'Update '. $this->singularize($n), 'text_domain' ),
            'search_items'        => __( 'Search '. $this->pluralize($n), 'text_domain' ),
            'not_found'           => __( $this->singularize($n) . ' Not found', 'text_domain' ),
            'not_found_in_trash'  => __( $this->singularize($n) . ' Not found in Trash', 'text_domain' ),
        );
        $args = array(
            "labels" => $labels,
            'singular_name' => $this->singularize($n),
            "public" => true,
            "publicly_queryable" => true,
            "query_var" => true,
            #"menu_icon" => get_stylesheet_directory_uri() . "/article16.png",
            "rewrite" => true,
            "capability_type" => "post",
            "hierarchical" => false,
            "menu_position" => null,
            "supports" => array("title", "editor", "thumbnail"),
            'has_archive' => true,
        );

        // Take user provided options, and override the defaults.
        $args = array_merge($args, $this->post_type_args);

        register_post_type($this->post_type_name, $args);
    }

    /**
    * Pluralizes English nouns.
    *
    * @access public
    * @static
    * @param    string    $word    English noun to pluralize
    * @return string Plural noun
    */
    function pluralize($word)
    {
        $plural = array(
        '/(quiz)$/i' => '1zes',
        '/^(ox)$/i' => '1en',
        '/([m|l])ouse$/i' => '1ice',
        '/(matr|vert|ind)ix|ex$/i' => '1ices',
        '/(x|ch|ss|sh)$/i' => '1es',
        '/([^aeiouy]|qu)ies$/i' => '1y',
        '/([^aeiouy]|qu)y$/i' => '1ies',
        '/(hive)$/i' => '1s',
        '/(?:([^f])fe|([lr])f)$/i' => '12ves',
        '/sis$/i' => 'ses',
        '/([ti])um$/i' => '1a',
        '/(buffal|tomat)o$/i' => '1oes',
        '/(bu)s$/i' => '1ses',
        '/(alias|status)/i'=> '1es',
        '/(octop|vir)us$/i'=> '1i',
        '/(ax|test)is$/i'=> '1es',
        '/s$/i'=> 's',
        '/$/'=> 's');

        $uncountable = array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep');

        $irregular = array(
        'person' => 'people',
        'man' => 'men',
        'child' => 'children',
        'sex' => 'sexes',
        'move' => 'moves');

        $lowercased_word = strtolower($word);

        foreach ($uncountable as $_uncountable){
            if(substr($lowercased_word,(-1*strlen($_uncountable))) == $_uncountable){
                return $word;
            }
        }

        foreach ($irregular as $_plural=> $_singular){
            if (preg_match('/('.$_plural.')$/i', $word, $arr)) {
                return preg_replace('/('.$_plural.')$/i', substr($arr[0],0,1).substr($_singular,1), $word);
            }
        }

        foreach ($plural as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }
        return false;

    }

    // }}}
    // {{{ singularize()

    /**
    * Singularizes English nouns.
    *
    * @access public
    * @static
    * @param    string    $word    English noun to singularize
    * @return string Singular noun.
    */
    function singularize($word)
    {
        $singular = array (
        '/(quiz)zes$/i' => '\1',
        '/(matr)ices$/i' => '\1ix',
        '/(vert|ind)ices$/i' => '\1ex',
        '/^(ox)en/i' => '\1',
        '/(alias|status)es$/i' => '\1',
        '/([octop|vir])i$/i' => '\1us',
        '/(cris|ax|test)es$/i' => '\1is',
        '/(shoe)s$/i' => '\1',
        '/(o)es$/i' => '\1',
        '/(bus)es$/i' => '\1',
        '/([m|l])ice$/i' => '\1ouse',
        '/(x|ch|ss|sh)es$/i' => '\1',
        '/(m)ovies$/i' => '\1ovie',
        '/(s)eries$/i' => '\1eries',
        '/([^aeiouy]|qu)ies$/i' => '\1y',
        '/([lr])ves$/i' => '\1f',
        '/(tive)s$/i' => '\1',
        '/(hive)s$/i' => '\1',
        '/([^f])ves$/i' => '\1fe',
        '/(^analy)ses$/i' => '\1sis',
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
        '/([ti])a$/i' => '\1um',
        '/(n)ews$/i' => '\1ews',
        '/s$/i' => '',
        );

        $uncountable = array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep');

        $irregular = array(
        'person' => 'people',
        'man' => 'men',
        'child' => 'children',
        'sex' => 'sexes',
        'move' => 'moves');

        $lowercased_word = strtolower($word);
        foreach ($uncountable as $_uncountable){
            if(substr($lowercased_word,(-1*strlen($_uncountable))) == $_uncountable){
                return $word;
            }
        }

        foreach ($irregular as $_plural=> $_singular){
            if (preg_match('/('.$_singular.')$/i', $word, $arr)) {
                return preg_replace('/('.$_singular.')$/i', substr($arr[0],0,1).substr($_plural,1), $word);
            }
        }

        foreach ($singular as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }

        return $word;
    }


    /**
     * Registers a new taxonomy, associated with the instantiated post type.
     *
     * @param string $taxonomy_name The name of the desired taxonomy
     * @param string $plural The plural form of the taxonomy name. (Optional)
     * @param array $options A list of overrides
     */
    public function add_taxonomy($taxonomy_name, $plural = '', $options = array())
    {
        // Create local reference so we can pass it to the init cb.
        $post_type_name = $this->post_type_name;

        // If no plural form of the taxonomy was provided, do a crappy fix. :)

        if (empty($plural)) {
            $plural = $taxonomy_name . 's';
        }

        // Taxonomies need to be lowercase, but displaying them will look better this way...
        $taxonomy_name = ucwords($taxonomy_name);

        // At WordPress' init, register the taxonomy
        $this->init(
            function () use ($taxonomy_name, $plural, $post_type_name, $options) {
                // Override defaults with user provided options

                $options = array_merge(
                    array(
                        "hierarchical" => false,
                        "label" => $taxonomy_name,
                        "singular_label" => $plural,
                        "show_ui" => true,
                        "query_var" => true,
                        "rewrite" => array("slug" => strtolower($taxonomy_name)),
                    ),
                    $options
                );

                // name of taxonomy, associated post type, options
                register_taxonomy(strtolower($taxonomy_name), $post_type_name, $options);
            });
    }

    /**
     * Creates a new custom meta box in the New 'post_type' page.
     *
     * @param string $title
     * @param array $form_fields Associated array that contains the label of the input, and the desired input type. 'Title' => 'text'
     */
    public function add_meta_box($title, $form_fields = array())
    {
        $post_type_name = $this->post_type_name;

        // end update_edit_form
        add_action('post_edit_form_tag', function () {
            echo ' enctype="multipart/form-data"';
        });

        // At WordPress' admin_init action, add any applicable metaboxes.
        $this->admin_init(function () use ($title, $form_fields, $post_type_name) {
            add_meta_box(
                strtolower(str_replace(' ', '_', $title)), // id
                $title, // title
                function ($post, $data) {
                    // function that displays the form fields
                    global $post;

                    wp_nonce_field(plugin_basename(__FILE__), 'jw_nonce');

                    // List of all the specified form fields
                    $inputs = $data['args'][0];

                    // Get the saved field values
                    $meta = get_post_custom($post->ID);

                    // For each form field specified, we need to create the necessary markup
                    // $name = Label, $type = the type of input to create
                    foreach ($inputs as $name => $type) {
                        #'Happiness Info' in 'Snippet Info' box becomes
                        # snippet_info_happiness_level
                        $id_name = $data['id'] . '_' . strtolower(str_replace(' ', '_', $name));

                        if (is_array($inputs[$name])) {
                            // then it must be a select or file upload
                            // $inputs[$name][0] = type of input

                            if (strtolower($inputs[$name][0]) === 'select') {
                                // filter through them, and create options
                                $select = "<select name='$id_name' class='widefat'>";
                                foreach ($inputs[$name][1] as $option) {
                                    // if what's stored in the db is equal to the
                                    // current value in the foreach, that should
                                    // be the selected one

                                    if (isset($meta[$id_name]) && $meta[$id_name][0] == $option) {
                                        $set_selected = "selected='selected'";
                                    } else {
                                        $set_selected = '';
                                    }

                                    $select .= "<option value='$option' $set_selected> $option </option>";
                                }
                                $select .= "</select>";
                                array_push($_SESSION['taxonomy_data'], $id_name);
                            }
                        }

                        // Attempt to set the value of the input, based on what's saved in the db.
                        $value = isset($meta[$id_name][0]) ? $meta[$id_name][0] : '';

                        $checked = ($type == 'checkbox' && !empty($value) ? 'checked' : '');

                        // Sorta sloppy. I need a way to access all these form fields later on.
                        // I had trouble finding an easy way to pass these values around, so I'm
                        // storing it in a session. Fix eventually.
                        array_push($_SESSION['taxonomy_data'], $id_name);

                        // TODO - Add the other input types.
                        $lookup = array(
                            "text" => "<input type='text' name='$id_name' value='$value' class='widefat' />",
                            "textarea" => "<textarea name='$id_name' class='widefat' rows='10'>$value</textarea>",
                            "checkbox" => "<input type='checkbox' name='$id_name' value='$name' $checked />",
                            "select" => isset($select) ? $select : '',
                            "file" => "<input type='file' name='$id_name' id='$id_name' />",
                        );
                        ?>

                            <p>
                                <label><?php echo ucwords($name) . ':';?></label>
                                <?php echo $lookup[is_array($type) ? $type[0] : $type];?>
                            </p>
                            <p>
                                <?php
// If a file was uploaded, display it below the input.
                        $file = get_post_meta($post->ID, $id_name, true);
                        if ($type === 'file') {
                            // display the image
                            $file = get_post_meta($post->ID, $id_name, true);

                            $file_type = wp_check_filetype($file);
                            $image_types = array('jpeg', 'jpg', 'bmp', 'gif', 'png');
                            if (isset($file)) {
                                if (in_array($file_type['ext'], $image_types)) {
                                    echo "<img src='$file' alt='' style='max-width: 400px;' />";
                                } else {
                                    echo "<a href='$file'>$file</a>";
                                }
                            }
                        }
                        ?>
                            </p>

                            <?php

                    }
                },
                $post_type_name, // associated post type
                'normal', // location/context. normal, side, etc.
                'default', // priority level
                array($form_fields) // optional passed arguments.
            ); // end add_meta_box
        });
    }

    /**
     * When a post saved/updated in the database, this methods updates the meta box params in the db as well.
     */
    public function save_post()
    {
        add_action('save_post', function () {
            // Only do the following if we physically submit the form,
            // and now when autosave occurs.
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }

            global $post;

            if ($_POST && !wp_verify_nonce($_POST['jw_nonce'], plugin_basename(__FILE__))) {
                return;
            }

            // Get all the form fields that were saved in the session,
            // and update their values in the db.
            if (isset($_SESSION['taxonomy_data'])) {
                foreach ($_SESSION['taxonomy_data'] as $form_name) {
                    if (!empty($_FILES[$form_name])) {
                        if (!empty($_FILES[$form_name]['tmp_name'])) {
                            $upload = wp_upload_bits($_FILES[$form_name]['name'], null, file_get_contents($_FILES[$form_name]['tmp_name']));

                            if (isset($upload['error']) && $upload['error'] != 0) {
                                wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
                            } else {
                                update_post_meta($post->ID, $form_name, $upload['url']);
                            }
                        }
                    } else {
                        // Make better. Have to do this, because I can't figure
                        // out a better way to deal with checkboxes. If deselected,
                        // they won't be represented here, but I still need to
                        // update the value to false to blank in the table. Hmm...
                        if (!isset($_POST[$form_name])) {
                            $_POST[$form_name] = '';
                        }

                        if (isset($post->ID)) {
                            update_post_meta($post->ID, $form_name, $_POST[$form_name]);
                        }
                    }
                }

                $_SESSION['taxonomy_data'] = array();

            }

        });
    }
}

/*********/
/* USAGE */
/*********/

// $product = new PostType("movie");
// $product->add_taxonomy('Actor');
// $product->add_taxonomy('Director');
// $product->add_meta_box('Movie Info', array(
//     'name' => 'text',
//     'rating' => 'text',
//     'review' => 'textarea',
// 'Profile Image' => 'file'

// ));
