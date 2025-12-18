<?php

add_filter('rwmb_meta_boxes', 'prefix_register_taxonomy_meta_boxes');
function prefix_register_taxonomy_meta_boxes($meta_boxes)
{
    $prefix = '';
    //Sản phẩm
    $meta_boxes[] = array(
        'title'      => esc_html__('Thông tin', 'xhome'),
        'post_types' => array('product',),
        'context'    => 'normal',
        'priority'   => 'high',
        'autosave'   => true,
        'fields'     => array(

            array(
                'name' => 'Ảnh sản phẩm',
                'id'   => 'anhsp',
                'type' => 'image_advanced',
                'max_file_uploads' => 6,
            ),
        ),
    );

    //Công trình thực tế
        $meta_boxes[] = array(
        'title'      => esc_html__('Thông tin', 'xhome'),
        'post_types' => array('project',),
        'context'    => 'normal',
        'priority'   => 'high',
        'autosave'   => true,
        'fields'     => array(

            array(
                'name' => 'Ảnh công trình',
                'id'   => 'anh_project',
                'type' => 'image_advanced',
                'max_file_uploads' => 10,
            ),

            array(
                'name' => 'Địa chỉ công trình',
                'id' => 'diachi',
                'type' => 'text',
                'size' => 70,
            ),
        ),
    );



    // liên hệ
    $meta_boxes[] = array(
        'title'      => esc_html__('Thông tin', 'xhome'),
        'post_types' => array('page',),
        'context'    => 'normal',
        'priority'   => 'high',
        'autosave'   => true,
        'include'   => array(
            'template'  => 'page-lienhe.php',
        ),
        'fields'     => array(
            array(
                'id' => $prefix . 'nd',
                'name' => esc_html__('Nội dung liên hệ', 'xhome'),
                'type'  => 'wysiwyg',
                'options' => array(
                    'textarea_rows' => 2,
                ),
            ),

            array(
                'name' => 'Form',
                'id' => 'form',
                'type' => 'text',
                'size' => 70,
            ),


            array(
                'id' => 'map',
                'name' => esc_html__('Map', 'xhome'),
                'type'  => 'textarea',
                'sanitize_callback' => 'none',
            ),
        ),
    );


    //Giới thiệu
    $meta_boxes[] = array(
        'title'      => esc_html__('Thông tin', 'xhome'),
        'post_types' => array('page'),
        'context'    => 'normal',
        'priority'   => 'high',
        'autosave'   => true,
        'include'    => array(
            'template' => 'page-gioithieu.php',
        ),
        'fields'     => array(
            array(
                'name' => 'Ảnh Banner',
                'id'   => 'anh_banner',
                'type' => 'image_advanced',
                'max_file_uploads' => 1,
            ),

            //Tại sao chọn chúng tôi
            array(
                'id' => $prefix . 'tieude_taisao',
                'name' => esc_html__('Tiêu đề tại sao chọn chúng tôi', 'xhome'),
                'type'  => 'wysiwyg',
                'options' => array(
                    'textarea_rows' => 2,
                ),
            ),
            array(
                'id'          => $prefix . 'cards_taisao',
                'name'        => esc_html__('Danh sách Card lý do', 'xhome'),
                'type'        => 'group',
                'clone'       => true,
                'sort_clone'  => true,
                'collapsible' => true,
                'group_title' => array('field' => 'card_title'),
                'fields'      => array(
                    array(
                        'id'   => 'card_image',
                        'name' => esc_html__('Icon card', 'xhome'),
                        'type' => 'image_advanced',
                    ),
                    array(
                        'id'   => 'card_title',
                        'name' => esc_html__('Tiêu đề', 'xhome'),
                        'type' => 'text',
                    ),
                    array(
                        'id'   => 'card_desc',
                        'name' => esc_html__('Mô tả', 'xhome'),
                        'type' => 'textarea',
                        'rows' => 3,
                    ),
                ),
            ),


            //Đồng hành
            array(
                'id' => $prefix . 'tieude_donghanh',
                'name' => esc_html__('Tiêu đề đồng hành', 'xhome'),
                'type'  => 'wysiwyg',
                'options' => array(
                    'textarea_rows' => 2,
                ),
            ),

            array(
                'id' => $prefix . 'noidung_donghanh',
                'name' => esc_html__('Nội dung đồng hành', 'xhome'),
                'type' => 'group',
                'fields' => array(
                    array(
                        'id' => $prefix . 'image',
                        'name' => esc_html__('Ảnh', 'xhome'),
                        'type'  => 'wysiwyg',
                        'options' => array(
                            'textarea_rows' => 2,
                        ),
                    ),

                    array(
                        'id' => $prefix . 'text',
                        'name' => esc_html__('Văn bản', 'xhome'),
                        'type'  => 'wysiwyg',
                        'options' => array(
                            'textarea_rows' => 2,
                        ),
                    ),


                ),
            ),



//Đối tác
            array(
                'id' => $prefix . 'tieude_doitac',
                'name' => esc_html__('Tiêu đề đối tác', 'xhome'),
                'type'  => 'wysiwyg',
                'options' => array(
                    'textarea_rows' => 2,
                ),
            ),
            array(
                'id'          => $prefix . 'cards_group',
                'name'        => esc_html__('Danh sách Card đối tác', 'xhome'),
                'type'        => 'group',
                'clone'       => true,
                'sort_clone'  => true,
                'collapsible' => true,
                'group_title' => array('field' => 'card_title'),
                'fields'      => array(
                    array(
                        'id'   => 'card_image',
                        'name' => esc_html__('Ảnh card', 'xhome'),
                        'type' => 'image_advanced',
                    ),
                    array(
                        'id'   => 'card_title',
                        'name' => esc_html__('Tiêu đề', 'xhome'),
                        'type' => 'text',
                    ),
                    array(
                        'id'   => 'card_desc',
                        'name' => esc_html__('Mô tả', 'xhome'),
                        'type' => 'textarea',
                        'rows' => 3,
                    ),
                ),
            ),

            //Giá trị cốt lỗi
            array(
                'id' => $prefix . 'tieude_giatri',
                'name' => esc_html__('Tiêu đề giá trị', 'xhome'),
                'type'  => 'wysiwyg',
                'options' => array(
                    'textarea_rows' => 2,
                ),
            ),
            array(
                'id'          => $prefix . 'cards_giatri',
                'name'        => esc_html__('Danh sách Card giá trị', 'xhome'),
                'type'        => 'group',
                'clone'       => true,
                'sort_clone'  => true,
                'collapsible' => true,
                'group_title' => array('field' => 'card_title'),
                'fields'      => array(
                    array(
                        'id'   => 'card_image',
                        'name' => esc_html__('Icon card', 'xhome'),
                        'type' => 'image_advanced',
                    ),
                    array(
                        'id'   => 'card_title',
                        'name' => esc_html__('Tiêu đề', 'xhome'),
                        'type' => 'text',
                    ),
                    array(
                        'id'   => 'card_desc',
                        'name' => esc_html__('Mô tả', 'xhome'),
                        'type' => 'textarea',
                        'rows' => 3,
                    ),
                ),
            ),


        ),

    );


    //BLOG
    $meta_boxes[] = array(
        'title'      => esc_html__('Thông tin', 'xhome'),
        'post_types' => array('page'),
        'context'    => 'normal',
        'priority'   => 'high',
        'autosave'   => true,
        'include'    => array(
            'template' => 'page-blog.php',
        ),
        'fields'     => array(
            array(
                'name' => 'Ảnh Banner',
                'id'   => 'anh_banner',
                'type' => 'image_advanced',
                'max_file_uploads' => 1,
            ),

        ),
    );


  

    return $meta_boxes;
}
