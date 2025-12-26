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
                'max_file_uploads' => 30,
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
                'name' => 'Ảnh Banner',
                'id'   => 'anh_banner',
                'type' => 'image_advanced',
                'max_file_uploads' => 1,
            ),

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

              // Đội ngũ
              array(
                'id' => $prefix . 'tieude_doingu',
                'name' => esc_html__('Đội ngũ Xhome', 'xhome'),
                'type'  => 'wysiwyg',
                'options' => array(
                    'textarea_rows' => 2,
                ),
            ),

             array(
                'name' => 'Ảnh đội ngũ',
                'id'   => 'anh_doingu',
                'type' => 'image_advanced',
                'max_file_uploads' => 12,
            ),

             // Giải thưởng
              array(
                'id' => $prefix . 'tieude_giaithuong',
                'name' => esc_html__('Tiêu đề mục giải thưởng', 'xhome'),
                'type'  => 'wysiwyg',
                'options' => array(
                    'textarea_rows' => 2,
                ),
            ),

               array(
                'id'          => $prefix . 'cards_giaithuong',
                'name'        => esc_html__('Danh sách giải thưởng', 'xhome'),
                'type'        => 'group',
                'clone'       => true,
                'sort_clone'  => true,
                'collapsible' => true,
                'group_title' => array('field' => 'card_title'),
                'fields'      => array(
                    array(
                        'id'   => 'card_stt',
                        'name' => esc_html__('Thứ tự giải thưởng', 'xhome'),
                        'type' => 'text',
                    ),
                    array(
                        'id'   => 'card_image',
                        'name' => esc_html__('Ảnh giải thưởng', 'xhome'),
                        'type' => 'image_advanced',
                    ),
                    array(
                        'id'   => 'card_title',
                        'name' => esc_html__('Tiêu đề giải thưởng', 'xhome'),
                        'type' => 'text',
                         'size' => 60
                    ),
                    array(
                        'id'   => 'card_desc',
                        'name' => esc_html__('Mô tả giải thưởng', 'xhome'),
                        'type' => 'textarea',
                        'rows' => 3,
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

               // Đối tác
              array(
                'id' => $prefix . 'tieude_doitac',
                'name' => esc_html__('Tiêu đề đối tác', 'xhome'),
                'type'  => 'wysiwyg',
                'options' => array(
                    'textarea_rows' => 2,
                ),
            ),

             array(
                'name' => 'Ảnh logo các đối tác',
                'id'   => 'logo_doitac',
                'type' => 'image_advanced',
                'max_file_uploads' => 12,
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


        // Công trình thực tế
    $meta_boxes[] = array(
        'title'      => esc_html__('Thông tin', 'xhome'),
        'post_types' => array('page'),
        'context'    => 'normal',
        'priority'   => 'high',
        'autosave'   => true,
        'include'    => array(
            'template' => 'page-congtrinh.php',
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

      // Dịch vụ
    $meta_boxes[] = array(
        'title'      => esc_html__('Thông tin', 'xhome'),
        'post_types' => array('page'),
        'context'    => 'normal',
        'priority'   => 'high',
        'autosave'   => true,
        'include'    => array(
            'template' => 'page-dichvu.php',
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
