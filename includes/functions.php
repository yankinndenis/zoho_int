<?php
function get_items(){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://eojgbzdx1m0fjzm.m.pipedream.net');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    $headers = array();
    $headers[] = 'Content-Type: application/json;charset=UTF-8';
    $headers[] = 'Authorization: bearer df15ec91-8c15-49aa-a95f-cd87cdd02bdd';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return json_decode($result, true);
}
function add_products(){
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://reqbin.com/echo/put/json');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $products);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return json_decode($result, true);
}
function check($sku){
    
    global $woocommerce;
    $stock = 0;
    $args = array(
         'post_type'  => 'product',
         'meta_query' => array(
             array(
                 'key'   => '_sku',
                 'value' => $sku,
             )
         )
    );
    $posts = get_posts( $args );
    if(sizeof($posts)){
        
        $product_id = $posts[0]->ID;
        $product_id = intval($product_id);
        $product = wc_get_product( $product_id );
        $stock = $product->get_stock_quantity();
    }else{
        $args = array(
            'post_type'  => 'product_variation',
            'meta_query' => array(
                array(
                    'key'   => '_sku',
                    'value' => $sku,
                )
            )
        );
        $posts = get_posts( $args );
        if(sizeof($posts)){
            $product_id = $posts[0]->post_parent;
            $product_id = intval($product_id);
            $product = wc_get_product( $product_id );
            $stock = $product->get_stock_quantity();
        }
    }
    return $stock;
}
function my_cron_schedules($schedules){
    if(!isset($schedules["5min"])){
        $schedules["5min"] = array(
            'interval' => 5*60,
            'display' => __('Once every 5 minutes'));
    }
    if(!isset($schedules["15min"])){
        $schedules["15min"] = array(
            'interval' => 15*60,
            'display' => __('Once every 15 minutes'));
    }
    if(!isset($schedules["1min"])){
        $schedules["1min"] = array(
            'interval' => 1*60,
            'display' => __('Once every 1 minutes'));
    }
    return $schedules;
}
add_filter('cron_schedules','my_cron_schedules');


// add_action( 'my_hourly_event', 'send_orders' );
add_action( 'my_hourly_event', 'addZohotoDb' );

function add_my_cron_event() {

    if( ! wp_next_scheduled( 'my_hourly_event' ) ) {
        wp_schedule_event( time(), '15min', 'my_hourly_event');
    }
}
add_action( 'wp', 'add_my_cron_event' );

function addZohotoDb(){
    global $wpdb;
    $table = $wpdb->prefix . 'zoho_orders';
    $wpdb->query("TRUNCATE TABLE $table");

    $url = get_option('zoho_url');
    $token = get_option('zoho_authorization');
    $organization = get_option('zoho_organization');
    $items = Zoho_int::get_items($url, $token, $organization);
    
    foreach($items as $item){
        $sku = strval($item['SKU']);
        $wpdb->insert($table, array(
            'item_id' => $item['Item_ID'],
            'available' => $item['QTY_Available'],
            'category' => $item['Category_Name'],
            'name' => $item['Name'], 
            'sku' => $item['SKU'], 
            'item_description' =>  $item['Item_Description'], 
            'upc' =>  $item['UPC'], 
            'wholesale_price' =>  $item['Wholesale_Price'], 
            'sell_at_price' =>  $item['Sell_At_Price'], 
            'msrp' =>  $item['MSRP'], 
            'weight' =>  $item['Weight'], 
            'weight_unit' =>  $item['Weight_Unit'], 
            'length_field' =>  $item['Length_field'], 
            'width' =>  $item['Width'], 
            'height' =>  $item['Height'], 
            'dimension_unit' => $item['Dimension_Unit'], 
            'distro_price' => $item['Distro_Price'], 
        ));
    }
    mail("rotuss1206@gmail.com","My subject3", 'day1distro - test3');
}

function fixed($json){
    return str_replace('`', '"', preg_replace(
            '/`([^`]+)`(?=`)/', 
            '\\\"$1\"', 
            str_replace(['\"', '"'], '`', $json))
    );
}

