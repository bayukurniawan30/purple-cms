<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Filesystem\File;

class ImageComponent extends Component
{
    public function convertToBase64($image)
    {
        $fullSizeImage              = WWW_ROOT . 'uploads' . DS .'images' . DS .'original' . DS;
        $uploadedThumbnailSquare    = WWW_ROOT . 'uploads' . DS .'images' . DS .'thumbnails' . DS . '300x300' . DS;
        $uploadedThumbnailLandscape = WWW_ROOT . 'uploads' . DS .'images' . DS .'thumbnails' . DS . '480x270' . DS;

        $fullSizeImageFile    = new File($fullSizeImage . $image);
        $fullSizeImageFileExt = $fullSizeImageFile->ext();

        $uploadedThumbnailSquareFile    = new File($uploadedThumbnailSquare . $image);
        $uploadedThumbnailSquareFileExt = $uploadedThumbnailSquareFile->ext();

        $uploadedThumbnailLandscapeFile    = new File($uploadedThumbnailLandscape . $image);
        $uploadedThumbnailLandscapeFileExt = $uploadedThumbnailLandscapeFile->ext();

        $dataFullSize   = file_get_contents($fullSizeImage . $image);
        $base64FullSize = 'data:image/' . $fullSizeImageFileExt . ';base64,' . base64_encode($dataFullSize);

        $dataThumbnailSquare   = file_get_contents($uploadedThumbnailSquare . $image);
        $base64ThumbnailSquare = 'data:image/' . $uploadedThumbnailSquareFileExt . ';base64,' . base64_encode($dataThumbnailSquare);

        $dataThumbnailLandscape   = file_get_contents($uploadedThumbnailLandscape . $image);
        $base64ThumbnailLandscape = 'data:image/' . $uploadedThumbnailLandscapeFileExt . ';base64,' . base64_encode($dataThumbnailLandscape);

        $result = [
            'full_path' => $base64FullSize,
            'thumbnail' => [
                '300x300' => $base64ThumbnailSquare,
                '480x270' => $base64ThumbnailLandscape
            ]
        ];

        return $result;
    }
}