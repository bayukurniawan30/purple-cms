<?php

namespace App\Purple;

class PurpleProjectComponents
{
    public function fieldTypes() 
    {
        $fieldTypes = [
            'boolean'     => [
                'name'    => '',
                'text'    => 'True or false',
                'options' => NULL 
            ],
            'colorpicker' => [
                'name'    => '',
                'text' => 'Color picker',
                'options' => NULL
            ],
            'date'        => [
                'name'    => '',
                'text' => 'Date picker',
                'options' => [
                    'data-date-format' => 'yyyy-mm-dd'
                ]
            ],
            'gallery'     => [
                'name'    => '',
                'text' => 'Select multiple images',
                'options' => [
                    'min' => '', 'max' => ''
                ]
            ],
            'html'        => [
                'name'    => '',
                'text' => 'HTML editor',
                'options' => NULL
            ],
            'image'       => [
                'name'    => '',
                'text' => 'Upload or choose existing media image',
                'options' => [
                    'allowedFormat' => 'jpeg,jpg,png' // format separated with comma
                ]
            ],
            'link'       => [
                'name'    => '',
                'text' => 'Link',
                'options' => [
                    'open' => '_self,_blank'
                ]
            ],
            // 'media'       => [
            //     'name'    => '',
            //     'text' => 'Media (Link to Media Document, Image, or Video)',
            //     'options' => [
            //         'type' => 'image' // doc, image, video
            //     ]
            // ],
            'markdown'    => [
                'name'    => '',
                'text' => 'Markdown editor',
                'options' => NULL
            ],
            'password'    => [
                'name'    => '',
                'text' => 'Password',
                'options' => [
                    'minlength' => '', 'maxlength' => ''
                ]
            ],
            'repeater'    => [
                'name'    => '',
                'text' => 'Repeater (To create radio button)',
                'options' => [
                    'fields' => [
                        0 => [
                            'value' => '',
                            'text'  => ''
                        ] 
                    ]
                ]
                        ],
            'selectbox'   => [
                'name'    => '',
                'text' => 'Selectbox',
                'options' => [
                    'fields' => [
                        0 => [
                            'value' => '',
                            'label' => ''
                        ] 
                    ]
                ]
            ],
            'tags'        => [
                'name'    => '',
                'text' => 'Tags',
                'options' => [
                    'max' => 5
                ]
            ],
            'text'        => [
                'name'    => '',
                'text' => 'Text input',
                'options' => [
                    'placeholder' => '', 'minlength' => '', 'maxlength' => '', 'size' => '', 'pattern' => ''
                ]
            ],
            'textarea'    => [
                'name'    => '',
                'text' => 'Textarea',
                'options' => [
                    'placeholder' => '', 'minlength' => '', 'maxlength' => '', 'cols' => '', 'rows' => ''
                ]
            ],
            'time'        => [
                'name'    => '',
                'text' => 'Time picker',
                'options' => [
                    'data-minute-tep' => '1', 'data-show-seconds' => false, 'data-show-meridian' => true
                ]
            ],
            // 'upload_file' => [
            //     'name'    => '',
            //     'text' => 'Upload file',
            //     'options' => [
            //         'allowedFormat' => '*' // format separated with comma
            //     ]
            // ]
        ];

        return $fieldTypes;
    }
}