function send_test(){
    $data = trim($_POST['textarea']);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://eojgbzdx1m0fjzm.m.pipedream.net');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $headers = array();
    $headers[] = 'Content-Type: application/json;charset=UTF-8';
    $headers[] = 'Authorization: bearer df15ec91-8c15-49aa-a95f-cd87cdd02bdd';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    echo json_encode($result, 320);
    wp_die();

} //endfunction
add_action('wp_ajax_send_test', 'send_test');
add_action('wp_ajax_nopriv_send_test', 'send_test');

// insert new table checked_orders
function insert_orders_table_into_db(){
  global $wpdb;
  // set the default character set and collation for the table
  $charset_collate = $wpdb->get_charset_collate();
  $table = $wpdb->prefix . 'checked_orders';
  // Check that the table does not already exist before continuing
  $sql = "CREATE TABLE IF NOT EXISTS $table (
  id bigint(50) NOT NULL AUTO_INCREMENT,
  oreder_id bigint(20) NOT NULL,
  is_checked bigint(20),
  PRIMARY KEY (id)
  ) $charset_collate;";
  require_once ABSPATH . 'wp-admin/includes/upgrade.php';
  dbDelta( $sql );
  $is_error = empty( $wpdb->last_error );
  return $is_error;
}

function insert_zoho_table_into_db(){
  global $wpdb;
  // set the default character set and collation for the table
  $charset_collate = $wpdb->get_charset_collate();
  $table = $wpdb->prefix . 'zoho_orders';
  // Check that the table does not already exist before continuing
  $sql = "CREATE TABLE IF NOT EXISTS $table (
  id bigint(50) NOT NULL AUTO_INCREMENT,
  item_id bigint(20),
  available bigint(20),
  category VARCHAR(40),
  name VARCHAR(40),
  sku VARCHAR(40),
  item_description VARCHAR(40),
  upc VARCHAR(40),
  wholesale_price VARCHAR(40),
  sell_at_price VARCHAR(40),
  msrp VARCHAR(40),
  weight VARCHAR(40),
  weight_unit VARCHAR(40),
  length_field VARCHAR(40),
  width VARCHAR(40),
  height VARCHAR(40),
  dimension_unit VARCHAR(40),
  distro_price VARCHAR(40),
  PRIMARY KEY (id)
  ) $charset_collate;";
  require_once ABSPATH . 'wp-admin/includes/upgrade.php';
  dbDelta( $sql );
  $is_error = empty( $wpdb->last_error );
  return $is_error;
}

// Add order ID to checked_orders
function insert_checked_order($order_id = NULL){
    global $wpdb;
    if($order_id != NULL){
        $table = $wpdb->prefix . 'checked_orders';
        $data = [ 'oreder_id' => $order_id, 'is_checked' => 1 ];

        $wpdb->insert( $table, $data );

        $my_id = $wpdb->insert_id;
        return $my_id;
    }else{
        return 'Please add an order ID!';
    }
}

// Add order ID to checked_orders
function select_checked_order($order_id = NULL){
    global $wpdb;
    if($order_id != NULL){
        $table = $wpdb->prefix . 'wc_order_stats';
        $results = $wpdb->get_results( "SELECT * FROM $table WHERE order_id = $order_id");
        return $results;
    }else{
        return 'Please add an order ID!';
    }  
}

