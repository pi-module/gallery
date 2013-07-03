<?php
/**
 * Gallery admin photo controller
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Copyright (c) Pi Engine http://www.xoopsengine.org
 * @license         http://www.xoopsengine.org/license New BSD License
 * @author          Hossein Azizabadi <azizabadi@faragostaresh.com>
 * @since           3.0
 * @package         Module\Gallery
 * @version         $Id$
 */

namespace Module\Gallery\Controller\Admin;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Pi\File\Transfer\Upload;
use Module\Gallery\Form\PhotoForm;
use Module\Gallery\Form\PhotoFilter;
use Zend\Json\Json;


class PhotoController extends ActionController
{
    protected $PhotoPrefix = 'photo_';
    protected $photoColumns = array(
        'id', 'title', 'alias', 'album', 'information', 'keywords', 'description', 'image', 'path', 'link', 'size', 
        'resx', 'resy', 'order', 'hits', 'comments', 'download', 'create', 'status', 'author', 'point', 'count'
    );

    public function indexAction()
    {
        // Get page
        $page = $this->params('p', 1);
        // Set params
        $params = array(
            'module' => $this->getModule(),
            'controller' => 'photo',
            'action' => 'index',
        );
        // Set where
        $where = array();
        // Get album
        $album = $this->params('album');
        if (!empty($album)) {
            $where['album'] = $album;
            $params['album'] = $album;
            $this->view()->assign('back', 1);
        }
        // Get author
        $author = $this->params('author');
        if (!empty($author)) {
            $where['author'] = $author;
            $params['author'] = $author;
            $this->view()->assign('back', 1);
        }
        // Get type
        $type = $this->params('type');
        if (!empty($type)) {
            if ($type == 'accept') {
                $where['status'] = 1;
                $params['type'] = 'accept';
                $this->view()->assign('back', 1);
            } elseif ($type == 'reject') {
                $where['status'] = array(0, 2, 3, 4);
                $params['type'] = 'reject';
                $this->view()->assign('back', 1);
            }
        }
        // Get album list
        $select = $this->getModel('album')->select()->columns(array('id', 'title', 'alias'))->order(array('id DESC'));
        $rowset = $this->getModel('album')->selectWith($select);
        // Make album list
        foreach ($rowset as $row) {
            $albumList[$row->id] = $row->toArray();
        }
        // Get info
        $offset = (int)($page - 1) * $this->config('admin_perpage');
        $columns = array('id', 'title', 'alias', 'album', 'image', 'path', 'author', 'create', 'status', 'author');
        $select = $this->getModel('photo')->select()->where($where)->columns($columns)->offset($offset)->order(array('create DESC', 'id DESC'))->limit(intval($this->config('admin_perpage')));
        $rowset = $this->getModel('photo')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $photo[$row->id] = $row->toArray();
            $user = Pi::model('user_account')->find($photo[$row->id]['author'])->toArray();
            $photo[$row->id]['identity'] = $user['identity'];
            $photo[$row->id]['albumid'] = $albumList[$row->album]['id'];
            $photo[$row->id]['albumtitle'] = $albumList[$row->album]['title'];
            $photo[$row->id]['create'] = date('Y/m/d', $photo[$row->id]['create']);
            $photo[$row->id]['thumburl'] = Pi::url('/upload/' . $this->config('image_path') . '/thumb/' . $photo[$row->id]['path'] . '/' . $photo[$row->id]['image']);
            $photo[$row->id]['shorttitle'] = mb_strlen($photo[$row->id]['title'], 'utf-8') > 15 ? mb_substr($photo[$row->id]['title'], 0, 15, 'utf-8') . "..." : $photo[$row->id]['title'];
        }
        // Set paginator
        $select = $this->getModel('photo')->select()->columns(array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)')))->where($where);
        $count = $this->getModel('photo')->selectWith($select)->current()->count;
        $paginator = \Pi\Paginator\Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($this->config('admin_perpage'));
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(array(
            // Use router to build URL for each page
            'pageParam' => 'p',
            'totalParam' => 't',
            'router' => $this->getEvent()->getRouter(),
            'route' => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'params' => $params,
        ));
        // Set view
        $this->view()->setTemplate('photo_index');
        $this->view()->assign('photos', $photo);
        $this->view()->assign('paginator', $paginator);

    }

    public function acceptAction()
    {
        // Get id and status
        $id = $this->params('id');
        $status = $this->params('status');
        // set photo
        $photo = $this->getModel('photo')->find($id);
        // Check
        if ($photo && in_array($status, array(0, 1))) {
            // Accept
            $photo->status = $status;
            // Save
            if ($photo->save()) {
                $message = sprintf(__('%s photo accept successfully'), $photo->title);
                $status = 1;
            } else {
                $message = sprintf(__('Error in accept %s photo'), $photo->title);
                $status = 0;
            }
        } else {
            $message = __('Please select photo');
            $status = 0;
        }

        return array(
            'status' => $status,
            'message' => $message,
        );
    }

    public function updateAction()
    {
        // Get id
        $id = $this->params('id');
        $module = $options['module'] = $this->params('module');
        // Get this image
        if ($id) {
            $values = $this->getModel('photo')->find($id)->toArray();
            $options['imageurl'] = Pi::url('/upload/' . $this->config('image_path') . '/thumb/' . $values['path'] . '/' . $values['image']);
        } else {
            $options['imageurl'] = null;
        }
        $form = new PhotoForm('photo', $options);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $file = $this->request->getFiles();
            $form->setInputFilter(new PhotoFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                // Tag
                $tag = explode(' ', $values['tag']);
                if (empty($values['id'])) {
                    // Set path
                    $values['path'] = date('Y') . '/' . date('m');
                    $original_path = $this->config('image_path') . '/original/' . $values['path'];
                    $large_path = $this->config('image_path') . '/large/' . $values['path'];
                    $medium_path = $this->config('image_path') . '/medium/' . $values['path'];
                    $thumb_path = $this->config('image_path') . '/thumb/' . $values['path'];
                    // Do upload
                    $uploader = new Upload(array('destination' => $original_path, 'rename' => $this->PhotoPrefix . '%random%'));
                    $uploader->setExtension($this->config('image_extension'));
                    $uploader->setSize($this->config('image_size'));
                    if ($uploader->isValid()) {
                        $uploader->receive();
                        // Get image name
                        $values['image'] = $uploader->getUploaded('image');
                        // Resize
                        $this->resize($values['image'], $original_path, $large_path, '8000', '8000');
                        $this->resize($values['image'], $original_path, $medium_path, '8000', '8000');
                        $this->resize($values['image'], $original_path, $thumb_path, '8000', '8000');
                    } else {
                        $message = $upload->getErrors();
                        $class = 'alert-error';
                        $this->jump(array('action' => 'update'), $message);
                    }
                    // end upload image
                    $default = $data['default'];
                }
                // unset unused columns
                foreach (array_keys($values) as $key) {
                    if (!in_array($key, $this->photoColumns)) {
                        unset($values[$key]);
                    }
                }
                // Set keywords
                $keywords = ($values['keywords']) ? $values['keywords'] : $values['title'];
                $values['keywords'] = $this->meta()->keywords($keywords);
                // Set description
                $description = ($values['description']) ? $values['description'] : $values['title'];
                $values['description'] = $this->meta()->description($description);
                // Set alias
                $alias = ($values['alias']) ? $values['alias'] : $values['title'];
                $values['alias'] = $this->alias($alias, $values['id'], $this->getModel('photo'));
                // Set if new
                if (empty($values['id'])) {
                    // Set time
                    $values['create'] = time();
                    // Set user
                    $values['author'] = Pi::registry('user')->id;
                    // Set order
                    $select = $this->getModel('photo')->select()->columns(array('order'))->order(array('order DESC'))->limit(1);
                    $lastrow = $this->getModel('photo')->selectWith($select)->toArray();
                    $values['order'] = $lastrow[0]['order'] + 1;
                }
                // Save values
                if (!empty($values['id'])) {
                    $row = $this->getModel('photo')->find($values['id']);
                } else {
                    $row = $this->getModel('photo')->createRow();
                    if (!empty($default)) {
                        // Add image for album and category
                        Pi::service('api')->gallery(array('Admin', 'Image'), $values['album'], $values['image'], $values['path']);
                    }
                }
                $row->assign($values);
                $row->save();
                // Tag
                if (is_array($tag) && Pi::service('module')->isActive('tag')) {
                    if (empty($values['id'])) {
                        Pi::service('tag')->add($module, $row->id, '', $tag);
                    } else {
                        Pi::service('tag')->update($module, $row->id, '', $tag);
                    }
                }
                // Writer
                if (empty($values['id'])) {
                    Pi::service('api')->gallery(array('Photographer', 'Add'), $values['author']);
                }
                // Check it save or not
                if ($row->id) {
                    $message = __('Photo data saved successfully.');
                    $class = 'alert-success';
                    $this->jump(array('action' => 'index'), $message);
                } else {
                    $message = __('Photo data not saved.');
                    $class = 'alert-error';
                }
            } else {
                $message = __('Invalid data, please check and re-submit.');
                $class = 'alert-error';
            }
        } else {
            if (isset($values['id'])) {
                $values['tag'] = implode(' ', Pi::service('tag')->get($module, $values['id'], ''));
                $form->setData($values);
                $message = 'You can edit this photo';
                $class = '';
            } else {
                $message = 'You can add new photo';
                $class = '';
            }
        }
        // Set view
        $this->view()->setTemplate('photo_update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add a Photo'));
        $this->view()->assign('message', $message);
        $this->view()->assign('class', $class);
    }

    public function updatesAction()
    {
        // Get id
        $album = $this->params('album');
        if (empty($album)) {
            $this->jump(array('controller' => 'album', 'action' => 'index'), __('You must select album'));
        }
        // Get story
        $album = $this->getModel('album')->find($album)->toArray();
        if (empty($album)) {
            $this->jump(array('controller' => 'album', 'action' => 'index'), __('Your selected album not exist'));
        }
        // Set view
        $this->view()->setTemplate('photo_updates');
        $this->view()->assign('album', $album);
        $this->view()->assign('title', sprintf(__('Add photos to %s'), $album['title']));
    }

    public function uploadAction()
    {
        // deactive log
        Pi::service('log')->active(false);
        // Set return
        $return = array(
            'status' => 1, 'message' => '', 'id' => '', 'title' => '', 'create' => '',
            'type' => '', 'status' => '', 'hits' => '', 'size' => '', 'preview' => '',
        );
        // Get id
        $album = $this->params('album');
        if (empty($album)) {
            $return = array(
                'status' => 0,
                'message' => __('You must select album'),
            );
        } else {
            // Get story
            $album = $this->getModel('album')->find($album)->toArray();
            if (empty($album)) {
                $return = array(
                    'status' => 0,
                    'message' => __('Your selected album not exist'),
                );
            } else {
                // Set path
                $path = date('Y') . '/' . date('m');
                $original_path = $this->config('image_path') . '/original/' . $path;
                $large_path = $this->config('image_path') . '/large/' . $path;
                $medium_path = $this->config('image_path') . '/medium/' . $path;
                $thumb_path = $this->config('image_path') . '/thumb/' . $path;
                // start upload
                $uploader = new Upload(array('destination' => $original_path, 'rename' => $this->PhotoPrefix . '%random%'));
                $uploader->setExtension($this->config('image_extension'));
                $uploader->setSize($this->config('image_size'));
                if ($uploader->isValid()) {
                    $uploader->receive();
                    // Set info
                    $image = $uploader->getUploaded('file');
                    // Resize
                    $this->resize($image, $original_path, $large_path, $this->config('image_largew'), $this->config('image_largeh'));
                    $this->resize($image, $original_path, $medium_path, $this->config('image_mediumw'), $this->config('image_mediumh'));
                    $this->resize($image, $original_path, $thumb_path, $this->config('image_thumbw'), $this->config('image_thumbh'));
                    // Set save array
                    $values['image'] = $image;
                    $values['title'] = $album['title'];
                    $values['path'] = $path;
                    $values['album'] = $album['id'];
                    $values['create'] = time();
                    $values['status'] = 1;
                    $values['author'] = Pi::registry('user')->id;
                    $values['alias'] = md5($image . $path); // temporary
                    $values['description'] = $this->meta()->description($album['title']);
                    $values['keywords'] = $this->meta()->keywords($album['title']);
                    // save in DB
                    $row = $this->getModel('photo')->createRow();
                    $row->assign($values);
                    $row->save();
                    //
                    Pi::service('api')->gallery(array('Photographer', 'Add'), $values['author']);
                    // Set erturn array
                    $return['id'] = $row->id;
                    $return['title'] = $row->title;
                    $return['create'] = date('Y/m/d', $row->create);
                    $return['status'] = $row->status;
                    $return['preview'] = Pi::url('/upload/' . $this->config('image_path') . '/thumb/' . $row->path . '/' . $row->image);
                } else {
                    // Upload error
                    $messages = $uploader->getMessages();
                    $return = array(
                        'status' => 0,
                        'message' => implode('; ', $messages),
                    );
                }
            }
        }
        $this->view()->setTemplate(false)->setLayout('layout-content');
        return Json::encode($return);
    }

    public function deleteAction()
    {
        /*
           * not completed and need confirm option
           */
        // Get information
        $this->view()->setTemplate(false);
        $id = $this->params('id');
        $row = $this->getModel('photo')->find($id);
        if ($row) {
            // Writer
            Pi::service('api')->gallery(array('Photographer', 'Delete'), $row->author);
            // Remove page
            $row->delete();
            $this->jump(array('action' => 'index'), __('This photo deleted'));
        }
        $this->jump(array('action' => 'index'), __('Please select photo'));
    }
}