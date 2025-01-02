<?php
/*
Plugin Name: Falling Shapes
Description: Makes shapes (images from media) fall randomly from top to bottom during festive seasons. Includes a dashboard to upload/select images.
Version: 1.1
Author: Sahan Ranasingha
Author URI: https://sahanranasingha.me
Plugin URI: https://sahanranasingha.me/portfolio/falling-shapes-plugin/
*/

// Enqueue scripts and styles
function falling_shapes_enqueue_scripts() {
    if (is_admin()) {
        wp_enqueue_media(); // Enqueue WordPress Media Uploader
    } else {
        wp_enqueue_script('falling-shapes-js', plugin_dir_url(__FILE__) . 'falling-shapes.js', array('jquery'), '1.1', true);
        wp_enqueue_style('falling-shapes-css', plugin_dir_url(__FILE__) . 'falling-shapes.css');
        wp_localize_script('falling-shapes-js', 'fallingShapesData', [
            'images' => get_option('falling_shapes_images', [])
        ]);
    }
}
add_action('admin_enqueue_scripts', 'falling_shapes_enqueue_scripts');
add_action('wp_enqueue_scripts', 'falling_shapes_enqueue_scripts');

// Create admin menu
function falling_shapes_admin_menu() {
    add_menu_page(
        'Falling Shapes Settings',
        'Falling Shapes',
        'manage_options',
        'falling-shapes-settings',
        'falling_shapes_settings_page',
        'dashicons-art',
        100
    );
}
add_action('admin_menu', 'falling_shapes_admin_menu');

// Admin page content
function falling_shapes_settings_page() {
    ?>
    <div class="wrap">
        <h1>Falling Shapes Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('falling_shapes_options_group');
            do_settings_sections('falling-shapes-settings');
            submit_button();
            ?>
        </form>
        <h2>Support & Documentation</h2>
        <p>For more information, visit the <a href="https://sahanranasingha.me/portfolio/falling-shapes-plugin/" target="_blank">Documentation</a>.</p>
        <p>If you'd like to support me, consider donating through <a href="https://buymeacoffee.com/rsahan" target="_blank">Buy Me a Coffee</a>.</p>
    </div>
    <?php
}

// Register settings
function falling_shapes_register_settings() {
    register_setting('falling_shapes_options_group', 'falling_shapes_images');
    add_settings_section(
        'falling_shapes_main_section',
        'Falling Shapes Configuration',
        null,
        'falling-shapes-settings'
    );
    add_settings_field(
        'falling_shapes_images_field',
        'Select Images',
        'falling_shapes_images_field_callback',
        'falling-shapes-settings',
        'falling_shapes_main_section'
    );
}
add_action('admin_init', 'falling_shapes_register_settings');

// Callback to render media uploader
function falling_shapes_images_field_callback() {
    $images = get_option('falling_shapes_images', []);
    ?>
    <div id="falling-shapes-images">
        <?php if (!empty($images)): ?>
            <?php foreach ($images as $image_url): ?>
                <div class="falling-shape-image">
                    <img src="<?php echo esc_url($image_url); ?>" style="max-width: 100px;">
                    <button type="button" class="button remove-image">Remove</button>
                    <input type="hidden" name="falling_shapes_images[]" value="<?php echo esc_url($image_url); ?>">
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <button type="button" class="button" id="add-image">Add Image</button>
    <script>
        jQuery(document).ready(function ($) {
            let mediaUploader;

            $('#add-image').on('click', function (e) {
                e.preventDefault();

                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }

                mediaUploader = wp.media({
                    title: 'Select Image',
                    button: { text: 'Add Image' },
                    multiple: false
                });

                mediaUploader.on('select', function () {
                    const attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#falling-shapes-images').append(`
                        <div class="falling-shape-image">
                            <img src="${attachment.url}" style="max-width: 100px;">
                            <button type="button" class="button remove-image">Remove</button>
                            <input type="hidden" name="falling_shapes_images[]" value="${attachment.url}">
                        </div>
                    `);
                });

                mediaUploader.open();
            });

            $(document).on('click', '.remove-image', function () {
                $(this).parent().remove();
            });
        });
    </script>
    <?php
}
?>
