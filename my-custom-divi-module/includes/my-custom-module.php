<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class My_Custom_Module extends ET_Builder_Module
{
    public $slug       = 'my_custom_module';
    public $vb_support = 'on';

    function init()
    {
        $this->name = esc_html__('et_builder - WooCommerce Product', 'et_builder');
    }

    function get_fields()
    {
        $fields = array(
            'et_builder_layout_type' => array(
                'label'           => esc_html__('Select Layout', 'et_builder'),
                'type'            => 'select',
                'options'         => array(
                    'grid' => esc_html__('Grid', 'et_builder'),
                    'list' => esc_html__('List', 'et_builder'),
                    'slider' => esc_html__('Slider', 'et_builder'),
                ),
                'default'         => 'grid',
                'description'    => esc_html__('Select Layout Product View.', 'et_builder'),
            ),
            'et_builder_product_filter' => array(
                'label'           => esc_html__('Select Products Filter', 'et_builder'),
                'type'            => 'select',
                'options'         => array(
                    'recent-products' => esc_html__('Recent Products', 'et_builder'),
                    'featured-products' => esc_html__('Featured Products', 'et_builder'),
                    'best-selling-products' => esc_html__('Best Selling Products', 'et_builder'),
                    'related-products' => esc_html__('Related Products', 'et_builder'),
                    'sale-products' => esc_html__('Sale Products', 'et_builder'),
                    'top-products' => esc_html__('Top Rated Products', 'et_builder'),
                ),
                'default'         => 'recent-products',
                'description'    => esc_html__('Select Products Filter.', 'et_builder'),
            ),
            'et_builder_order' => array(
                'label'           => esc_html__('Select Layout', 'et_builder'),
                'type'            => 'select',
                'options'         => array(
                    'asc' => esc_html__('Ascending', 'et_builder'),
                    'desc' => esc_html__('Descending', 'et_builder'),
                ),
                'default'         => 'asc',
                'description'    => esc_html__('Select Layout Product View.', 'et_builder'),
            ),
            'et_builder_orderby' => array(
                'label'           => esc_html__('Select Layout', 'et_builder'),
                'type'            => 'select',
                'options'         => array(
                    'ID' => esc_html__('Product ID', 'et_builder'),
                    'title' => esc_html__('Product Title', 'et_builder'),
                    '_price' => esc_html__('Price', 'et_builder'),
                    '_sku' => esc_html__('SKU', 'et_builder'),
                    'date' => esc_html__('Date', 'et_builder'),
                    'modified' => esc_html__('Last Modified Date', 'et_builder'),
                    'parent' => esc_html__('Parent Id', 'et_builder'),
                    'rand' => esc_html__('Random', 'et_builder'),
                    'menu_order' => esc_html__('Menu Order', 'et_builder'),
                ),
                'default'         => 'date',
                'description'    => esc_html__('Select Layout Product View.', 'et_builder'),
                'show_if'        => array(
                    'et_builder_product_filter!' => ['best-selling-products', 'top-products'],
                ),
            ),
            'et_builder_show_product_sale_badge' => array(
                'label'           => esc_html__('Show Badge ?', 'et_builder'),
                'type'            => 'yes_no_button',
                'default'         => 'yes',
                'options'         => array(
                    'no' => esc_html__('No', 'et_builder'),
                    'yes'  => esc_html__('Yes', 'et_builder'),
                ),
                'description'    => esc_html__('Enable or disable the Show Badge.', 'et_builder'),
            ),
            'show_wishlist' => array(
                'label' => esc_html__('Show Wishlist', 'et_builder'),
                'type'            => 'yes_no_button',
                'default'         => 'yes',
                'options'         => array(
                    'no' => esc_html__('No', 'et_builder'),
                    'yes'  => esc_html__('Yes', 'et_builder'),
                ),
                'description'    => esc_html__('Enable or disable the Show Badge.', 'et_builder'),
            ),
            'show_category' => array(
                'label' => esc_html__('Show Category', 'et_builder'),
                'type'            => 'yes_no_button',
                'default'         => 'yes',
                'options'         => array(
                    'no' => esc_html__('No', 'et_builder'),
                    'yes'  => esc_html__('Yes', 'et_builder'),
                ),
                'description'    => esc_html__('Enable or disable the Show Badge.', 'et_builder'),
            ),
            'show_rating' => array(
                'label' => esc_html__('Show Rating', 'et_builder'),
                'type'            => 'yes_no_button',
                'default'         => 'yes',
                'options'         => array(
                    'no' => esc_html__('No', 'et_builder'),
                    'yes'  => esc_html__('Yes', 'et_builder'),
                ),
                'description'    => esc_html__('Enable or disable the Show Badge.', 'et_builder'),
            ),
            'no_of_products' => array(
                'label'           => esc_html__('Products Count', 'et_builder'),
                'type'            => 'number',
                'default'         => 4,
                'min'             => 1,
                'max'             => 1000,
                'step'            => 1,
                'description'    => esc_html__('Set the number of products to display.', 'et_builder'),
                'show_if' => array(
                    'et_builder_layout_type' => 'grid',
                ),
            ),
        );

        if (isset($this->props['et_builder_layout_type']) && $this->props['et_builder_layout_type'] === 'grid') {
            $fields['no_of_products']['show_if'] = array(
                'et_builder_layout_type' => 'grid',
            );
        } else {
            $fields['no_of_products']['show_if'] = array(); // Hide if not grid
        }

        return $fields;
    }


    function render($attrs, $content = null, $render_slug)
    {
        if (!function_exists('WC')) {
            return;
        }

        $et_builderstype = $this->props['et_builder_layout_type'];


        // Catfilter css
        $catfilter = array(
            'post_type'             => 'product',
            'post_status'           => 'publish',
            'posts_per_page' => $this->props['no_of_products'] ?: 4,
            'order' => (isset($this->props['et_builder_order']) ? $this->props['et_builder_order'] : 'desc'),

        );
        if ($this->props['et_builder_orderby'] == '_price') {
            $catfilter['orderby'] = 'meta_value_num';
            $catfilter['meta_key'] = '_price';
        } else if ($this->props['et_builder_orderby'] == '_sku') {
            $catfilter['orderby'] = 'meta_value meta_value_num';
            $catfilter['meta_key'] = '_sku';
        } else {
            $catfilter['orderby'] = (isset($this->props['et_builder_orderby']) ? $this->props['et_builder_orderby'] : 'date');
        }

        $catfilter['meta_query'] = ['relation' => 'AND'];

        if (function_exists('whols_lite')) {
            $catfilter['meta_query'] = array_filter(apply_filters('woocommerce_product_query_meta_query', $catfilter['meta_query'], new \WC_Query()));
        }

        if (get_option('woocommerce_hide_out_of_stock_items') == 'yes') {
            $catfilter['meta_query'][] = [
                'key' => '_stock_status',
                'value' => 'instock'
            ];
        }


        /*grid and slider */
        $args = array(
            'post_type'             => 'product',
            'post_status'           => 'publish',
            'posts_per_page' => $this->props['no_of_products'] ?: 4,
            'order' => (isset($this->props['et_builder_order']) ? $this->props['et_builder_order'] : 'desc'),

        );
        // price & sku filter
        if ($this->props['et_builder_orderby'] == '_price') {
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_price';
        } else if ($this->props['et_builder_orderby'] == '_sku') {
            $args['orderby'] = 'meta_value meta_value_num';
            $args['meta_key'] = '_sku';
        } else {
            $args['orderby'] = (isset($this->props['et_builder_orderby']) ? $this->props['et_builder_orderby'] : 'date');
        }

        if (!empty($this->props['et_builder_product_grid_categories'])) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => $this->props['et_builder_product_grid_categories'],
                    'operator' => 'IN',
                ],
            ];
        }

        $args['meta_query'] = ['relation' => 'AND'];

        if (function_exists('whols_lite')) {
            $args['meta_query'] = array_filter(apply_filters('woocommerce_product_query_meta_query', $args['meta_query'], new \WC_Query()));
        }

        if (get_option('woocommerce_hide_out_of_stock_items') == 'yes') {
            $args['meta_query'][] = [
                'key' => '_stock_status',
                'value' => 'instock'
            ];
        }

        if ($this->props['et_builder_product_filter'] == 'featured-products') {
            $args['tax_query'] = [
                'relation' => 'AND',
                [
                    'taxonomy' => 'product_visibility',
                    'field' => 'name',
                    'terms' => 'featured',
                ],
                [
                    'taxonomy' => 'product_visibility',
                    'field' => 'name',
                    'terms' => ['exclude-from-search', 'exclude-from-catalog'],
                    'operator' => 'NOT IN',
                ],
            ];

            if ($this->props['et_builder_product_grid_categories']) {
                $args['tax_query'][] = [
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => $this->props['et_builder_product_grid_categories'],
                ];
            }
        } else if ($this->props['et_builder_product_filter'] == 'best-selling-products') {
            $args['meta_key'] = 'total_sales';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
        } else if ($this->props['et_builder_product_filter'] == 'sale-products') {
            $args['post__in']  = array_merge(array(0), wc_get_product_ids_on_sale());
        } else if ($this->props['et_builder_product_filter'] == 'top-products') {
            $args['meta_key'] = '_wc_average_rating';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
        } else if ($this->props['et_builder_product_filter'] == 'related-products') {
            $current_product_id = get_the_ID();
            $product_categories = wp_get_post_terms($current_product_id, 'product_cat', array('fields' => 'ids'));
            $product_tags       = wp_get_post_terms($current_product_id, 'product_tag', array('fields' => 'names'));
            $args['tax_query'] = array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'term_id',
                    'terms'    => $product_categories,
                    'operator' => 'IN',
                ),
                array(
                    'taxonomy' => 'product_tag',
                    'field'    => 'name',
                    'terms'    => $product_tags,
                    'operator' => 'IN',
                ),
            );
        }

        $sliders = new \WP_Query($args);
        $sliderpost_ids = array();
        while ($sliders->have_posts()) : $sliders->the_post();
            $sliderpost_ids[] = get_the_ID();
        endwhile;
        wp_reset_postdata();
        wp_reset_query();
        global $product;

        ob_start();

        if ($et_builderstype === 'grid') { ?>
            <div class="et_builder-block-content et_builder-block">
                <div class="et_builder-block-grid et_builder-block-grid-desing">
                    <?php foreach ($sliderpost_ids as $prditemitem) {
                        $product = wc_get_product($prditemitem);
                    ?>
                        <div class="et_builder-block-box boxstyle">
                            <div class="et_builder-card-product">
                                <div class="et_builder-card-product-customlable">
                                    <?php
                                    if ($this->props['et_builder_show_product_sale_badge'] == 'yes') {

                                        $days_to_show = 10; // Show the new badge for 10 days
                                        $product_published_date = strtotime($product->get_date_created());
                                        if ((time() - (60 * 60 * 24 * $days_to_show)) < $product_published_date) {
                                            echo '<span class="onsale">' . 'NEW!' . '</span>';
                                        } else {
                                            if (!$product->managing_stock() && !$product->is_in_stock()) {
                                                printf('<span class="outofstock-badge">%s</span>', __('Stock <br/> Out', 'et_builder'));
                                            } elseif ($product->is_on_sale()) {
                                                printf('<span class="onsale">%s</span>', __('Sale!', 'et_builder'));
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="et_builder-block-box-img">
                                    <a href="<?php echo  $product->get_permalink(); ?>" title="<?php echo  $product->get_title();  ?>">
                                        <?php echo wp_kses_post($product->get_image('woocommerce_thumbnail')); ?>
                                    </a>
                                </div>
                                <div>
                                    <div class="et_builder-card-product-cart-icon-box">
                                        <?php
                                        if ($this->props['show_wishlist'] == 'yes') {
                                            echo '<a href="" class="et_builder-card-product-cart-icon-text">Add to wishlist</a>';
                                        }

                                        ?>
                                        <div class="et_builder-card-product-cart-icon">
                                            <?php
                                            if ($this->props['show_wishlist'] == 'yes') {
                                                echo '<a href="" class="et_builder-card-product-cart-icon-text"><svg width="19" height="16" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M17.1867 1.57174C16.2989 0.558179 15.0673 0 13.7188 0C11.8221 0 10.6214 1.1397 9.94807 2.09582C9.77337 2.34393 9.62477 2.5927 9.5 2.8268C9.37523 2.5927 9.22666 2.34393 9.05193 2.09582C8.37862 1.1397 7.17786 0 5.28125 0C3.93273 0 2.7011 0.558215 1.81326 1.57178C0.966418 2.53865 0.5 3.83357 0.5 5.21798C0.5 6.72496 1.08475 8.1266 2.34025 9.62894C3.46234 10.9717 5.07661 12.3557 6.9459 13.9584C7.64245 14.5557 8.36277 15.1733 9.12963 15.8484L9.15266 15.8687C9.25208 15.9562 9.37604 16 9.5 16C9.62396 16 9.74792 15.9562 9.84734 15.8687L9.87037 15.8484C10.6372 15.1733 11.3576 14.5557 12.0542 13.9584C13.9234 12.3558 15.5377 10.9717 16.6597 9.62894C17.9152 8.12656 18.5 6.72496 18.5 5.21798C18.5 3.83357 18.0336 2.53865 17.1867 1.57174ZM11.3701 13.1507C10.7696 13.6656 10.1516 14.1954 9.5 14.7654C8.84841 14.1955 8.2304 13.6656 7.62983 13.1507C3.9715 10.0141 1.55469 7.94192 1.55469 5.21798C1.55469 4.09207 1.92752 3.04632 2.60452 2.27339C3.2893 1.4917 4.23989 1.06118 5.28125 1.06118C6.72719 1.06118 7.66189 1.95745 8.19134 2.70933C8.66627 3.38367 8.91409 4.06346 8.9986 4.3244C9.0693 4.54283 9.2717 4.69065 9.5 4.69065C9.7283 4.69065 9.9307 4.54283 10.0014 4.3244C10.0859 4.06346 10.3337 3.38367 10.8087 2.70929C11.3381 1.95745 12.2728 1.06118 13.7188 1.06118C14.7601 1.06118 15.7107 1.4917 16.3954 2.27339C17.0725 3.04632 17.4453 4.09207 17.4453 5.21798C17.4453 7.94192 15.0285 10.0141 11.3701 13.1507Z" fill="#232323"/>
</svg></a>';
                                            }

                                            ?>
                                        </div>
                                    </div>
                                    <div class="et_builder-card-product-lock-icon ">
                                        <?php woocommerce_template_loop_add_to_cart(); ?>
                                        <div class="et_builder-card-product-cart-icon">
                                            <?php echo '<a href="' . $product->get_permalink() . '"><svg width="12" height="15" viewBox="0 0 12 15" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M11.9718 14.4851L11.4202 3.96063L11.3914 3.50794H8.66263V2.82269C8.66263 2.07291 8.38431 1.36547 7.87965 0.830103C7.37467 0.294737 6.70707 0 6.00016 0C5.29325 0 4.62565 0.294737 4.12067 0.830103C3.61601 1.36547 3.33768 2.07291 3.33768 2.82269V3.50794H0.608321L0.580108 3.96232L0.0288469 14.4855L0 15H12L11.9718 14.4851ZM4.25064 2.82269C4.25064 1.79968 5.03553 0.967558 6.00016 0.967558C6.96479 0.967558 7.74968 1.79968 7.74968 2.82269V3.50794H4.25064V2.82269ZM0.970968 14.0321L1.46358 4.47584H3.33768V6.22981H4.25064V4.47584H7.74968V6.22981H8.66263V4.47584H10.537L11.03 14.0321H0.970968Z" fill="#232323"/>
</svg></a>'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="et_builder-block-box-card">
                                <div class="et_builder-card-vendor">
                                    <?php
                                    if ($this->props['show_category'] == 'yes') {
                                        echo  $product->get_categories();
                                    }  ?>
                                </div>
                                <div class="et_builder-card-heading">
                                    <a href="<?php echo  $product->get_permalink(); ?>" title="<?php echo  $product->get_title();  ?>">
                                        <?php echo esc_html($product->get_title()); ?>
                                    </a>
                                </div>
                                <div class="et_builder-card-price">
                                    <span><?php echo wp_kses_post($product->get_price_html()); ?></span>
                                </div>
                                <div class="et_builder-card-review">
                                    <div class="rating-custom">
                                        <?php
                                        if ($this->props['show_rating'] == 'yes') {
                                            $rating = $product->get_average_rating();
                                            $rating_html = '<a href="' . $product->get_permalink() . '#respond"><div class="star-rating ehi-star-rating"><span style="width:' . (($rating / 5) * 100) . '%"></span></div></a>';
                                            echo $rating_html;
                                            echo wp_kses_post(wc_get_rating_html($product->get_average_rating(), $product->get_rating_count()));
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="et_builder-card-swatch">
                                    <?php $attributes = $product->get_attributes();
                                    $attributes = $product->get_attributes();
                                    $globalattributes = $product->get_attribute('pa_color');
                                    foreach ($attributes as $attribute) {

                                        if (!empty($attribute['name'] == 'pa_color')) {
                                            echo '<ul class="et_builder-list-unstyled">';
                                            foreach ($attribute['options'] as $pa) {
                                                $color_code = get_term_meta($pa, 'color_code', true);
                                                $term = get_term($pa, 'pa_color');
                                                echo '<li class="et_builder-swatch-label"><div><a href="#">';
                                                if (!empty($color_code)) { ?>
                                                    <span class="coloroptionattri" style="background: <?php echo  $color_code; ?>"></span>
                                                <?php } else { ?>
                                                    <span class="coloroptionattri" style="background: <?php echo  $term->name; ?>"></span>
                                    <?php
                                                }

                                                echo '</a></div><span class="tooltip">' . $term->name . '</span></li>';
                                            }
                                            echo  '</ul>';
                                        }
                                    }

                                    ?>

                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

        <?php  }
        if ($et_builderstype === 'list') { ?>
            <div class="et_builder-block-content et_builder-block-list">
                <div class="et_builder-block-list et_builder-block-list-desing-list">
                    <?php foreach ($sliderpost_ids as $prditemitem) {
                        $product = wc_get_product($prditemitem);
                    ?>
                        <div class="et_builder-block-box boxstyle-list">
                            <div class="et_builder-card-product">
                                <div class="et_builder-card-product-customlable">
                                    <?php
                                    if ($this->props['et_builder_show_product_sale_badge'] == 'yes') {

                                        $days_to_show = 10; // Show the new badge for 10 days
                                        $product_published_date = strtotime($product->get_date_created());
                                        if ((time() - (60 * 60 * 24 * $days_to_show)) < $product_published_date) {
                                            echo '<span class="onsale">' . 'NEW!' . '</span>';
                                        } else {
                                            if (!$product->managing_stock() && !$product->is_in_stock()) {
                                                printf('<span class="outofstock-badge">%s</span>', __('Stock <br/> Out', 'et_builder'));
                                            } elseif ($product->is_on_sale()) {
                                                printf('<span class="onsale">%s</span>', __('Sale!', 'et_builder'));
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="et_builder-block-box-img">
                                    <a href="<?php echo  $product->get_permalink(); ?>" title="<?php echo  $product->get_title();  ?>">
                                        <?php echo wp_kses_post($product->get_image('woocommerce_thumbnail')); ?>
                                    </a>
                                </div>
                                <div>
                                    <div class="et_builder-card-product-cart-icon-box">
                                        <?php
                                        if ($this->props['show_wishlist'] == 'yes') {
                                            echo '<a href="" class="et_builder-card-product-cart-icon-text">Add to wishlist</a>';
                                        }

                                        ?>
                                        <div class="et_builder-card-product-cart-icon">
                                            <?php
                                            if ($this->props['show_wishlist'] == 'yes') {
                                                echo '<a href="" class="et_builder-card-product-cart-icon-text"><svg width="19" height="16" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M17.1867 1.57174C16.2989 0.558179 15.0673 0 13.7188 0C11.8221 0 10.6214 1.1397 9.94807 2.09582C9.77337 2.34393 9.62477 2.5927 9.5 2.8268C9.37523 2.5927 9.22666 2.34393 9.05193 2.09582C8.37862 1.1397 7.17786 0 5.28125 0C3.93273 0 2.7011 0.558215 1.81326 1.57178C0.966418 2.53865 0.5 3.83357 0.5 5.21798C0.5 6.72496 1.08475 8.1266 2.34025 9.62894C3.46234 10.9717 5.07661 12.3557 6.9459 13.9584C7.64245 14.5557 8.36277 15.1733 9.12963 15.8484L9.15266 15.8687C9.25208 15.9562 9.37604 16 9.5 16C9.62396 16 9.74792 15.9562 9.84734 15.8687L9.87037 15.8484C10.6372 15.1733 11.3576 14.5557 12.0542 13.9584C13.9234 12.3558 15.5377 10.9717 16.6597 9.62894C17.9152 8.12656 18.5 6.72496 18.5 5.21798C18.5 3.83357 18.0336 2.53865 17.1867 1.57174ZM11.3701 13.1507C10.7696 13.6656 10.1516 14.1954 9.5 14.7654C8.84841 14.1955 8.2304 13.6656 7.62983 13.1507C3.9715 10.0141 1.55469 7.94192 1.55469 5.21798C1.55469 4.09207 1.92752 3.04632 2.60452 2.27339C3.2893 1.4917 4.23989 1.06118 5.28125 1.06118C6.72719 1.06118 7.66189 1.95745 8.19134 2.70933C8.66627 3.38367 8.91409 4.06346 8.9986 4.3244C9.0693 4.54283 9.2717 4.69065 9.5 4.69065C9.7283 4.69065 9.9307 4.54283 10.0014 4.3244C10.0859 4.06346 10.3337 3.38367 10.8087 2.70929C11.3381 1.95745 12.2728 1.06118 13.7188 1.06118C14.7601 1.06118 15.7107 1.4917 16.3954 2.27339C17.0725 3.04632 17.4453 4.09207 17.4453 5.21798C17.4453 7.94192 15.0285 10.0141 11.3701 13.1507Z" fill="#232323"/>
</svg></a>';
                                            }

                                            ?>
                                        </div>
                                    </div>
                                    <div class="et_builder-card-product-lock-icon ">
                                        <?php woocommerce_template_loop_add_to_cart(); ?>
                                        <div class="et_builder-card-product-cart-icon">
                                            <?php echo '<a href="' . $product->get_permalink() . '"><svg width="12" height="15" viewBox="0 0 12 15" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M11.9718 14.4851L11.4202 3.96063L11.3914 3.50794H8.66263V2.82269C8.66263 2.07291 8.38431 1.36547 7.87965 0.830103C7.37467 0.294737 6.70707 0 6.00016 0C5.29325 0 4.62565 0.294737 4.12067 0.830103C3.61601 1.36547 3.33768 2.07291 3.33768 2.82269V3.50794H0.608321L0.580108 3.96232L0.0288469 14.4855L0 15H12L11.9718 14.4851ZM4.25064 2.82269C4.25064 1.79968 5.03553 0.967558 6.00016 0.967558C6.96479 0.967558 7.74968 1.79968 7.74968 2.82269V3.50794H4.25064V2.82269ZM0.970968 14.0321L1.46358 4.47584H3.33768V6.22981H4.25064V4.47584H7.74968V6.22981H8.66263V4.47584H10.537L11.03 14.0321H0.970968Z" fill="#232323"/>
</svg></a>'; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="et_builder-block-box-card">
                                <div class="et_builder-card-vendor">
                                    <?php
                                    if ($this->props['show_category'] == 'yes') {
                                        echo  $product->get_categories();
                                    }  ?>
                                </div>
                                <div class="et_builder-card-heading">
                                    <a href="<?php echo  $product->get_permalink(); ?>" title="<?php echo  $product->get_title();  ?>">
                                        <?php echo esc_html($product->get_title()); ?>
                                    </a>
                                </div>
                                <div class="et_builder-card-price">
                                    <span><?php echo wp_kses_post($product->get_price_html()); ?></span>
                                </div>
                                <div class="et_builder-card-review">
                                    <div class="rating-custom">
                                        <?php
                                        if ($this->props['show_rating'] == 'yes') {
                                            $rating = $product->get_average_rating();
                                            $rating_html = '<a href="' . $product->get_permalink() . '#respond"><div class="star-rating ehi-star-rating"><span style="width:' . (($rating / 5) * 100) . '%"></span></div></a>';
                                            echo $rating_html;
                                            echo wp_kses_post(wc_get_rating_html($product->get_average_rating(), $product->get_rating_count()));
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="et_builder-card-swatch">
                                    <?php $attributes = $product->get_attributes();
                                    $attributes = $product->get_attributes();
                                    $globalattributes = $product->get_attribute('pa_color');
                                    foreach ($attributes as $attribute) {

                                        if (!empty($attribute['name'] == 'pa_color')) {
                                            echo '<ul class="et_builder-list-unstyled">';
                                            foreach ($attribute['options'] as $pa) {
                                                $color_code = get_term_meta($pa, 'color_code', true);
                                                $term = get_term($pa, 'pa_color');
                                                echo '<li class="et_builder-swatch-label"><div><a href="#">';
                                                if (!empty($color_code)) { ?>
                                                    <span class="coloroptionattri" style="background: <?php echo  $color_code; ?>"></span>
                                                <?php } else { ?>
                                                    <span class="coloroptionattri" style="background: <?php echo  $term->name; ?>"></span>
                                    <?php
                                                }

                                                echo '</a></div><span class="tooltip">' . $term->name . '</span></li>';
                                            }
                                            echo  '</ul>';
                                        }
                                    }

                                    ?>

                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>

        <?php   }
        if ($et_builderstype === 'slider') { ?>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/6.7.0/swiper-bundle.min.css" integrity="sha512-x9WWODH0qw+aXoiT69XzI4WX2MXpqvVGY+O9HpJ4wbjEubpjDIM5CwTrU/OFJg7tSMbBMpgwz2Qp6xlUsk+FgA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/6.7.0/swiper-bundle.min.js" integrity="sha512-qqdD5ZLIGB5PCqCk1OD8nFBr/ngB5w+Uw35RE/Ivt5DK35xl1PFVkuOgAbqFpvtoxX6MpRGLmIqixzdhFOJhnA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

            <div class="ella-block-style_1 slider-section slider-4 ella-block-slider" id="ellaslider-sections">
                <div class="slider-column ella-slider-column">
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <?php foreach ($sliderpost_ids as $prditemitem) {
                                $product = wc_get_product($prditemitem); ?>
                                <div class="ella-block-box swiper-slide">
                                    <div class="ella-card-product">
                                        <div class="ella-card-product-customlable">
                                            <?php
                                            if ($this->props['et_builder_show_product_sale_badge'] == 'yes') {

                                                $days_to_show = 10; // Show the new badge for 10 days
                                                $product_published_date = strtotime($product->get_date_created());
                                                if ((time() - (60 * 60 * 24 * $days_to_show)) < $product_published_date) {
                                                    echo '<span class="onsale">' . 'NEW!' . '</span>';
                                                } else {
                                                    if (!$product->managing_stock() && !$product->is_in_stock()) {
                                                        printf('<span class="outofstock-badge">%s</span>', __('Stock <br/> Out', 'ella'));
                                                    } elseif ($product->is_on_sale()) {
                                                        printf('<span class="onsale">%s</span>', __('Sale!', 'ella'));
                                                    }
                                                }
                                            }
                                            ?>
                                        </div>
                                        <div class="ella-block-box-img">
                                            <a href="<?php echo  $product->get_permalink(); ?>" title="<?php echo  $product->get_title();  ?>">
                                                <?php echo wp_kses_post($product->get_image('woocommerce_thumbnail')); ?>
                                            </a>
                                        </div>
                                        <div>
                                            <div class="ella-card-product-cart-icon-box">
                                                <?php
                                                if ($this->props['show_wishlist'] == 'yes') {
                                                    echo '<a href="" class="ella-card-product-cart-icon-text">Add to wishlist</a>';
                                                }

                                                ?>
                                                <div class="ella-card-product-cart-icon">
                                                    <?php
                                                    if ($this->props['show_wishlist'] == 'yes') {
                                                        echo '<a href="" class="ella-card-product-cart-icon-text"><svg width="19" height="16" viewBox="0 0 19 16" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M17.1867 1.57174C16.2989 0.558179 15.0673 0 13.7188 0C11.8221 0 10.6214 1.1397 9.94807 2.09582C9.77337 2.34393 9.62477 2.5927 9.5 2.8268C9.37523 2.5927 9.22666 2.34393 9.05193 2.09582C8.37862 1.1397 7.17786 0 5.28125 0C3.93273 0 2.7011 0.558215 1.81326 1.57178C0.966418 2.53865 0.5 3.83357 0.5 5.21798C0.5 6.72496 1.08475 8.1266 2.34025 9.62894C3.46234 10.9717 5.07661 12.3557 6.9459 13.9584C7.64245 14.5557 8.36277 15.1733 9.12963 15.8484L9.15266 15.8687C9.25208 15.9562 9.37604 16 9.5 16C9.62396 16 9.74792 15.9562 9.84734 15.8687L9.87037 15.8484C10.6372 15.1733 11.3576 14.5557 12.0542 13.9584C13.9234 12.3558 15.5377 10.9717 16.6597 9.62894C17.9152 8.12656 18.5 6.72496 18.5 5.21798C18.5 3.83357 18.0336 2.53865 17.1867 1.57174ZM11.3701 13.1507C10.7696 13.6656 10.1516 14.1954 9.5 14.7654C8.84841 14.1955 8.2304 13.6656 7.62983 13.1507C3.9715 10.0141 1.55469 7.94192 1.55469 5.21798C1.55469 4.09207 1.92752 3.04632 2.60452 2.27339C3.2893 1.4917 4.23989 1.06118 5.28125 1.06118C6.72719 1.06118 7.66189 1.95745 8.19134 2.70933C8.66627 3.38367 8.91409 4.06346 8.9986 4.3244C9.0693 4.54283 9.2717 4.69065 9.5 4.69065C9.7283 4.69065 9.9307 4.54283 10.0014 4.3244C10.0859 4.06346 10.3337 3.38367 10.8087 2.70929C11.3381 1.95745 12.2728 1.06118 13.7188 1.06118C14.7601 1.06118 15.7107 1.4917 16.3954 2.27339C17.0725 3.04632 17.4453 4.09207 17.4453 5.21798C17.4453 7.94192 15.0285 10.0141 11.3701 13.1507Z" fill="#232323"/>
</svg></a>';
                                                    }

                                                    ?>
                                                </div>
                                            </div>
                                            <div class="ella-card-product-lock-icon ">
                                                <?php woocommerce_template_loop_add_to_cart(); ?>
                                                <div class="ella-card-product-cart-icon">
                                                    <?php echo '<a href="' . $product->get_permalink() . '"><svg width="12" height="15" viewBox="0 0 12 15" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M11.9718 14.4851L11.4202 3.96063L11.3914 3.50794H8.66263V2.82269C8.66263 2.07291 8.38431 1.36547 7.87965 0.830103C7.37467 0.294737 6.70707 0 6.00016 0C5.29325 0 4.62565 0.294737 4.12067 0.830103C3.61601 1.36547 3.33768 2.07291 3.33768 2.82269V3.50794H0.608321L0.580108 3.96232L0.0288469 14.4855L0 15H12L11.9718 14.4851ZM4.25064 2.82269C4.25064 1.79968 5.03553 0.967558 6.00016 0.967558C6.96479 0.967558 7.74968 1.79968 7.74968 2.82269V3.50794H4.25064V2.82269ZM0.970968 14.0321L1.46358 4.47584H3.33768V6.22981H4.25064V4.47584H7.74968V6.22981H8.66263V4.47584H10.537L11.03 14.0321H0.970968Z" fill="#232323"/>
</svg></a>'; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ella-block-box-card">
                                        <div class="ella-card-vendor">
                                            <?php
                                            if ($this->props['show_category'] == 'yes') {
                                                echo  $product->get_categories();
                                            }  ?>
                                        </div>
                                        <div class="ella-card-heading">
                                            <a href="<?php echo  $product->get_permalink(); ?>" title="<?php echo  $product->get_title();  ?>">
                                                <?php echo esc_html($product->get_title()); ?>
                                            </a>
                                        </div>
                                        <div class="ella-card-price">
                                            <span><?php echo wp_kses_post($product->get_price_html()); ?></span>
                                        </div>
                                        <div class="ella-card-review">
                                            <div class="rating-custom">
                                                <?php
                                                if ($this->props['show_rating'] == 'yes') {

                                                    $rating = $product->get_average_rating();

                                                    $rating_html = '<a href="' . $product->get_permalink() . '#respond"><div class="star-rating ehi-star-rating"><span style="width:' . (($rating / 5) * 100) . '%"></span></div></a>';
                                                    echo $rating_html;
                                                    echo wp_kses_post(wc_get_rating_html($product->get_average_rating(), $product->get_rating_count()));
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="ella-card-swatch">
                                            <?php
                                            $attributes = $product->get_attributes();
                                            $attributes = $product->get_attributes();
                                            $globalattributes = $product->get_attribute('pa_color');
                                            foreach ($attributes as $attribute) {

                                                if (!empty($attribute['name'] == 'pa_color')) {
                                                    echo '<ul class="ella-list-unstyled">';
                                                    foreach ($attribute['options'] as $pa) {
                                                        $color_code = get_term_meta($pa, 'color_code', true);
                                                        $term = get_term($pa, 'pa_color');
                                                        echo '<li class="ella-swatch-label"><div><a href="#">';
                                                        if (!empty($color_code)) { ?>
                                                            <span class="coloroptionattri" style="background: <?php echo  $color_code; ?>"></span>
                                                        <?php } else { ?>
                                                            <span class="cornflowerblue" style="background: <?php echo  $term->name; ?>"></span>
                                            <?php
                                                        }

                                                        echo '</a></div><span class="tooltip">' . $term->name . '</span></li>';
                                                    }
                                                    echo  '</ul>';
                                                }
                                            }

                                            ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <span class="swiper-pagination"></span>
                <span class="swiper-button-prev"></span>
                <span class="swiper-button-next"></span>

            </div>
            <script>
                new Swiper("#ellaslider-sections .swiper-container", {
                    // Optional parameters
                    slidesPerView: 2,
                    grabCursor: true,
                    loop: true,
                    mousewheel: false,
                    spaceBetween: 30,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false
                    },

                    pagination: {
                        el: ".swiper-pagination",
                        dynamicBullets: true,
                        clickable: true
                    },
                    navigation: {
                        nextEl: ".swiper-button-next",
                        prevEl: ".swiper-button-prev"
                    },
                    // Responsive breakpoints
                    breakpoints: {
                        320: {
                            slidesPerView: 1,
                            spaceBetween: 20
                        },
                        768: {
                            slidesPerView: 2,
                            spaceBetween: 20
                        },
                        1025: {
                            slidesPerView: 3,
                            spaceBetween: 20
                        }
                    }
                });
            </script>


<?php  }
        return ob_get_clean();
    }
}

new My_Custom_Module;
