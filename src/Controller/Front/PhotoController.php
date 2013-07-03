<?php
/**
 * Gallery photo controller
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

namespace Module\Gallery\Controller\Front;
use Pi\Mvc\Controller\ActionController;
use Pi;

class PhotoController extends ActionController
{

    public function indexAction()
    {
        // Get page ID or alias from url
        $params = $this->params()->fromRoute();
        // Find photo
        $photo = $this->getModel('photo')->find($params['id'])->toArray();
        // Get Module Config
        $config = Pi::service('registry')->config->read($params['module']);
        // Check page
        if (!$photo || $photo['status'] != 1) {
            $this->jump(array('route' => 'gallery', 'module' => $params['module'], 'controller' => 'index'), __('The photo not found.'));
        }
        // Find album
        $album = $this->getModel('album')->find($photo['album'])->toArray();
        // Check category is active
        if ($album['status'] == 0) {
            $this->jump(array('route' => 'gallery', 'module' => $params['module'], 'controller' => 'index'), __('The album not found.'));
        }
        // Check album permission
        /*	todo */
        // Update Hits
        $this->getModel('photo')->update(array('hits' => $photo['hits'] + 1), array('id' => $photo['id']));
        // set photo url
        $photo['originalurl'] = Pi::url('/upload/' . $config['image_path'] . '/original/' . $photo['path'] . '/' . $photo['image']);
        $photo['largeurl'] = Pi::url('/upload/' . $config['image_path'] . '/large/' . $photo['path'] . '/' . $photo['image']);
        $photo['mediumurl'] = Pi::url('/upload/' . $config['image_path'] . '/medium/' . $photo['path'] . '/' . $photo['image']);
        $photo['thumburl'] = Pi::url('/upload/' . $config['image_path'] . '/thumb/' . $photo['path'] . '/' . $photo['image']);
        $photo['downloadurl'] = $this->url('.gallery', array('module' => $params['module'], 'controller' => 'photo', 'action' => 'download', 'id' => $photo['id']));
        $photo['albumurl'] = $this->url('.gallery', array('module' => $params['module'], 'controller' => 'album', 'alias' => $album['alias']));
        // Set date
        $photo['create'] = date('Y/m/d H:i:s', $photo['create']);
        // Get writer identity
        $photographer = Pi::model('user_account')->find($photo['author'])->toArray();
        $photo['identity'] = $photographer['identity'];
        unset($photographer);
        // Links
        if ($config['photo_nav']) {
            // Select next
            $where = array('status' => 1, 'album' => $photo['album'], 'id > ?' => $photo['id']);
            $selectNext = $this->getModel('photo')->select()->columns(array('id', 'title', 'alias'))->where($where)->order(array('id ASC'))->limit(1);
            $photoNext = $this->getModel('photo')->selectWith($selectNext)->toArray();
            if ($photoNext) {
                $link['next'] = $this->url('.gallery', array('module' => $params['module'], 'controller' => 'photo', 'id' => $photoNext[0]['id']));
            }
            // Select Prev
            $where = array('status' => 1, 'album' => $photo['album'], 'id <  ?' => $photo['id']);
            $selectPrev = $this->getModel('photo')->select()->columns(array('id', 'title', 'alias'))->where($where)->order(array('id DESC'))->limit(1);
            $photoPrev = $this->getModel('photo')->selectWith($selectPrev)->toArray();
            if ($photoPrev) {
                $link['previous'] = $this->url('.gallery', array('module' => $params['module'], 'controller' => 'photo', 'id' => $photoPrev[0]['id']));
            }
        }
        // Set vote
        if ($config['vote_bar'] && Pi::service('module')->isActive('vote')) {
            $vote['point'] = $photo['point'];
            $vote['count'] = $photo['count'];
            $vote['item'] = $photo['id'];
            $vote['module'] = $params['module'];
            $vote['type'] = $config['vote_type'];
            $vote['table'] = 'photo';
            $this->view()->assign('vote', $vote);
        }
        // Set view
        $this->view()->headTitle($photo['title']);
        $this->view()->headDescription($photo['description'], 'set');
        $this->view()->headKeywords($photo['keywords'], 'set');
        $this->view()->setTemplate('photo_index');
        $this->view()->assign('photo', $photo);
        $this->view()->assign('album', $album);
        $this->view()->assign('link', $link);
        $this->view()->assign('config', $config);
        $this->view()->assign('tags', Pi::service('tag')->get($params['module'], $photo['id'], ''));
        // Support Comment system for test
        if (Pi::service('module')->isActive('comment')) {
        	   // Set story url
            $url['route'] = $params['module'] . '-gallery';
            $url['parameter']['module'] = $params['module'];
            $url['parameter']['controller'] = 'photo';
            $url['parameter']['alias'] = $photo['id'];
            // Get comment
            $comment = Pi::service('api')->comment(array('Comment', 'Render'), $params['module'], $photo['id'], $url);
            $this->view()->assign('usecomment', $comment['config']['comment_active']);
            $this->view()->assign('commentForm', $comment['form']);
            $this->view()->assign('commentList', $comment['list']);
            $this->view()->assign('commentConfig', $comment['config']);
        }
    }

    public function sendAction()
    {

    }

    public function downloadAction()
    {
        // Get page ID or alias from url
        $params = $this->params()->fromRoute();
        // Find photo
        $photo = $this->getModel('photo')->find($params['id'])->toArray();
        // Check page
        if (!$photo || $photo['status'] != 1) {
            $this->jump(array('route' => 'gallery', 'module' => $params['module'], 'controller' => 'photo', 'id' => $photo['id']), __('The photo not found.'));
        }
        // Find album
        $album = $this->getModel('album')->find($photo['album'])->toArray();
        // Check category is active
        if ($album['status'] == 0) {
            $this->jump(array('route' => 'gallery', 'module' => $params['module'], 'controller' => 'index'), __('The album not found.'));
        }
        // Check album permission
        /* todo */
        // Update download
        $this->getModel('photo')->update(array('download' => $photo['download'] + 1), array('id' => $photo['id']));
        // Get photo
        $original = Pi::path('/upload/' . $this->config('image_path') . '/original/' . $photo['path'] . '/' . $photo['image']);
        $large = Pi::path('/upload/' . $this->config('image_path') . '/large/' . $photo['path'] . '/' . $photo['image']);
        // dwonload file
        if (file_exists($large)) {
            // This part must be change
            if (function_exists('mime_content_type')) {
                $mtype = mime_content_type($large);
            } else if (function_exists('finfo_file')) {
                $finfo = finfo_open(FILEINFO_MIME); // return mime type
                $mtype = finfo_file($finfo, $large);
                finfo_close($finfo);
            }
            header($mtype);
            header('Content-Disposition: attachment; filename=' . $photo['image']);
            readfile($large);
            exit();
        } else {
            $this->jump(array('route' => 'gallery', 'module' => $params['module'], 'controller' => 'photo', 'alias' => $photo['alias']), __('The photo not found.'));
        }
    }
}