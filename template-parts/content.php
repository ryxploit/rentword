<?php
/**
 * Template part for displaying content
 *
 * @package RentWord
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="entry-header">
        <?php
        if (is_singular()) :
            the_title('<h1 class="entry-title">', '</h1>');
        else :
            the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>');
        endif;

        if ('post' === get_post_type()) :
            ?>
            <div class="entry-meta">
                <span class="posted-on">
                    <?php echo get_the_date(); ?>
                </span>
                <span class="byline">
                    <?php echo esc_html__('by', 'rentword'); ?> <?php the_author(); ?>
                </span>
            </div>
            <?php
        endif;
        ?>
    </header>

    <?php if (has_post_thumbnail()) : ?>
        <div class="post-thumbnail">
            <?php the_post_thumbnail('large'); ?>
        </div>
    <?php endif; ?>

    <div class="entry-content">
        <?php
        if (is_singular()) :
            the_content();
        else :
            the_excerpt();
        endif;

        wp_link_pages(array(
            'before' => '<div class="page-links">' . esc_html__('Pages:', 'rentword'),
            'after'  => '</div>',
        ));
        ?>
    </div>

    <?php if (!is_singular()) : ?>
        <div class="entry-footer">
            <a href="<?php echo esc_url(get_permalink()); ?>" class="rw-btn rw-btn-primary">
                <?php echo esc_html__('Read More', 'rentword'); ?>
            </a>
        </div>
    <?php endif; ?>
</article>
