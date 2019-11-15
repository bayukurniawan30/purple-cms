<?php
namespace App\View\Cell;
use Cake\View\Cell;
use Cake\Routing\Router;
use League\ColorExtractor\Color;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;
use Aws\S3\S3Client;  
use Aws\Exception\AwsException;

class MediasCell extends Cell
{
    public function previousId($id)
    {
        $this->loadModel('Medias');
        $query = $this->Medias->find('previousRow', ['id' => $id]);
        if ($query == false) {
            $this->set('id', '0');
        }
        else {
            $this->set('id', $query->id);
        }
    }
    public function nextId($id)
    {
        $this->loadModel('Medias');
        $query = $this->Medias->find('nextRow', ['id' => $id]);
        if ($query == false) {
            $this->set('id', '0');
        }
        else {
            $this->set('id', $query->id);
        }
    }   
    public function colorExtract($image)
    {
        $palette    = Palette::fromFilename($image);
        $extractor  = new ColorExtractor($palette);
        $colorCount = count($palette);
        if ($colorCount >= 5) {
            $colors = $extractor->extract(5);
            // $colors = $palette->getMostUsedColors(8);
        }
        else {
            $colors = $extractor->extract($colorCount);
            // $colors = $palette->getMostUsedColors($colorCount);
        }

        $setColors = [];
        foreach($colors as $color) {
            array_push($setColors, Color::fromIntToHex($color));
        }

        $this->set('colors', implode(',', $setColors));
    }
    /**
     * @param $mediaName = media file name
     * @param $mediaType = ['image', 'document', 'video']
     * @param $return = default 'original', ['original', 'thumbnail::300', 'thumbnail::480']
     */
    public function mediaPath($mediaName, $mediaType, $return = 'original')
    {
        $this->loadModel('Settings');
        $mediaStorage = $this->Settings->fetch('mediastorage');

        if ($mediaStorage->value == 'awss3') {
            $awsS3AccessKey = $this->Settings->fetch('awss3accesskey');
            $awsS3SecretKey = $this->Settings->fetch('awss3secretkey');
            $awsS3Region    = $this->Settings->fetch('awss3region');
            $awsS3Bucket    = $this->Settings->fetch('awss3bucket');
        }
        else {
            $baseUrl = Router::url([
                '_name' => 'home'
            ], true);
        }

        if ($mediaType == 'image') {
            // If media storage is Amazon AWS S3
            if ($mediaStorage->value == 'awss3') {
                $s3Client = new S3Client([
                    'region'  => $awsS3Region->value,
                    'version' => 'latest',
                    'credentials' => [
                        'key'      => $awsS3AccessKey->value,
                        'secret'   => $awsS3SecretKey->value,
                    ]
                ]);

                // Key
                $originalKey           = 'images/original/' . $mediaName;
                $thumbnailSquareKey    = 'images/thumbnails/300x300/' . $mediaName;
                $thumbnailLandscapeKey = 'images/thumbnails/480x270/' . $mediaName;

                $original         = $s3Client->getObjectUrl($awsS3Bucket->value, $originalKey);
                $thumbnail300x300 = $s3Client->getObjectUrl($awsS3Bucket->value, $thumbnailSquareKey);
                $thumbnail480x270 = $s3Client->getObjectUrl($awsS3Bucket->value, $thumbnailLandscapeKey);
            }
            else {
                $original         = $baseUrl . 'uploads/images/original/' . $mediaName;
                $thumbnail300x300 = $baseUrl . 'uploads/images/thumbnails/300x300/' . $mediaName;
                $thumbnail480x270 = $baseUrl . 'uploads/images/thumbnails/480x270/' . $mediaName;
            }

            $result = [
                'path'  => $original,
                'thumbnail' => [
                    '300x300' => $thumbnail300x300,
                    '480x270' => $thumbnail480x270,
                ]
            ];
        }
        elseif ($mediaType == 'document') 
        {
            if ($mediaStorage->value == 'awss3') {
                $s3Client = new S3Client([
                    'region'  => $awsS3Region->value,
                    'version' => 'latest',
                    'credentials' => [
                        'key'      => $awsS3AccessKey->value,
                        'secret'   => $awsS3SecretKey->value,
                    ]
                ]);

                // Key
                $key = 'documents/' . $mediaName;

                $path = $s3Client->getObjectUrl($awsS3Bucket->value, $key);
            }
            else {
                $path = $baseUrl . 'uploads/documents/' . $mediaName;
            }

            $result = [
                'path' => $path
            ];
        }
        elseif ($mediaType == 'video') 
        {
            if ($mediaStorage->value == 'awss3') {
                $s3Client = new S3Client([
                    'region'  => $awsS3Region->value,
                    'version' => 'latest',
                    'credentials' => [
                        'key'      => $awsS3AccessKey->value,
                        'secret'   => $awsS3SecretKey->value,
                    ]
                ]);

                // Key
                $key = 'videos/' . $mediaName;

                $path = $s3Client->getObjectUrl($awsS3Bucket->value, $key);
            }
            else {
                $path = $baseUrl . 'uploads/videos/' . $mediaName;
            }

            $result = [
                'path' => $path
            ];
        }

        if ($return == 'original') {
            $this->set('filePath', $result['path']);
        }
        elseif ($return == 'thumbnail::300') {
            $this->set('filePath', $result['thumbnail']['300x300']);
        }
        elseif ($return == 'thumbnail::480') {
            $this->set('filePath', $result['thumbnail']['480x270']);
        }
    }
}