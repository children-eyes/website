<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */




$children = new Child_Case($post);

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<table>
<tr>
<td style="width:360px;">
	<header class="case-header entry-header">

	<?php echo '<img src="'.$children->image().'" alt="Kinderaugen"/>';  ?><img class='mask' src='wp-content/themes/childreneyes/images/mask.png' alt='maske'/>
		<div class="entry-meta">
			<?php
				edit_post_link( __( 'Edit', 'childreneyes' ), '<span class="edit-link">', '</span>' );
			?>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->
</td>
<td>
	<div class="case-content">

		<?php

		  echo '<h1 class="entry-title">'.$children->name.'</h1>' ;

			echo 'Kosten bis zum '.$children->date.': '.$children->costs_euro.'€ <br/>';
			echo 'vermisst seit: '.$children->missed_days.' Tagen <br/>';
			echo 'Kommentar: <i>'.$children->comment.'</i>';

			wp_link_pages( array(
				'before'      => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'childreneyes' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			) );
		?>

	</div>


	<?php the_tags( '<footer class="entry-meta"><span class="tag-links">', '', '</span></footer>' ); ?>
</td>
</tr>
</table>
</article><!-- #post-## -->
