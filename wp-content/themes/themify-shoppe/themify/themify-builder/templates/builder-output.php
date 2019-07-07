<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly 
$builder_id = (int) $builder_id;
?>
<div id="themify_builder_content-<?php echo $builder_id; ?>" data-postid="<?php echo $builder_id; ?>" class="themify_builder_content themify_builder_content-<?php echo $builder_id; ?> themify_builder">

    <?php
    foreach ($builder_output as $key => $row) {
        if (!empty($row)) {
            if (!isset($row['row_order'])) {
                $row['row_order'] = $key; // Fix issue with import content has same row_order number
            }
            Themify_Builder_Component_Row::template($key, $row, $builder_id, true);
        }
    } // end row loop
    ?>
</div>
<!-- /themify_builder_content -->