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
                'id' => $prefix . 'thanhtuu',
                'name' => esc_html__('Thành tựu', 'xhome'),
                'type'  => 'wysiwyg',
                'options' => array(
                    'textarea_rows' => 2,
                ),
            ),
        


            //Sứ mệnh - Tầm nhìn - Giá trị cốt lõi
            array(
                'id' => $prefix . 'noidung_tamnhin',
                'name' => esc_html__('Tầm nhìn', 'xhome'),
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

                array(
                'id' => $prefix . 'noidung_sumenh',
                'name' => esc_html__('Sứ mệnh', 'xhome'),
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

                array(
                'id' => $prefix . 'noidung_giatri',
                'name' => esc_html__('Giá trị cốt lõi', 'xhome'),
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

             // Tại sao nên chọn chúng tôi
               array(              
                'id' => $prefix . 'lydo',
                'name' => esc_html__( 'Tại sao nên chọn chúng tôi', 'xhome' ),
                'type' => 'group',
                'fields' => array( 
                    array(
                        'id' => $prefix . 'nd_lydo',
                        'name' => esc_html__( 'Nội dung lý do', 'xhome' ), 
                        'type'  => 'wysiwyg',   
                        'options' => array(
                            'textarea_rows' =>2,
                        ),             
                    ),
                ),
                'sort_clone' => true,
                'clone' => true,
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
