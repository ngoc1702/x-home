(function() {
    tinymce.PluginManager.add('button_ads', function( editor, url ) {
        editor.addButton( 'button_ads', {
            icon: 'dashicons dashicons-carrot',
            type: 'menubutton',
            title: editor.getLang('button_ads.decorations_shortcodes_title'),
            menu: [
				{
					text: editor.getLang('button_ads.warning_title'),
					icon: 'dashicons dashicons-lightbulb dashicons-red',
					onclick: function() {
						editor.windowManager.open( {
							title: editor.getLang('button_ads.warning_title_open_windows'),
							body: [
								{
									type: 'label',
									text: editor.getLang('button_ads.label_windows_add_text'),
								},
								{
									type: 'textbox',
									name: 'warning',
									label: '',
									value: tinyMCE.activeEditor.selection.getContent(),
									multiline: true,
									minWidth: 300,
									minHeight: 100
								}
							],
							onsubmit: function( e ) {
								editor.insertContent( '[tds_warning]' + e.data.warning + '[/tds_warning]');
							}
						});
					},
				},
				{
					text: editor.getLang('button_ads.advice_title'),
					icon: 'dashicons dashicons-format-status dashicons-blue',
					onclick: function() {
						editor.windowManager.open( {
							title: editor.getLang('button_ads.advice_title_open_windows'),
							body: [
								{
									type: 'label',
									text: editor.getLang('button_ads.label_windows_add_text'),
								},
								{
									type: 'textbox',
									name: 'council',
									label: '',
									value: tinyMCE.activeEditor.selection.getContent(),
									multiline: true,
									minWidth: 300,
									minHeight: 100
								}
							],
							onsubmit: function( e ) {
								editor.insertContent( '[tds_council]' + e.data.council + '[/tds_council]');
							}
						});
					}

				},
				{
					text: editor.getLang('button_ads.note_title'),
					icon: 'dashicons dashicons-admin-post dashicons-green',
					onclick: function() {
						editor.windowManager.open( {
							title: editor.getLang('button_ads.note_title_open_windows'),
							body: [
								{
									type: 'label',
									text: editor.getLang('button_ads.label_windows_add_text'),
								},
								{
									type: 'textbox',
									name: 'note',
									label: '',
									value: tinyMCE.activeEditor.selection.getContent(),
									multiline: true,
									minWidth: 300,
									minHeight: 100
								}
							],
							onsubmit: function( e ) {
								editor.insertContent( '[tds_note]' + e.data.note + '[/tds_note]');
							}
						});
					}
				},
				{
					text: editor.getLang('button_ads.info_title'),
					icon: 'dashicons dashicons-info dashicons-yellow',
					onclick: function() {
						editor.windowManager.open( {
							title: editor.getLang('button_ads.info_title_open_windows'),
							body: [
								{
									type: 'label',
									text: editor.getLang('button_ads.label_windows_add_text'),
								},
								{
									type: 'textbox',
									name: 'info',
									label: '',
									value: tinyMCE.activeEditor.selection.getContent(),
									multiline: true,
									minWidth: 300,
									minHeight: 100
								}
							],
							onsubmit: function( e ) {
								editor.insertContent( '[tds_info]' + e.data.info + '[/tds_info]');
							}
						});
					}
				},
				{
					text: editor.getLang('button_ads.custom_title'),
					icon: 'dashicons dashicons-screenoptions',
					onclick: function() {
						editor.windowManager.open( {
							title: editor.getLang('button_ads.custom_title_open_windows'),
							body: [
								{
									type: 'label',
									text: editor.getLang('button_ads.title_windows_add_text'),
								},
								{
									type: 'textbox',
									name: 'title',
									value: ''
								},
								{
									type: 'label',
									text: editor.getLang('button_ads.label_windows_add_text'),
								},
								{
									type: 'textbox',
									name: 'custom_text',
									label: '',
									value: tinyMCE.activeEditor.selection.getContent(),
									multiline: true,
									minWidth: 300,
									minHeight: 100
								},
								{
									type: 'colorbox',
									name: 'color_border',
									label: editor.getLang('button_ads.colorborder_text'),
									value: '#e87e04',
									onaction: createColorPickAction()

								}
							],
							onsubmit: function( e ) {
								editor.insertContent( '[ads_custom_box title="' + e.data.title + '" color_border="' + e.data.color_border + '"]' + e.data.custom_text + '[/ads_custom_box]');
							}
						});
					}
				},
                {
                    text: editor.getLang('button_ads.colorbox_title'),
                    icon: 'dashicons dashicons-format-aside dashicons-box',
                    onclick: function() {
                        editor.windowManager.open( {
                            title: editor.getLang('button_ads.colorbox_title_open_window'),
                            Width: 500,
                            autoScroll: true,
                            body: [
                                {
                                    type: 'container',
                                    layout: 'grid',
                                    columns: 2,
                                    items: [
                                        {
                                            type: 'container',
                                            layout: 'stack',
                                            items: [
                                                {
                                                    type: 'label',
                                                    text: editor.getLang('button_ads.colorbox_background'),
                                                },
                                                {
                                                    type: 'colorbox',
                                                    name: 'color_background',
                                                    value: '#eee',
                                                    onaction: createColorPickAction()
                                                },
                                            ]
                                        },
                                        {
                                            type: 'container',
                                            layout: 'stack',
                                            items: [
                                                {
                                                    type: 'label',
                                                    text: editor.getLang('button_ads.colorbox_text'),
                                                    style:''
                                                },
                                                {
                                                    type: 'colorbox',
                                                    name: 'color_text',
                                                    value: '#444',
                                                    onaction: createColorPickAction()

                                                },
                                            ]
                                        },
                                    ]
                                },
                                {
                                    type: 'label',
                                    text: editor.getLang('button_ads.label_windows_add_text'),
                                },
                                {
                                    type: 'textbox',
                                    name: 'color_box',
                                    multiline: true,
                                    value: tinyMCE.activeEditor.selection.getContent(),
                                }
                            ],
                            onsubmit: function( e ) {
                                editor.insertContent( '[ads_color_box color_background="' + e.data.color_background + '" color_text="' + e.data.color_text + '"]' + e.data.color_box + '[/ads_color_box]');
                            }
                        });
                    }

                },


            ]

        });
        function createColorPickAction() {
            var colorPickerCallback = editor.settings.color_picker_callback;

            if (colorPickerCallback) {
                return function() {
                    var self = this;

                    colorPickerCallback.call(
                        editor,
                        function(value) {
                            self.value(value).fire('change');
                        },
                        self.value()
                    );
                };
            }
        }

    });
})();
