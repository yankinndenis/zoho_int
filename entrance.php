<?php
$url = get_option('zoho_url');
$token = get_option('zoho_authorization');
$organization = get_option('zoho_organization');
// $items = Zoho_int::get_items($url, $token, $organization);
$items = getZohoFromDb();
?>
<style>
    .container{
        padding: 0 5%;
    }
</style>
<div class="container">
    <p><button style="margin-right:10px;" id="update_zoho">Update</button><button id="update_zoho_each">Update selected</button></p>
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
        </tr>
      </thead>
      <tbody>
        <?php
            $i = 0;
            foreach($items as $item){ 
                // echo '<pre>';
                ?>
                <tr>
                    <td>
                        <p style="display: flex;"><input type="radio" value="10" name="percent<?php echo $i; ?>"><span style="min-width: 40px;"> +10%</span></p>
                        <p style="display: flex;"><input type="radio" value="10" name="percent<?php echo $i; ?>"><span style="min-width: 40px;"> -10%</span></p>
                        <p style="display: flex;"><input type="checkbox" value="10" name="update<?php echo $i; ?>"><span>Update</span></p>
                    </td>
                    <td><?php echo $item->available; ?></td>
                    <td><?php echo $item->category; ?></td>
                    <td><?php echo $item->name; ?></td>
                    <td><?php echo $item->sku; ?></td>
                    <td><?php echo $item->item_description; ?></td>
                    <td><?php echo $item->upc; ?></td>
                    <td><?php echo $item->wholesale_price.' '.$item->distro_price; ?></td>
                    <td><?php echo $item->sell_at_price; ?></td>
                    <td><?php echo $item->msrp; ?></td>
                    <td><?php echo $item->weight; ?></td>
                    <td><?php echo $item->weight_unit; ?></td>
                    <td><?php echo $item->length_field; ?></td>
                    <td><?php echo $item->width; ?></td>
                    <td><?php echo $item->height; ?></td>
                    <td><?php echo $item->dimension_unit; ?></td>
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
        var site_url = document.location.origin;
        $.ajax({
            type: 'POST',
            url: site_url + '/wp-admin/admin-ajax.php',
            data: {
                action: 'update_products_by_zoho_each'
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

})( jQuery );
</script>