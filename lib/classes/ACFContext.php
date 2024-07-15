<?php
// classes/ACFContext.php

class ACFContext {
    /**
     * Get ACF Flexible Content fields and provide them to Timber context
     *
     * @param string $field_name The name of the ACF Flexible Content field
     * @param int $post_id The ID of the post
     * @return array An array of context data for Timber
     */
    public static function get_flexible_content($field_name, $post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        $context = array();

        // Get the flexible content field from the post meta
        $flexible_content = get_post_meta($post_id, $field_name, true);

        if (is_array($flexible_content) && !empty($flexible_content)) {
            foreach ($flexible_content as $index => $layout) {
                if (isset($layout['acf_fc_layout'])) {
                    $layout_name = $layout['acf_fc_layout'];
                    $subfields = self::get_subfields($field_name, $index, $post_id);

                    // Add the layout and subfields to the context
                    $context[] = array(
                        'layout' => $layout_name,
                        'fields' => $subfields
                    );
                } else {
                    error_log("Layout not found at index {$index} for field: {$field_name}");
                }
            }
        } else {
            // Debugging: Log if no rows are found
            error_log("No rows found for field: $field_name or field is not an array");
        }

        return $context;
    }

    /**
     * Get all subfields for the current ACF Flexible Content layout
     *
     * @param string $field_name The name of the ACF Flexible Content field
     * @param int $index The index of the current layout in the flexible content field
     * @param int $post_id The ID of the post
     * @return array An array of subfield data
     */
    private static function get_subfields($field_name, $index, $post_id) {
        $subfields = array();

        // Get all subfields for the current layout
        $subfield_prefix = "{$field_name}_{$index}_";
        $all_meta = get_post_meta($post_id);

        foreach ($all_meta as $key => $value) {
            if (strpos($key, $subfield_prefix) === 0) {
                $subfield_name = str_replace($subfield_prefix, '', $key);
                $subfields[$subfield_name] = maybe_unserialize($value[0]);
            }
        }

        return $subfields;
    }
}



