function add_featured_video_meta_box()
{
    add_meta_box(
        'featured-video-meta-box',
        'Featured Video',
        'render_eazi_featured_video_meta_box',
        'post',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'add_featured_video_meta_box');

function render_eazi_featured_video_meta_box($post)
{
    $video_url = get_post_meta($post->ID, 'featured_video_url', true);
    $video_poster = get_post_meta($post->ID, 'featured_video_poster', true);
?>
    <input type="hidden" id="featured_video_url" name="featured_video_url" value="<?php echo esc_url($video_url); ?>">
    <input type="hidden" id="featured_video_poster" name="featured_video_poster" value="<?php echo esc_url($video_poster); ?>">
    <label for="featured_video_attachment">Selected Video:</label>
    <div id="featured_video_preview">
        <?php if ($video_url) : ?>
            <video controls width="320" height="240" poster="<?php echo esc_url($video_poster); ?>" autoplay>
                <source src="<?php echo esc_url($video_url); ?>" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <button id="featured_video_remove_button" class="button">Remove Video</button>
        <?php endif; ?>
    </div>
    <button id="featured_video_upload_button" class="button" <?php if ($video_url) echo 'style="display:none;"'; ?>>Select Video</button>
    <p>Click "Select Video" to choose a video from the media library.</p>
    <script>
        jQuery(document).ready(function($) {
            var featured_video_frame;
            var $featuredVideoPreview = $('#featured_video_preview');
            var $featuredVideoUrl = $('#featured_video_url');
            var $featuredVideoPoster = $('#featured_video_poster');
            var $featuredVideoUploadButton = $('#featured_video_upload_button');

            $('#featured_video_upload_button').click(function(e) {
                e.preventDefault();

                if (featured_video_frame) {
                    featured_video_frame.open();
                    return;
                }

                featured_video_frame = wp.media({
                    title: 'Select a Video',
                    button: {
                        text: 'Use this Video'
                    },
                    multiple: false,
                    library: {
                        type: 'video'
                    }
                });

                featured_video_frame.on('select', function() {
                    var attachment = featured_video_frame.state().get('selection').first().toJSON();
                    $featuredVideoUrl.val(attachment.url);
                    $featuredVideoPoster.val(attachment.image);
                    $featuredVideoPreview.html('<video controls width="320" height="240" poster="' + attachment.image + '" autoplay><source src="' + attachment.url + '" type="video/mp4">Your browser does not support the video tag.</video><button id="featured_video_remove_button" class="button">Remove Video</button>');
                    $featuredVideoUploadButton.hide();
                });

                featured_video_frame.open();
            });

            $featuredVideoPreview.on('click', '#featured_video_remove_button', function() {
                $featuredVideoUrl.val('');
                $featuredVideoPoster.val('');
                $featuredVideoPreview.html('');
                $featuredVideoUploadButton.show();
            });
        });
    </script>
<?php
}

function save_featured_video_meta_box($post_id)
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (isset($_POST['featured_video_url'])) {
        update_post_meta($post_id, 'featured_video_url', esc_url($_POST['featured_video_url']));
    }
    if (isset($_POST['featured_video_poster'])) {
        update_post_meta($post_id, 'featured_video_poster', esc_url($_POST['featured_video_poster']));
    }
}
add_action('save_post', 'save_featured_video_meta_box');
