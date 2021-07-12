<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Http\Exception\NotFoundException;
use Cake\Utility\Text;
use Cake\Filesystem\File;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;
use Gregwar\Image\Image;
use Aws\S3\S3Client;  
use Aws\Exception\AwsException;

class FroalaController extends AppController
{
    public function beforeFilter(Event $event)
	{ 
	    parent::beforeFilter($event);
	    $purpleGlobal = new PurpleProjectGlobal();
		$databaseInfo   = $purpleGlobal->databaseInfo();
		if ($databaseInfo == 'default') {
			return $this->redirect(
	            ['prefix' => false, 'controller' => 'Setup', 'action' => 'index']
	        );
		}
	}
    public function froalaManagerLoadUrl() 
    {
        $this->viewBuilder()->enableAutoLayout(false);
        
        $this->loadModel('Medias');
        $medias      = $this->Medias->find('all', ['contain' => ['Admins']])->order(['Medias.id' => 'DESC']);
    	$this->set(compact('medias'));
        $this->render();
    }
    public function froalaImageUploadUrl()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        
        if ($this->request->is('post') || $this->request->is('ajax')) {
            if (!empty($this->request->getData('file'))) {
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                $purpleSettings = new PurpleProjectSettings();
                $timezone       = $purpleSettings->timezone();
                $date           = Carbon::now($timezone);
                
                $uploadPath = TMP . 'uploads' . DS . 'images' . DS;
                $fileName   = $this->request->getData('file.name');
                $newName    = Text::slug($fileName, ['preserve' => '.']);
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);;
                $acceptedExt   = ['jpeg', 'png', 'jpg'];

                if (in_array($fileExtension, $acceptedExt)) {
                    $fileOnlyName    = str_replace('.'.$fileExtension, '', $newName);
                    $dateSlug        = Text::slug($date);
                    $generatedName   = $fileOnlyName . '_' . $dateSlug . '.' . $fileExtension;
                    $fullSizeImage              = WWW_ROOT . 'uploads' . DS .'images' . DS .'original' . DS;
                    $uploadedThumbnailSquare    = WWW_ROOT . 'uploads' . DS .'images' . DS .'thumbnails' . DS . '300x300' . DS;
                    $uploadedThumbnailLandscape = WWW_ROOT . 'uploads' . DS .'images' . DS .'thumbnails' . DS . '480x270' . DS;
                    $uploadFile      = $uploadPath . $generatedName;

                    if (move_uploaded_file($this->request->getData('file.tmp_name'), $uploadFile)) {
                        $readImageFile   = new File($uploadFile);
                        $imageSize       = $readImageFile->size();
                        $fullSize           = Image::open($uploadFile)->save($fullSizeImage . $generatedName, 'guess', 90);
                        $thumbnailSquare    = Image::open($uploadFile)
                                                ->zoomCrop(300, 300)
                                                ->save($uploadedThumbnailSquare . $generatedName, 'guess', 90);
                        $thumbnailLandscape = Image::open($uploadFile)
                                                ->zoomCrop(480, 270)
                                                ->save($uploadedThumbnailLandscape . $generatedName, 'guess', 90);
                       
                        $this->loadModel('Medias');
                        $media = $this->Medias->newEntity();

                        $media->name     = $generatedName;
                        $media->created  = $date;
                        $media->title    = $fileOnlyName;
                        $media->size     = $imageSize;
                        $media->admin_id = $sessionID;

                        if ($this->Medias->save($media)) {
                            $readImageFile   = new File($uploadFile);
                            $deleteImage     = $readImageFile->delete();

                            $link = $this->request->getAttribute("webroot") . 'uploads/images/original/' . $generatedName;

                            $this->loadModel('Settings');

                            // Check for media storage
                            $mediaStorage   = $this->Settings->fetch('mediastorage');

                            // If media storage is Amazon AWS S3
                            if ($mediaStorage->value == 'awss3') {
                                $awsS3AccessKey = $this->Settings->fetch('awss3accesskey');
                                $awsS3SecretKey = $this->Settings->fetch('awss3secretkey');
                                $awsS3Region    = $this->Settings->fetch('awss3region');
                                $awsS3Bucket    = $this->Settings->fetch('awss3bucket');

                                $s3Client = new S3Client([
                                    'region'  => $awsS3Region->value,
                                    'version' => 'latest',
                                    'credentials' => [
                                        'key'      => $awsS3AccessKey->value,
                                        'secret'   => $awsS3SecretKey->value,
                                    ]
                                ]);

                                // Path
                                $originalFilePath           = $fullSizeImage . $generatedName;
                                $thumbnailSquareFilePath    = $uploadedThumbnailSquare . $generatedName;
                                $thumbnailLandscapeFilePath = $uploadedThumbnailLandscape . $generatedName;

                                // Key
                                $originalKey           = 'images/original/' . basename($originalFilePath);
                                $thumbnailSquareKey    = 'images/thumbnails/300x300/' . basename($thumbnailSquareFilePath);
                                $thumbnailLandscapeKey = 'images/thumbnails/480x270/' . basename($thumbnailLandscapeFilePath);
                                
                                // Send original image
                                try {
                                    $result = $s3Client->putObject([
                                        'Bucket'     => $awsS3Bucket->value,
                                        'Key'        => $originalKey,
                                        'SourceFile' => $originalFilePath,
                                        'ACL'        => 'public-read',
                                    ]);
                                } 
                                catch (AwsException $e) {
                                    $this->log($e->getMessage(), 'debug');
                                }

                                // Send thumbnail 300x300 image
                                try {
                                    $result = $s3Client->putObject([
                                        'Bucket'     => $awsS3Bucket->value,
                                        'Key'        => $thumbnailSquareKey,
                                        'SourceFile' => $thumbnailSquareFilePath,
                                        'ACL'        => 'public-read',
                                    ]);
                                } 
                                catch (AwsException $e) {
                                    $this->log($e->getMessage(), 'debug');
                                }

                                // Send thumbnail 480x270 image
                                try {
                                    $result = $s3Client->putObject([
                                        'Bucket'     => $awsS3Bucket->value,
                                        'Key'        => $thumbnailLandscapeKey,
                                        'SourceFile' => $thumbnailLandscapeFilePath,
                                        'ACL'        => 'public-read',
                                    ]);

                                    $link = $s3Client->getObjectUrl($awsS3Bucket->value, $originalKey);
                                } 
                                catch (AwsException $e) {
                                    $this->log($e->getMessage(), 'debug');
                                }
                            }

                            $json = json_encode(['link' => $link]);
                        }
                        else {
                            $json = json_encode(['status' => 'error', 'error' => "Can't save image file. Please try again."]);
                        } 
                    }
                    else {
                        $json = json_encode(['status' => 'error', 'error' => "Can't upload image file. Please try again"]);
                    }
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "File extension is not allowed. Please try again with another file."]);
                }
            }
            $this->set(['json' => $json]);
            $this->render();
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
    public function froalaFileUploadUrl()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        
        if ($this->request->is('post') || $this->request->is('ajax')) {
            if (!empty($this->request->getData('file'))) {
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                $purpleSettings = new PurpleProjectSettings();
                $timezone       = $purpleSettings->timezone();
                $date           = Carbon::now($timezone);
                
                $fileName   = $this->request->getData('file.name');
                $newName    = Text::slug($fileName, ['preserve' => '.']);
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);;
                $acceptedExt   = ['txt', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'md', 'pdf'];

                if (in_array($fileExtension, $acceptedExt)) {
                    $fileOnlyName    = str_replace('.'.$fileExtension, '', $newName);
                    $dateSlug        = Text::slug($date);
                    $generatedName   = $fileOnlyName . '_' . $dateSlug . '.' . $fileExtension;
                    $uploadFile      = WWW_ROOT . 'uploads' . DS .'documents' . DS . $generatedName;

                    if (move_uploaded_file($this->request->getData('file.tmp_name'), $uploadFile)) {
                        $readDocumentFile = new File($uploadFile);
                        $fileSize         = $readDocumentFile->size();

                        $this->loadModel('MediaDocs');
                        $doc       = $this->MediaDocs->newEntity();

                        $doc->name     = $generatedName;
                        $doc->created  = $date;
                        $doc->title    = $fileOnlyName;
                        $doc->size     = $fileSize;
                        $doc->admin_id = $sessionID;

                        if ($this->MediaDocs->save($doc)) {
                            $link = $this->request->getAttribute("webroot") . 'uploads/documents/' . $generatedName;

                            $this->loadModel('Settings');
                            
                            // Check for media storage
                            $mediaStorage   = $this->Settings->fetch('mediastorage');

                            // If media storage is Amazon AWS S3
                            if ($mediaStorage->value == 'awss3') {
                                $awsS3AccessKey = $this->Settings->fetch('awss3accesskey');
                                $awsS3SecretKey = $this->Settings->fetch('awss3secretkey');
                                $awsS3Region    = $this->Settings->fetch('awss3region');
                                $awsS3Bucket    = $this->Settings->fetch('awss3bucket');

                                $s3Client = new S3Client([
                                    'region'  => $awsS3Region->value,
                                    'version' => 'latest',
                                    'credentials' => [
                                        'key'      => $awsS3AccessKey->value,
                                        'secret'   => $awsS3SecretKey->value,
                                    ]
                                ]);

                                // Path
                                $filePath = $uploadFile;

                                // Key
                                $key = 'documents/' . basename($filePath);
                                try {
                                    $result = $s3Client->putObject([
                                        'Bucket'     => $awsS3Bucket->value,
                                        'Key'        => $key,
                                        'SourceFile' => $filePath,
                                        'ACL'        => 'public-read',
                                    ]);

                                    $link = $s3Client->getObjectUrl($awsS3Bucket->value, $key);
                                } 
                                catch (AwsException $e) {
                                    $this->log($e->getMessage(), 'debug');
                                }
                            }
                            $json = json_encode(['link' => $link]);
                        }
                        else {
                            $json = json_encode(['status' => 'error', 'error' => "Can't save file. Please try again."]);
                        } 
                    }
                    else {
                        $json = json_encode(['status' => 'error', 'error' => "Can't upload file. Please try again"]);
                    }
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "File extension is not allowed. Please try again with another file."]);
                }
            }
            $this->set(['json' => $json]);
            $this->render();
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
    public function froalaVideoUploadUrl()
    {
        $this->viewBuilder()->enableAutoLayout(false);
        
        if ($this->request->is('post') || $this->request->is('ajax')) {
            if (!empty($this->request->getData('file'))) {
                $session   = $this->getRequest()->getSession();
                $sessionID = $session->read('Admin.id');

                $purpleSettings = new PurpleProjectSettings();
                $timezone       = $purpleSettings->timezone();
                $date           = Carbon::now($timezone);
                
                $fileName   = $this->request->getData('file.name');
                $newName    = Text::slug($fileName, ['preserve' => '.']);
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);;
                $acceptedExt   = ['mp4', 'm4v', 'ogv', 'webm'];

                if (in_array($fileExtension, $acceptedExt)) {
                    $fileOnlyName    = str_replace('.'.$fileExtension, '', $newName);
                    $dateSlug        = Text::slug($date);
                    $generatedName   = $fileOnlyName . '_' . $dateSlug . '.' . $fileExtension;
                    $uploadFile      = WWW_ROOT . 'uploads' . DS .'videos' . DS . $generatedName;

                    if (move_uploaded_file($this->request->getData('file.tmp_name'), $uploadFile)) {
                        $readVideoFile = new File($uploadFile);
                        $fileSize      = $readVideoFile->size();

                        $this->loadModel('MediaVideos');
                        $video = $this->MediaVideos->newEntity();

                        $video->name     = $generatedName;
                        $video->created  = $date;
                        $video->title    = $fileOnlyName;
                        $video->size     = $fileSize;
                        $video->admin_id = $sessionID;

                        if ($this->MediaVideos->save($video)) {
                            $link = $this->request->getAttribute("webroot") . 'uploads/videos/' . $generatedName;

                            $this->loadModel('Settings');

                            // Check for media storage
                            $mediaStorage   = $this->Settings->fetch('mediastorage');

                            // If media storage is Amazon AWS S3
                            if ($mediaStorage->value == 'awss3') {
                                $awsS3AccessKey = $this->Settings->fetch('awss3accesskey');
                                $awsS3SecretKey = $this->Settings->fetch('awss3secretkey');
                                $awsS3Region    = $this->Settings->fetch('awss3region');
                                $awsS3Bucket    = $this->Settings->fetch('awss3bucket');

                                $s3Client = new S3Client([
                                    'region'  => $awsS3Region->value,
                                    'version' => 'latest',
                                    'credentials' => [
                                        'key'      => $awsS3AccessKey->value,
                                        'secret'   => $awsS3SecretKey->value,
                                    ]
                                ]);

                                // Path
                                $filePath = $uploadFile;

                                // Key
                                $key = 'videos/' . basename($filePath);
                                try {
                                    $result = $s3Client->putObject([
                                        'Bucket'     => $awsS3Bucket->value,
                                        'Key'        => $key,
                                        'SourceFile' => $filePath,
                                        'ACL'        => 'public-read',
                                    ]);

                                    $link = $s3Client->getObjectUrl($awsS3Bucket->value, $key);
                                } 
                                catch (AwsException $e) {
                                    $this->log($e->getMessage(), 'debug');
                                }
                            }

                            $json = json_encode(['link' => $link]);
                        }
                        else {
                            $json = json_encode(['status' => 'error', 'error' => "Can't save file. Please try again."]);
                        } 
                    }
                    else {
                        $json = json_encode(['status' => 'error', 'error' => "Can't upload file. Please try again"]);
                    }
                }
                else {
                    $json = json_encode(['status' => 'error', 'error' => "File extension is not allowed. Please try again with another file."]);
                }
            }
            $this->set(['json' => $json]);
            $this->render();
        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
    public function froalaManagerDeleteUrl() 
    {
        $this->viewBuilder()->enableAutoLayout(false);

        if ($this->request->is('post')) {

        }
        else {
	        throw new NotFoundException(__('Page not found'));
	    }
    }
}