// get orders from database
function get_orders(){
    global $wpdb;
    $table_posts = $wpdb->prefix . 'posts';
    $table_postmeta = $wpdb->prefix . 'postmeta';
   $results = $wpdb->get_results( "SELECT ID FROM $table_posts WHERE post_type = 'shop_order'");
   return $results;
}
class order_item{

}
function send_orders(){
    // Get url from pligin settings
    $url = get_option('zoho_url');
    // Get token from pligin settings
    $token = get_option('zoho_authorization');
    // Get organization from pligin settings
    $organization = get_option('zoho_organization');
    // Get items from pligin settings
    // $items = Zoho_int::get_items($url, $token);
    $items = getZohoFromDb();
    // Get orders 
    $orders = get_orders();
    foreach ($orders as $order_obj) {
        $order_id_int = intval($order_obj->ID);
        $select_order = select_checked_order($order_id_int);
        if(empty($select_order)){
            $order = new WC_Order($order_obj->ID);
            $line_items = [];
            $check='';
            foreach ( $order->get_items() as $item_id => $item ) {
                $product_id = $item->get_product_id();
                $product = wc_get_product( $product_id );
                $item_sku = $product->get_sku();
                $item_id='';
                foreach($items as $item_z){
                    if($item_sku == $item_z->sku){
                        $check = 'match';
                        $item_id = $item_z->item_id;
                    }
                }
                if($check == 'match'){
                    $quantity = $item->get_quantity();
                    $line_item = new order_item();
                    $line_item->item_id = $item_id;
                    $line_item->quantity = $quantity;
                    array_push($line_items, $line_item);
                }
            }
            if($check == 'match'){
                $shipping_address = array(
                  "attention" => $order->get_shipping_first_name().' '.$order->get_shipping_last_name(),
                  "address" => $order->get_shipping_address_1(),
                  "street2" => $order->get_shipping_address_2(),
                  "city" => $order->get_shipping_city(),
                  "state" => $order->get_shipping_state(),
                  "zip" => $order->get_shipping_postcode(),
                  "phone" => $order->get_billing_phone(),
                  "country" => $order->get_shipping_country()
                );
                $data = array(
                    'customer_id' => $organization,
                    'date' => $order->get_date_created(),
                    'reference_number' => '1',
                    'line_items' => $line_items,
                    'notes' => '',
                    "shipping_address" => $shipping_address
                );
                $json_data = json_encode($data);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://eoiqdujp7vhfxd5.m.pipedream.net');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

                $headers = array();
                $headers[] = 'Content-Type: application/json;charset=UTF-8';
                $headers[] = 'Authorization: bearer df15ec91-8c15-49aa-a95f-cd87cdd02bdd';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $result = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                }
                curl_close($ch);
                insert_checked_order($order_id_int);

                echo json_encode($result, 320);
            }
        }
    }
    mail("rotuss1206@gmail.com","My subject", 'day1distro - test');
}

// Add table if not exist
insert_orders_table_into_db();
insert_zoho_table_into_db();

add_action( 'woocommerce_new_order', 'create_invoice_for_wc_order',  50, 1  );
function create_invoice_for_wc_order( $order_id ) {
    // Get url from pligin settings
    $url = get_option('zoho_url');
    // Get token from pligin settings
    $token = get_option('zoho_authorization');
    // Get organization from pligin settings
    $organization = get_option('zoho_organization');
    // Get items from pligin settings
    // $items = Zoho_int::get_items($url, $token);
    $items = getZohoFromDb();
    
    $select_order = select_checked_order($order_id);
    
        
        $order = wc_get_order($order_id);

        $line_items = [];
        $check='';
        
        $order_items = $order->get_items();

        foreach ( $order_items as $item_id => $item ) {
            
            if (isset($item['variation_id'])) { 
                $product = wc_get_product( $item['variation_id'] );

            } else {
                $product = wc_get_product( $item['product_id'] );
            }
            if($product){
                $item_sku = $product->get_sku();
            }
            $item_id='';
            foreach($items as $item_z){
                if($item_sku == $item_z->sku){
                    $check = 'match';
                    $item_id = $item_z->item_id;
                }
            }
            
            if($check == 'match'){
                $quantity = $item->get_quantity();
                $line_item = new order_item();
                $line_item->item_id = $item_id;
                $line_item->quantity = $quantity;
                array_push($line_items, $line_item);
            }
        }
        if($check == 'match'){
            $shipping_address = array(
              "attention" => $order->get_shipping_first_name().' '.$order->get_shipping_last_name(),
              "address" => $order->get_shipping_address_1(),
              "street2" => $order->get_shipping_address_2(),
              "city" => $order->get_shipping_city(),
              "state" => $order->get_shipping_state(),
              "zip" => $order->get_shipping_postcode(),
              "phone" => $order->get_billing_phone(),
              "country" => $order->get_shipping_country()
            );
            $data = array(
                'customer_id' => $organization,
                'date' => $order->get_date_created(),
                'reference_number' => '1',
                'line_items' => $line_items,
                'notes' => '',
                "shipping_address" => $shipping_address
            );
            $json_data = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://eoiqdujp7vhfxd5.m.pipedream.net');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

            $headers = array();
            $headers[] = 'Content-Type: application/json;charset=UTF-8';
            $headers[] = 'Authorization: bearer df15ec91-8c15-49aa-a95f-cd87cdd02bdd';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                echo 'Error:' . curl_error($ch);
            }
            curl_close($ch);
            insert_checked_order($order_id_int);
            update_option('btb',$result);
        }

}

