<?php if ( ! defined( 'ABSPATH' ) ) exit;
	/**
		* Extansion version
		*
		* @package    Lead_Generation
		* @subpackage  
		* @copyright   Copyright (c) 2018, Dmytro Lobov
		* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
		* @since       1.0
	*/
?>

<style>
	.feature-section.one-col p {
	font-size: 16px;
	}
	.lg-thank-you {
	color: #777;
	}
	.itembox {
	
	} 
	.faq-title{		
	cursor: pointer;				
	}	
	.faq-title:before{
	content: "\f132";
	font-family: Dashicons;
	vertical-align: bottom;
	margin-right: 8px;
	color: #e95645
	
	}	
	.toggleshow:before{
	content: "\f132";
	font-family: Dashicons;	
	color: #e95645
	}	
	.togglehide:before{
	content: "\f460";	
	font-family: Dashicons;
	}
	.item-title {
	margin: 1.25em 0 .6em;
	font-size: 1em;
	line-height: 1;
	color: #1e73be;
	}
	.items .inside {
	margin: 10px 10px 10px 20px;
	}
	.feature-section ul {
		margin-left: 10px;
	}
	.feature-section ul li:before {
		content: "\f147";
		font-family: Dashicons;
		margin-right: 5px;
		color: #e95645
	}
	.lg-btn {
	width: 50%;	
	display: inline-block;	
	height: 42px;
	background: #e95645;
	border-radius: 3px;
	line-height: 42px;
	text-align: center;
	color: #fff !important;
	text-decoration: none;
	font-size: 18px;
	font-weight: 500;
	cursor: pointer;
	border:none;	
}
.lg-btn:hover {
	background: #d45041;
}
	
</style>

<script>
	jQuery(document).ready(function($) {		
		$('.item-title').children('.faq-title').click(function(){
			var par = $(this).closest('.items');
			$(par).children(".inside").toggle(500);
			if($(this).hasClass('togglehide')){
				$(this).removeClass('togglehide');
				$(this).addClass( "toggleshow" );
				$(this).attr('title', 'Show');
			}
			else {
				$(this).removeClass('toggleshow');
				$(this).addClass( "togglehide" );
				$(this).attr('title', 'Hide');
			}			
		});		
	})
</script>
<div class="about-wrap">
	<div class="feature-section one-col">
		<div class="col">
			
			<p>GET MORE FEATURES WITH THE PLUGIN EXTENSION.</p> 
			
			<p><center><a href="<?php echo $this->pro_url; ?>" target="_blank" class="lg-btn">Get Pro Version</a></center></p>		
			
			<p>ADDITIONAL OPTIONS IN PRO VERSION:</p>
			
			<div class="items itembox">	
				<div class="item-title">
					<span class="faq-title">User Target</span>		
				</div>			
				<div class="inside" style="display: none;">
					<p>You can customize display the item on the page depending on the role of the user who is on the site. You can configure targeting for such user groups:</p>
					<ul>
						<li>All users;</li>
						<li>Unauthorized users;</li>
						<li>Authorized users;</li>
						<li>The role of the authorized user on the site;</li>					
					<ul>					
				</div>
			</div>
			
			<div class="items itembox">	
				<div class="item-title">
					<span class="faq-title">Multi language</span>		
				</div>			
				<div class="inside" style="display: none;">
					<p>The condition for display the item depending on the language of the site.</p>
					<p>It is good to use if you have a website in several languages and you need to show different elements for a different language.</p>				
				</div>
			</div>
			
			<div class="items itembox">	
				<div class="item-title">
					<span class="faq-title">Target to content</span>		
				</div>			
				<div class="inside" style="display: none;">
					<p>Choose a condition to target your item to specific content or various other segments. You can display the item on:</p>
					<ul>
						<li>All posts and pages;</li>
						<li>Only posts;</li>
						<li>Only pages;</li>
						<li>Posts with certain IDs;</li>	
						<li>Pages with certain IDs;</li>
						<li>Posts in Categorys with IDs;</li>
						<li>All posts, except;</li>
						<li>All pages, except;</li>
						<li>Taxonomy;</li>
					<ul>
				</div>
			</div> 
		</div>
		
	</div>
</div>