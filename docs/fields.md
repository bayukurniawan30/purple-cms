# Headless CMS - Fields

Fields is a set of data type to create a schema in collections and singletons.

## Color Picker

Provides a color chooser field

Option: `NULL`

## Date Picker

Provides a date chooser field

Option: 
```
{
    'data-date-format' => 'yyyy-mm-dd'
}
```

## HTML Editor

Provides a HTML Editor (Powered by Froala Editor)

Option: `NULL`

## Link

Provides a URL input type

Option: 
```
{
    'open' => '_self,_blank'
}
```

## Markdown Editor

Provides a Markdown editor

Option: `NULL`

## Number Input

Provides an input with number type

Option:
```
{
    'placeholder' => '', 
    'min' => 0, 
    'max' => '', 
    'step' => 1
}
```

## Password

Provides an input with password type

Option:
```
{
    'minlength' => '', 
    'maxlength' => ''
}
```

## Repeater

Provides an input to create radio button

Option:
```
{
    'fields' => [
        0 => [
            'value' => '',
            'text'  => ''
        ],
        ... (add more here to create multiple choices)
    ]
}
```

## Selectbox

Provides a selectbox input

Option:
```
{
    'fields' => [
        0 => [
            'value' => '',
            'text'  => ''
        ],
        ... (add more here to create multiple choices)
    ]
}
```

## Select Multiple Images

Provides an images selector

Option:
```
{
    'min' => '',
    'max' => ''
}
```

## Tags

Provides a tag input type

Option:
```
{
    'max' => 5 (default value is 5, change according to your needs)
}
```

## Textarea

Provides a textarea

Option:
```
{
    'placeholder' => '',
    'minlength' => '',
    'maxlength' => '',
    'cols' => '',
    'rows' => ''
}
```

## Text Input

Provides an input text type

Option:
```
{
    'placeholder' => '',
    'minlength' => '',
    'maxlength' => '',
    'size' => '',
    'pattern' => ''
}
```

## Time Picker

Provides a time chooser field

Option:
```
{
    'data-minute-tep' => '1',
    'data-show-seconds' => false,
    'data-show-meridian' => true
}
```

## True or False

Provides a switch

Option: `NULL`

## Upload or choose existing media image

Provides an input to upload or choose image from existing media

Option:
```
{
    'allowedFormat' => 'jpeg,jpg,png' (format separated with comma)
}
```