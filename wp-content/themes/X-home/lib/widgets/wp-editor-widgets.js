jQuery(document).ready(function ($) {

    /**
     * Removes all active editors. This is needed so they can be
     * re-initialized after a widget-updated event.
     */
    function custom_widgets_remove_editors() {
        custom_widgets_get_editors().forEach(function (id) {
            wp.editor.remove(id);
        });
    }

    /**
     * Searches the widgets areas for editors.
     *
     * @return array
     */
    function custom_widgets_get_editors() {
        let editors = [];

        // Doesn't look so great, but we need to target like this to support both the regular
        // widgets view and the Customizer. Also, we don't want to include the placeholder
        // widget that is in the DOM on the widgets.php page.
        $(document).find("#customize-theme-controls .custom-widget-wp-editor, #widgets-right .custom-widget-wp-editor").each(function () {
            editors.push($(this).attr('id'));
        });

        return editors;
    }

    /**
     *  Initializes all wp.editor instances.
     */
    function custom_widgets_set_editors() {
        custom_widgets_get_editors().forEach(function (id) {
            wp.editor.initialize($('#' + id).attr('id'), {
                tinymce: {
                    wpautop: true,
                    setup: function (editor) {
                        editor.on('dirty', function (e) {
                            editor.save();
                            $('#' + id).change();
                        }); 
                        // fix for missing dirty event when editor content is fully deleted    
                        editor.on('keyup', function (e) {
                            if(editor.getContent() === '') {
                                editor.save();
                                $('#' + id).change();
                            }
                        });
                    }
                },
                quicktags: true,
                mediaButtons: false
            });
        });

        // To prevent the editor from not submitting the value, we click the switch html tab.
        $(document).contents().find('.widget-control-save').off().on('click', function (e) {
            custom_widgets_get_editors().forEach(function (editor) {
                let form = $('#' + editor).closest('form');
                form.find('.switch-html').click();
            });
        });
    }

    // Trigger removal and setting of editors again after update or added.
    $(document).on('widget-updated widget-added', function () {
        custom_widgets_remove_editors();
        custom_widgets_set_editors();
    });

    // Init.
    custom_widgets_set_editors();
});