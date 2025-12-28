<?php
global $post;

if ( ! $post ) return;

$terms = wp_get_post_terms( $post->ID, 'project_cat', [ 'fields' => 'ids' ] );
if ( empty( $terms ) || is_wp_error( $terms ) ) return;

$args = [
    'post_type'           => 'project',
    'posts_per_page'      => 3, // mặc định 3 bài
    'post__not_in'        => [ $post->ID ],
    'ignore_sticky_posts' => true,
    'tax_query'           => [
        [
            'taxonomy' => 'project_cat',
            'field'    => 'term_id',
            'terms'    => $terms,
        ],
    ],
];

$related_query = new WP_Query( $args );

if ( $related_query->have_posts() ) :
    echo '<div class="page_congtrinh section"><div class="wrap">';
    echo '<h2 class="widgettitle">Dự án liên quan</h2>';
    echo '<div class="main-posts">';

    while ( $related_query->have_posts() ) :
        $related_query->the_post();

        echo '<div class="project">';

        if ( has_post_thumbnail() ) {
            echo '<div class="thumb">';
            echo '<a href="' . esc_url( get_permalink() ) . '">';
            the_post_thumbnail( 'large', [ 'class' => 'project-thumb' ] );
            echo '</a>';
            echo '</div>';
        }

        echo '<div class="list-info">';
       echo '<h3 class="title"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';

        $diachi = get_post_meta( get_the_ID(), 'diachi', true );
        if ( ! empty( $diachi ) ) {
            echo '<p class="project-diachi"><i class="fas fa-map-marker-alt"></i> ' . esc_html( $diachi ) . '</p>';
        }

        echo '</div>'; // .list-info
        echo '</div>'; // .project

    endwhile;

    echo '</div></div></div>'; // .main-posts .wrap .section

    wp_reset_postdata();
endif;
?>
