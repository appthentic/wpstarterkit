<?php get_header();?>

<!-- main loop -->
<?php 
	if (have_posts()):
    while (have_posts()):
        the_post();?>
		<h1><?php echo ucfirst(get_the_title());?></h1>

		<article id="page-content">
			<?php the_content();?>
		</article>
	<?php
    endwhile;
endif;
?>

<?php get_footer();?>

