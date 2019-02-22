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

    // Meta og:locale
    echo $this->Html->meta(
        'og:locale',
        'en_US'
    );

    // Meta og:title
    echo $this->Html->meta(
        'og:title',
        $this->element('head_title')
    );

    // Meta og:type
    echo $this->Html->meta(
        'og:type',
        $metaOgType
    );

    // Meta og:image
    echo $this->Html->meta(
        'og:image',
        $metaImage
    );

    // Meta og:video
    echo $this->Html->meta(
        'og:video',
        ''
    );

    // Meta og:url
    echo $this->Html->meta(
        'og:url',
        $this->Url->build($this->request->getRequestTarget(), true)
    );

    // Meta og:description
    echo $this->Html->meta(
        'og:description',
        $metaDescription
    );

    // Meta og:site_name
    echo $this->Html->meta(
        'og:site_name',
        $siteName
    );
?>