function getZohoFromDb(){
    global $wpdb;
    $table = $wpdb->prefix . 'zoho_orders';
    $results = $wpdb->get_results( "SELECT * FROM $table");
    return $results;
}

function getProductsToUpdate(){
    global $wpdb;
    $postmeta = $wpdb->prefix . 'postmeta';
    $posts = $wpdb->prefix . 'posts';
     
    $items = getZohoFromDb(); 
    
    foreach ($items as $item) {

        $qty = intval($item->available);
        $price = intval($item->msrp);
        $sku = $item->sku;
        $ids = $wpdb->get_results( "SELECT post_id FROM $postmeta WHERE meta_key = '_sku' AND meta_value = '$sku'");
        if(!empty($ids)){
            
            $id = $ids[0]->post_id;
            $product = wc_get_product( $id );
            $posts_array = $wpdb->get_results( "SELECT post_type FROM $posts WHERE ID = '$id'");
            if ( $posts_array[0]->post_type == 'product_variation' ) {
                $product->set_props(
                    array(
                        'regular_price' => $price
                    )
                );
                $product->set_manage_stock( true );
                $product->set_stock_quantity( $qty );
                $product->save();
            }else{
                $product->set_regular_price( $price );
                $product->set_manage_stock( true );
                $product->set_stock_quantity( $qty );
                $product->save();
            }
            // $posts = $wpdb->get_results( "SELECT post_type FROM $posts WHERE ID = 32250");
            
        }else{
            // products to create
            // $name = $item->name;
            // $sku = $item->name;
            // $name_array = explode('/',$name);
            // var_dump($name_array);
            // $product_id = 36457;
            // $variation_data = [
            //     'attributes' =>[
            //         'attribute1' => 'name33',
            //         'attribute2' => 'name22',
            //     ],
            //     'sku' => '32135',
            //     'sale_price' => 22,
            //     'regular_price' => 33,
            //     'stock_qty' => 40
            // ];
            // create_product_variation( $product_id, $variation_data );
        }
        
    }
     
}

add_action( 'init', 'process_post' );
function process_post() {
    // echo "<pre>";
    // getProductsToUpdate();
}

function create_product_variation( $product_id, $variation_data ){
    // Get the Variable product object (parent)
    wp_set_object_terms( $product_id, 'variable', 'product_type' );

    $taxonomys = [];
    foreach ($variation_data['attributes'] as $attribute => $term_name ){
        $taxonomy = 'pa_'.$attribute;

        $taxonomy = array(
               'name'=>$taxonomy,
               'value'=> $term_name,
               'is_visible' => '1',
               'is_variation' => '1',
               'is_taxonomy' => '1'
             );
        array_push($taxonomys, $taxonomy);
        
    }
    update_post_meta( $product_id, '_product_attributes', $taxonomys);
    $product = wc_get_product($product_id);

    $variation_post = array(
        'post_title'  => $product->get_name(),
        'post_name'   => 'product-'.$product_id.'-variation',
        'post_status' => 'publish',
        'post_parent' => $product_id,
        'post_type'   => 'product_variation',
        'guid'        => $product->get_permalink()
    );

    // Creating the product variation
    $variation_id = wp_insert_post( $variation_post );

    // Get an instance of the WC_Product_Variation object
    $variation = new WC_Product_Variation( $variation_id );

    // Iterating through the variations attributes
    foreach ($variation_data['attributes'] as $attribute => $term_name )
    {
        $taxonomy = 'pa_'.$attribute; // The attribute taxonomy

        // If taxonomy doesn't exists we create it (Thanks to Carl F. Corneil)
        if( ! taxonomy_exists( $taxonomy ) ){
            register_taxonomy(
                $taxonomy,
               'product_variation',
                array(
                    'hierarchical' => false,
                    'label' => ucfirst( $attribute ),
                    'query_var' => true,
                    'rewrite' => array( 'slug' => sanitize_title($attribute) ), // The base slug
                ),
            );
        }

        // Check if the Term name exist and if not we create it.
        if( ! term_exists( $term_name, $taxonomy ) )
            wp_insert_term( $term_name, $taxonomy ); // Create the term

        $term_slug = get_term_by('name', $term_name, $taxonomy )->slug; // Get the term slug

        // Get the post Terms names from the parent variable product.
        $post_term_names =  wp_get_post_terms( $product_id, $taxonomy, array('fields' => 'names') );

        // Check if the post term exist and if not we set it in the parent variable product.
        if( ! in_array( $term_name, $post_term_names ) )
            wp_set_post_terms( $product_id, $term_name, $taxonomy, true );

        // Set/save the attribute data in the product variation
        update_post_meta( $variation_id, 'attribute_'.$taxonomy, $term_slug );
    }

    ## Set/save all other data

    // SKU
    if( ! empty( $variation_data['sku'] ) )
        $variation->set_sku( $variation_data['sku'] );

    // Prices
    if( empty( $variation_data['sale_price'] ) ){
        $variation->set_price( $variation_data['regular_price'] );
    } else {
        $variation->set_price( $variation_data['sale_price'] );
        $variation->set_sale_price( $variation_data['sale_price'] );
    }
    $variation->set_regular_price( $variation_data['regular_price'] );

    // Stock
    if( ! empty($variation_data['stock_qty']) ){
        $variation->set_stock_quantity( $variation_data['stock_qty'] );
        $variation->set_manage_stock(true);
        $variation->set_stock_status('');
    } else {
        $variation->set_manage_stock(false);
    }
    
    $variation->set_weight(''); // weight (reseting)

    $variation->save(); // Save the data
}

