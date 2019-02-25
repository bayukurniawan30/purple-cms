<?php
namespace App\Controller\Purple;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\Http\Exception\NotFoundException;
use Cake\Filesystem\Folder;
use Cake\Utility\Text;
use Cake\Filesystem\File;
use Cake\I18n\Time;
use App\Purple\PurpleProjectGlobal;
use App\Purple\PurpleProjectSettings;
use Carbon\Carbon;
use Gregwar\Image\Image;
use \Gumlet\ImageResize;

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
                        /**
                         * Old style, cropping with ImageResize, but quality is bad
                         * 
                            $fullSize        = new ImageResize($uploadFile);
                            $fullSize->save($fullSizeImage . $generatedName);
                         */
                        $fullSize = Image::open($uploadFile)->save($fullSizeImage . $generatedName, 'guess', 90);
                        /**
                         * Old style, cropping with ImageResize, but quality is bad
                         * 
                            $thumbnailSquare = new ImageResize($uploadFile);
                            $thumbnailSquare->crop(300, 300);
                            $thumbnailSquare->save($uploadedThumbnailSquare . $generatedName);
                         */
                        $thumbnailSquare = Image::open($uploadFile)
                                                ->zoomCrop(300, 300)
                                                ->save($uploadedThumbnailSquare . $generatedName, 'guess', 90);
                        /**
                         * Old style, cropping with ImageResize, but quality is bad
                         * 
                            $thumbnailLandscape = new ImageResize($uploadFile);
                            $thumbnailLandscape->crop(480, 270);
                            $thumbnailLandscape->save($uploadedThumbnailLandscape . $generatedName);
                         */
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

                            $json = json_encode(['link' => $this->request->getAttribute("webroot") . 'uploads/images/original/' . $generatedName]);
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
                            $json = json_encode(['link' => $this->request->getAttribute("webroot") . 'uploads/documents/' . $generatedName]);
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

                        if ($videosTable->save($video)) {
                            $json = json_encode(['link' => $this->request->getAttribute("webroot") . 'uploads/videos/' . $generatedName]);
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