<?php

// Регистрация таба для полей

add_filter( 'woocommerce_product_data_tabs', 'add_my_custom_product_data_tab' );
function add_my_custom_product_data_tab( $product_data_tabs ) {
	$product_data_tabs['my-custom-tab'] = array(
		'label' => __( 'My Custom Tab', 'my_text_domain' ),
		'target' => 'my_custom_product_data',
	);
	return $product_data_tabs;
}


//добавляем поля в таб

add_action( 'woocommerce_product_data_panels', 'add_my_custom_product_data_fields' );
function add_my_custom_product_data_fields() {
	global $woocommerce, $post, $product_object;
?>

	<div id="my_custom_product_data" class="panel woocommerce_options_panel" style="display: none;">
        <?php


		//добавли поле дата
        woocommerce_wp_text_input( array(
            'id' => '_datafield',
            'type' => 'date',
            'label' => __( 'Дата', 'woocommerce' ),
            'placeholder' => '',
            'desc_tip'          => '',
            'custom_attributes' => array(),
            'description'       => __( 'Дата создания товара', 'woocommerce' ),
        ) );

		//добавили список селект. Поле для картинки нет, делаем через HTML+JQuery
        woocommerce_wp_select( array(
            'id'=> '_producttype',
            'label' => 'Выпадающий список',
            'options' => array(
                'rare' => __( 'Rare', 'woocommerce' ),
                'frequent' => __( 'Frequent', 'woocommerce' ),
                'unusual' => __( 'Unusual', 'woocommerce' ),
                ),
            ) );
        ?>
		
		<div class="form-field">
			<label for="">Выбрать картинку</label>
                <?php
                    $picture_id = $product_object->get_meta( 'picture_id' );
					$picture_scr = wp_get_attachment_image_url( $picture_id, 'thumbnail ');
                ?>

                <div class="product-thumbnail-select">
                    <div id="product_thumbnail">
						<?php 
							add_filter( 'wp_calculate_image_srcset_meta', '__return_null' );

							if ( $picture_id ) {
								echo wp_get_attachment_image( $picture_id, 'thumbnail', false, ['id' => 'picture_img', 'srcset' => ''] );
							} else {
								echo wc_placeholder_img( 'thumbnail', ['id' => 'picture_img', 'srcset' => ''] );
							}

							remove_filter( 'wp_calculate_image_srcset_meta', '__return_null' );
						?>
                    </div>
                    <div id="product_thumbnail_select">
                        <input type="hidden" id="picture_id" name="picture_id" value="<?php echo $picture_id; ?>" />
                        <button type="button" class="upload_image_button button"><?php esc_html_e( 'Upload/Add image', 'oakbluebird' ); ?></button>
                        <button type="button" class="remove_image_button button"><?php esc_html_e( 'Remove image', 'oakbluebird' ); ?></button>
                    </div>
                </div>
                
					<p class="form-row">
                        <input type="button" value="Очиcтить" id="custom-button-reset">
					</p>
					<p class="form-row">
                    	<button type="submit">Update</button>
					</p>

        </div>
	</div>	
<?php
}

// Вывод\замена картинки в список товаров на бекенд
add_filter( 'woocommerce_product_get_image_id', 'replace_woocommerce_product_image_id', 10, 2 );

function replace_woocommerce_product_image_id( $ID, $product ){
	// не меняем картинку если админка
	//if ( is_admin() ) {
	//	return $ID;
	//}

	// Получаем ID с БД
	$custom_image_ID = $product->get_meta('picture_id');

	// Если поле пустое, возвращаем стандарный ID
	if ( ! $custom_image_ID ) {
		return $ID;
	}
	
	// Меняем ID 
	$ID = (int) $custom_image_ID;
	
	return $ID;
}


// Форма для сохранения на фронте через ajax
function custom_product_create() {
	$product = new WC_Product();

	if ( isset( $_POST['product_name']) ) {
		$product->set_name( wc_clean( wp_unslash($_POST['product_name']) ) );
	}

	if ( isset( $_POST['product_price']) ) {
		$product->set_regular_price( (int) wc_clean( wp_unslash($_POST['product_price']) ) );
	}

	if ( isset( $_POST['product_date']) ) {
		$product->update_meta_data( '_datafield', wc_clean( wp_unslash($_POST['product_date']) ) );
	}

	if ( isset( $_POST['product_type']) ) {
		$product->update_meta_data( '_producttype', wc_clean( wp_unslash($_POST['product_type']) ) );
	}

	if ( isset( $_FILES['product_picture'] ) ) {

		$attachment_id = media_handle_upload( 'product_picture', 0 );

		if ( !is_wp_error($attachment_id) ) {
			$product->update_meta_data( 'picture_id', $attachment_id );
		}
	}


	$product->save();

    wp_send_json_success(array(
		'message' => sprintf('Товар "%s" создан успешно. Перейти<a href="%s"> по ссылке </a> чтобы посмотреть.', $product->get_name(), $product->get_permalink() ),
		'debug'	=> $_POST
	));
}

add_action( 'wp_ajax_custom_product_create', 'custom_product_create' );
add_action( 'wp_ajax_nopriv_custom_product_create', 'custom_product_create' );