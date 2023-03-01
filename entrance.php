<?php
$url = get_option('zoho_url');
$token = get_option('zoho_authorization');
$organization = get_option('zoho_organization');
// $api_items = Zoho_int::get_items($url, $token, $organization);
$items = getZohoFromDb();
$page = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") 
    . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if(isset($_POST['update_db'])){
        addZohotoDb();
    }
?>
<style>
    .container{
        padding: 0 5%;
    }
</style>

<?php
    // echo '<pre>';
    // var_dump($items);
?>

<div class="container">
    <div style="margin:10px 0; display: flex;">
        <button style="margin-right:10px;" id="reset_zoho">Reset</button>
        <button style="margin-right:10px;" id="update_zoho">Update</button>
        <button id="update_zoho_each">Update selected</button>
        <form style="margin-left: 10px;" action="<?php echo $page; ?>" method="POST">
            <input type="hidden" name="update_db" value="1">
            <button type="submit">Update database</button>
        </form>
    </div>
    <table id="myTable" class="tablesorter tablesorter-blue ">
      <thead>
        <tr>
            <td><?php echo $item->available; ?></td>
            <th>QTY Available</th>
            <th>Category</th>
            <th>Name</th>
            <th>SKU</th>
            <th>Item Description</th>
            <th>UPC</th>
            <th>Customer cost</th>
            <th>Customer Sell at price</th>
            <th>Restail MSRP</th>
            <th>Weight</th>
            <th>Weight Unit</th>
            <th>Length field</th>
            <th>Width</th>
            <th>Height</th>
            <th>Dimension Unit</th>
            <th>Picture</th>
        </tr>
      </thead>
      <tbody>
        <?php
            $i = 0;
            foreach($items as $item){ 
                // echo '<pre>';
                // var_dump($item);
                ?>
                <tr class="item">
                    <td data-img='<?php echo $item->image_url; ?>'>
                        <p class="plus_10" style="display: flex;">
                            <span class="minus">-</span>
                            <input disabled name="percent_d" value="10">%
                            <span class="plus">+</span>
                        </p>
                        <p class="update" style="display: flex;"><input type="checkbox" value="10" name="update<?php echo $i; ?>"><span>Update</span></p>
                        <p style="display: flex;"><a class="reset_lockaly" href='javascript:void(0)'>Reset</a></p>
                    </td>
                    <td class="qty"><?php echo $item->available; ?></td>
                    <td class="category"><?php echo $item->category; ?></td>
                    <td class="name"><?php echo $item->name; ?></td>
                    <td data-sku='<?php echo $item->sku; ?>'><?php echo $item->sku; ?></td>
                    <td class="description"><?php echo $item->item_description; ?></td>
                    <td class="sku"><?php echo $item->upc; ?></td>
                    <td><?php echo $item->customer_cost.' '.$item->wholesale_price.' '.$item->distro_price; ?></td>
                    <td data-price='<?php echo $item->sell_at_price; ?>'><?php echo $item->sell_at_price; ?></td>
                    <td><?php echo $item->msrp; ?></td>
                    <td><?php echo $item->weight; ?></td>
                    <td><?php echo $item->weight_unit; ?></td>
                    <td><?php echo $item->length_field; ?></td>
                    <td><?php echo $item->width; ?></td>
                    <td><?php echo $item->height; ?></td>
                    <td><?php echo $item->dimension_unit; ?></td>
                    <td><img src="<?php echo wp_get_attachment_image_url( $item->image_id,'zoho-img_size' ); ?>"></td>
                </tr>   
        <?php 
                $i++;
            } ?>
      </tbody>
      
    </table>
    <div class="pager tablesorter-pager">
        <img src="https://mottie.github.io/tablesorter/addons/pager/icons/first.png" class="first disabled" alt="First" tabindex="0" aria-disabled="true">
        <img src="https://mottie.github.io/tablesorter/addons/pager/icons/prev.png" class="prev disabled" alt="Prev" tabindex="0" aria-disabled="true">
        <!-- the "pagedisplay" can be any element, including an input -->
        <span class="pagedisplay" data-pager-output-filtered="{startRow:input} – {endRow} / {filteredRows} of {totalRows} total rows"><input type="text" class="ts-startRow" style="max-width:2em" value="1"> – 10 / 50 rows</span>
        <img src="https://mottie.github.io/tablesorter/addons/pager/icons/next.png" class="next" alt="Next" tabindex="0" aria-disabled="false">
        <img src="https://mottie.github.io/tablesorter/addons/pager/icons/last.png" class="last" alt="Last" tabindex="0" aria-disabled="false">
        <select class="pagesize" title="Select page size" aria-disabled="false">
            <option selected value="10">10</option>
            <option value="20">20</option>
            <option value="30">30</option>
            <option value="all">All Rows</option>
        </select>
        <span>Page-></span>
        <select style="width: 60px;" class="gotoPage" title="Select page number" aria-disabled="false"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select>
    </div>    