function so_29549525_create_attribute_taxonomies() {

    $attributes = wc_get_attribute_taxonomies();

    $slugs = wp_list_pluck( $attributes, 'attribute1' );

    if ( ! in_array( 'attribute1', $slugs ) ) {

        $args = array(
            'slug'    => 'attribute1',
            'name'   => __( 'Attribute 1', 'staging23.day1distro.com' ),
            'type'    => 'select',
            'orderby' => 'menu_order',
            'has_archives'  => false,
        );

        $result = wc_create_attribute( $args );

    }

    if ( ! in_array( 'attribute2', $slugs ) ) {

        $args = array(
            'slug'    => 'attribute2',
            'name'   => __( 'Attribute 2', 'staging23.day1distro.com' ),
            'type'    => 'select',
            'orderby' => 'menu_order',
            'has_archives'  => false,
        );

        $result = wc_create_attribute( $args );

    }
}
add_action( 'admin_init', 'so_29549525_create_attribute_taxonomies' );

function update_products_by_zoho(){

  getProductsToUpdate();
  echo json_encode($result, 320);
  wp_die();

} //endfunction

add_action('wp_ajax_update_products_by_zoho', 'update_products_by_zoho');
add_action('wp_ajax_nopriv_update_products_by_zoho', 'update_products_by_zoho');

function update_products_by_zoho_each(){

  getProductsToUpdate();
  echo json_encode($result, 320);
  wp_die();

} //endfunction

add_action('wp_ajax_update_products_by_zoho_each', 'update_products_by_zoho_each');
add_action('wp_ajax_nopriv_update_products_by_zoho_each', 'update_products_by_zoho_each');

// global $wpdb;
//     $table = $wpdb->prefix . 'zoho_orders';
//     $wpdb->query("TRUNCATE TABLE $table");

//     $url = get_option('zoho_url');
//     $token = get_option('zoho_authorization');
//     $organization = get_option('zoho_organization');
//     $items = Zoho_int::get_items($url, $token, $organization);
    
//     foreach($items as $item){
//         $sku = strval($item['SKU']);
//         $wpdb->insert($table, array(
//             'item_id' => $item['Item_ID'],
//             'available' => $item['QTY_Available'],
//             'category' => $item['Category_Name'],
//             'name' => $item['Name'], 
//             'sku' => $item['SKU'], 
//             'item_description' =>  $item['Item_Description'], 
//             'upc' =>  $item['UPC'], 
//             'wholesale_price' =>  $item['Wholesale_Price'], 
//             'sell_at_price' =>  $item['Sell_At_Price'], 
//             'msrp' =>  $item['MSRP'], 
//             'weight' =>  $item['Weight'], 
//             'weight_unit' =>  $item['Weight_Unit'], 
//             'length_field' =>  $item['Length_field'], 
//             'width' =>  $item['Width'], 
//             'height' =>  $item['Height'], 
//             'dimension_unit' => $item['Dimension_Unit'], 
//             'distro_price' => $item['Distro_Price'], 
//         ));
//     }