<?php 
	if(isset($_POST['url'])){
		update_option( 'zoho_url', $_POST['url'] );
	}
	if(isset($_POST['authorization'])){
		update_option( 'zoho_authorization', $_POST['authorization'] );
	}
	if(isset($_POST['organization'])){
		update_option( 'zoho_organization', $_POST['organization'] );
	}	
	if(isset($_POST['text_json'])){
		$text_json = fixed($_POST['text_json']);
		update_option( 'zoho_text_json', $text_json );
	}	
?>
<style>
    .container{
        padding: 0 5%;
    }
    form input{
    	margin-bottom: 10px;
    }
</style>
<div class="container">
	<h2>Settings</h2>
	<form style="display: flex;flex-direction: column;max-width: 400px;" action="/wp-admin/admin.php?page=zoho_int%2Fsettings.php" method="POST">
		<label>URL</label>
		<input type="text" required name="url" plaseholder="URL" value="<?php echo get_option('zoho_url');  ?>">
		<label>Token</label>
		<input type="text" required name="authorization" plaseholder="Authorization" value="<?php echo get_option('zoho_authorization');  ?>">
		<label>Organization</label>
		<input type="text" required name="organization" plaseholder="Organization" value="<?php echo get_option('zoho_organization');  ?>">
		<input type="submit" value="Save">
	</form>

	
</div>
<?php
// update_option('btb', '');
$opt = get_option('btb');
var_dump($opt);
// echo '<pre>';
// $url = get_option('zoho_url');
//     $token = get_option('zoho_authorization');
//     $organization = get_option('zoho_organization');
// $items = Zoho_int::get_items($url, $token, $organization);
// $i=0;
// foreach($items as $item){
// 	var_dump($item);
// 	$i++;
// }
// var_dump($i);
// addZohotoDb();

