<?php
    if ($logo == '') {
        if ($metaImage != '') {
            $metaImage = $this->request->host().$this->request->getAttribute("webroot").'uploads/images/original/' . $metaImage;
        }
        else {
            $metaImage = '';
        }
    }
    else {
        $metaImage = $this->request->host().$this->request->getAttribute("webroot").'uploads/images/original/' . $logo;
    }

    // Meta twitter:card
    echo $this->Html->meta(
        'twitter:card',
        'summary'
    );

    // Meta twitter:url
    echo $this->Html->meta(
        'twitter:url',
        $this->Url->build($this->request->getRequestTarget(), true)
    );

    // Meta twitter:title
    echo $this->Html->meta(
        'twitter:title',
        $this->element('head_title')
    );

    // Meta twitter:description
    echo $this->Html->meta(
        'twitter:description',
        $metaDescription
    );

    // Meta twitter:image
    echo $this->Html->meta(
        'twitter:image',
        $metaImage
    );
?>