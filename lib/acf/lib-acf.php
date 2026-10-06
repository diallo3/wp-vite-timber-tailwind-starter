<?php

/**
 * ACF local JSON save point
 */
add_filter('acf/settings/save_json', 'my_acf_json_save_point');
function my_acf_json_save_point($path) {
	$path = get_stylesheet_directory() . '/acf-json';
	return $path;
}

add_action('acf/init', function() {
	if (function_exists('acf_add_options_page')) {
		acf_add_options_page();
	}
});


/**
 * ACF Render Choice
 */
add_action('acfe/render_choice/name=section_choices', 'my_acfe_render_choice', 10, 4);
function my_acfe_render_choice($input, $value, $label, $field){
    
    ?>
    <div class="section-choice">
        <div>
            <span class="inline-flex"><?php echo $label; ?></span>
        </div>

        <div class="relative inline-flex items-center gap-3 group">
            <?php echo $input; ?>
            <span class="form-switch"></span>
        </div>


        
    </div>
    <?php

}

// Add a custom render function for the radio field type
add_action('acfe/render_choice/type=radio', 'radio_acfe_render_choice_entries_type', 10, 4);
function radio_acfe_render_choice_entries_type($input, $value, $label, $field){
    ?>
    <div class="radio-entry-choice">
        <strong class="sr-only"><?php echo $value; ?></strong>
        <?php echo $input; ?>
        <span><?php echo $label; ?></span>
    </div>
    <?php
    
}

add_action('acfe/render_choice/type=checkbox', 'checkbox_acfe_render_choice_entries_type', 10, 4);
function checkbox_acfe_render_choice_entries_type($input, $value, $label, $field){
    ?>

    <div class="flex flex-col items-center justify-center px-4 py-2 text-center border border-gray-300 rounded check-entry-choice">
        <div>
            <strong><?php echo $label; ?></strong>
            <div class="sr-only"><?php echo $value; ?></div>
        </div>
        <div class="text-white">
            <?php echo $input; ?>
        </div>
    </div>

    <?php
    
}