<?php
/**
 * Template Name: Create Product
 */
 
get_header(); ?>
 
<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<?php the_title('<h1>', '</h1>'); ?>

		<form action="" method="POSt" enctype="multipart/form-data" id="product-form">
			<p class="form-row">
				<label for="product-name">Product Name</label>
				<input type="text" name="product_name" value="" id="product-name" required="">
			</p>

			<p class="form-row">
				<label for="product-price">Product price</label>
				<input type="number" min="0" name="product_price" value="" id="product-price" required="">
			</p>

			<p class="form-row">
				<label for="product-date">Date</label>
				<input type="date" name="product_date" value="" id="product-date">
			</p>

			<p class="form-row">
				<label for="product-date">Type</label>
				<select name="product_type" id="product-type">
					<option value="rare" selected="true">Rare</option>
					<option value="frequent">Frequent</option>
					<option value="unusual">Unusual</option>
				</select>
			</p>

			<p class="form-row">
				<label for="product-picture">Image</label>
				<input type="file" name="product_picture" id="product-picture">
			</p>

			<p class="form-row">
				<input type="hidden" name="action" value="custom_product_create">
				<button type="submit">Save product</button>
			</p>
		</form>

		<script>
			var $form = document.getElementById('product-form');

			$form.addEventListener('submit', function(event) {
				event.preventDefault();
				var data = new FormData( this );

				fetch('/wp-admin/admin-ajax.php', {
					method: 'POST',
					body: data
				})
					.then( function(response) {
						return response.json();
					})
					.then( function(response) {						
						var alert = document.createElement('div');
						alert.innerHTML = response.data.message;

						$form.reset();
						$form.appendChild( alert );
					})
			})
		</script>

	</main><!-- #main -->
</div><!-- #primary -->
 
<?php
//get_sidebar();
get_footer();