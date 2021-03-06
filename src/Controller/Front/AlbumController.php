<?php
/**
 * Gallery album controller
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

use Pi;
use Pi\Mvc\Controller\ActionController;

class AlbumController extends ActionController
{

    public function indexAction()
    {
        // Get page
        $page = $this->params('page', 1);
        // Get page ID or alias from url
        $params = $this->params()->fromRoute();
        // Get config
        $config = Pi::service('registry')->config->read($params['module']);
        // Get album information from model
        $album = $this->getModel('album')->find($params['alias'], 'alias')->toArray();
        $album['thumburl'] = Pi::url('/upload/' . $this->config('image_path') . '/thumb/' . $album['path'] . '/' . $album['image']);
        $album['mediumurl'] = Pi::url('/upload/' . $this->config('image_path') . '/medium/' . $album['path'] . '/' . $album['image']);
        $album['create'] = date('Y/m/d H:i:s', $album['create']);
        // Get category information from model
        if ($album['category']) {
            $category = $this->getModel('category')->find($album['category'])->toArray();
            // Check page
            if (!$album || $album['status'] != 1 || !$category || $category['status'] != 1) {
                $this->jump(array('route' => 'gallery', 'module' => $params['module'], 'controller' => 'index'), __('The Album not found.'));
            }
        } else {
            // Check page
            if (!$album || $album['status'] != 1) {
                $this->jump(array('route' => 'gallery', 'module' => $params['module'], 'controller' => 'index'), __('The Album not found.'));
            }
        }
        // Get list of photos
        $columns = array('id', 'title', 'alias', 'album', 'image', 'path', 'hits', 'comments', 'create', 'author');
        $order = array('create DESC', 'id DESC');
        $offset = (int)($page - 1) * $config['album_perpage'];
        $where = array('status' => 1, 'album' => $album['id']);
        $select = $this->getModel('photo')->select()->columns($columns)->where($where)->order($order)->offset($offset)->limit(intval($config['album_perpage']));
        $rowset = $this->getModel('photo')->selectWith($select);
        foreach ($rowset as $row) {
            $photo[$row->id] = $row->toArray();
            $photo[$row->id]['create'] = date('Y/m/d H:i:s', $photo[$row->id]['create']);
            $photo[$row->id]['mediumurl'] = Pi::url('/upload/' . $this->config('image_path') . '/medium/' . $photo[$row->id]['path'] . '/' . $photo[$row->id]['image']);
            $photo[$row->id]['thumburl'] = Pi::url('/upload/' . $this->config('image_path') . '/thumb/' . $photo[$row->id]['path'] . '/' . $photo[$row->id]['image']);
            $photo[$row->id]['url'] = $this->url('.gallery', array('module' => $params['module'], 'controller' => 'photo', 'id' => $photo[$row->id]['id']));
        }
        // Set paginator
        $select = $this->getModel('photo')->select()->columns(array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)')))->where($where);
        $count = $this->getModel('photo')->selectWith($select)->current()->count;
        $paginator = \Pi\Paginator\Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($config['album_perpage']);
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(array(
            'template' => $this->url('.gallery', array('module' => $params['module'], 'controller' => 'album', 'alias' => $params['alias'], 'page' => '%page%')),
        ));
        // Set last photo-bar
        if ($this->config('list_lastphoto')) {
            $this->view()->assign('lastPhotos', Pi::service('api')->gallery(array('Photo', 'Last'), $this->config('list_barnumber')));
        }
        // Set top photo-bar
        if ($this->config('list_topphoto')) {
            $this->view()->assign('topPhotos', Pi::service('api')->gallery(array('Photo', 'Top'), $this->config('list_barnumber'), $this->config('list_topday')));
        }
        // Set random photo-bar
        if ($this->config('list_randomphoto')) {
            $this->view()->assign('randomPhotos', Pi::service('api')->gallery(array('Photo', 'Random'), $this->config('list_barnumber')));
        }
        // Set view
        $this->view()->headTitle($album['title']);
        $this->view()->headDescription($album['description'], 'set');
        $this->view()->headKeywords($album['keywords'], 'set');
        $this->view()->setTemplate('album_index');
        $this->view()->assign('album', $album);
        $this->view()->assign('photos', $photo);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('config', $config);

        if (!empty($category)) {
            $this->view()->assign('category', $category);
        }
    }
}