</div>
<script>
    (function( $ ) {
    $('body').on('click','#update_zoho',function(){
        console.log('test');
        var site_url = document.location.origin;
        $.ajax({
            type: 'POST',
            url: site_url + '/wp-admin/admin-ajax.php',
            data: {
                action: 'update_products_by_zoho'
            },
        error: function(error){
            alert('error');
            },
            dataType: "json",
            cache: false,
        beforeSend: function(){
                    $('body').append('<div class="backmodal"><div></div></div>');
                },
        success: function(data){
                $('.backmodal').remove();
                location.reload();
            } //endsuccess
        }); //endajax
    });
    $('body').on('click','#update_zoho_each',function(){
        let site_url = document.location.origin;
        let items = [];
        let i = 0;
        $( "#myTable tbody tr" ).each(function( index ) {
            let update = $(this).children('td').children('.update').children('input');
            let percent = $(this).children('td').children('.plus_10').children('input').val();
            let price = $(this).children('[data-price]').attr('data-price');
            let img = $(this).children('[data-img]').attr('data-img');
            let sku = $(this).children('[data-sku]').attr('data-sku');
            let qty = $(this).children('.qty').html();
            let category = $(this).children('.category').html();
            let name = $(this).children('.name').html();
            if ($(update).is(':checked')) {
                items[i] = {
                        "percent": percent,
                        "price": price,
                        "img": img,
                        "sku": sku,
                        "qty": qty,
                        "category": category,
                        "name": name    
                        };
            }
            i++;
        });
        let items_string = [];
        items.forEach(function(item){
            let string = item['category']+';'+item['img']+';'+item['name']+';'+item['percent']+';'+item['price']+';'+item['qty']+';'+item['sku'];
            items_string.push(string);
        });
        let string = items_string.join(",");

        $.ajax({
            type: 'POST',
            url: site_url + '/wp-admin/admin-ajax.php',
            data: {
                action: 'update_products_by_zoho_each',
                string:string
            },
        error: function(error){
            alert('error');
            },
            // dataType: "json",
            // cache: false,
        beforeSend: function(){
                    $('body').append('<div class="backmodal"><div></div></div>');
                },
        success: function(data){
            $('.backmodal').remove();
                // location.reload();
            } //endsuccess
        }); //endajax
    });
    $('body').on('click','.reset_lockaly',function(){
        $(this).closest('td').children('.plus_10').children('input').prop('checked',false);
        $(this).closest('td').children('.minus_10').children('input').prop('checked',false);
        $(this).closest('td').children('.update').children('input').prop('checked',false);
    });
    $('body').on('click','#reset_zoho',function(){
        $( "#myTable .item" ).each(function() {
            $(this).children('td').children('.plus_10').children('input').prop('checked',false);
            $(this).children('td').children('.minus_10').children('input').prop('checked',false);
            $(this).children('td').children('.update').children('input').prop('checked',false);
        });
    });
    $('body').on('click','.plus_10 span',function(){
        let value = parseInt($(this).closest('.plus_10').children('input').val());
        if($(this).hasClass('minus')){
            if(value > 1){
                value = value-1;
            }else{
                value = 1;
            }
        }else if($(this).hasClass('plus')){
            if(value < 9){

                value = value+1;
            }else{
                value = 10;
            }
        }
        $(this).closest('.plus_10').children('input').val(value);
    });
})( jQuery );
</script>