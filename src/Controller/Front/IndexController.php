<?php
/**
 * Gallery index controller
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

class IndexController extends ActionController
{

    public function indexAction()
    {
        // Get page
        $page = $this->params('page', 1);
        // Get page ID or alias from url
        $params = $this->params()->fromRoute();
        // Get config
        $config = Pi::service('registry')->config->read($params['module']);
        // Get category
        if (!empty($params['alias'])) {
            // Get album information from model
            $category = $this->getModel('category')->find($params['alias'], 'alias')->toArray();
            $category['thumburl'] = Pi::url('/upload/' . $this->config('image_path') . '/thumb/' . $category['path'] . '/' . $category['image']);
            $category['mediumurl'] = Pi::url('/upload/' . $this->config('image_path') . '/medium/' . $category['path'] . '/' . $category['image']);
            $category['create'] = date('Y/m/d H:i:s', $category['create']);
            // Check page
            if (!$category || $category['status'] != 1) {
                $this->jump(array('route' => 'gallery', 'module' => $params['module'], 'controller' => 'index'), __('The Controller not found.'));
            }
            $categoryId = $category['id'];
        } else {
            $categoryId = 0;
        }
        // Get list of category or sub categoryes
        $columns = array('id', 'title', 'alias', 'image', 'path', 'create');
        $order = array('create DESC', 'id DESC');
        $where = array('status' => 1, 'pid' => $categoryId);
        $select = $this->getModel('category')->select()->columns($columns)->where($where)->order($order);
        $rowset = $this->getModel('category')->selectWith($select);
        foreach ($rowset as $row) {
            $subcategory[$row->id] = $row->toArray();
            $subcategory[$row->id]['create'] = date('Y/m/d H:i:s', $subcategory[$row->id]['create']);
            $subcategory[$row->id]['mediumurl'] = Pi::url('/upload/' . $this->config('image_path') . '/medium/' . $subcategory[$row->id]['path'] . '/' . $subcategory[$row->id]['image']);
            $subcategory[$row->id]['thumburl'] = Pi::url('/upload/' . $this->config('image_path') . '/thumb/' . $subcategory[$row->id]['path'] . '/' . $subcategory[$row->id]['image']);
            $subcategory[$row->id]['url'] = $this->url('.gallery', array('module' => $params['module'], 'controller' => 'category', 'alias' => $subcategory[$row->id]['alias']));
        }
        // Get list of album
        $columns = array('id', 'title', 'alias', 'image', 'path', 'create');
        $order = array('create DESC', 'id DESC');
        $offset = (int)($page - 1) * $config['album_perpage'];
        $where = array('status' => 1, 'category' => $categoryId);
        $select = $this->getModel('album')->select()->columns($columns)->where($where)->order($order)->offset($offset)->limit(intval($config['album_perpage']));
        $rowset = $this->getModel('album')->selectWith($select);
        foreach ($rowset as $row) {
            $album[$row->id] = $row->toArray();
            $album[$row->id]['create'] = date('Y/m/d H:i:s', $album[$row->id]['create']);
            $album[$row->id]['mediumurl'] = Pi::url('/upload/' . $this->config('image_path') . '/medium/' . $album[$row->id]['path'] . '/' . $album[$row->id]['image']);
            $album[$row->id]['thumburl'] = Pi::url('/upload/' . $this->config('image_path') . '/thumb/' . $album[$row->id]['path'] . '/' . $album[$row->id]['image']);
            $album[$row->id]['url'] = $this->url('.gallery', array('module' => $params['module'], 'controller' => 'album', 'alias' => $album[$row->id]['alias']));
        }
        // Set paginator
        $select = $this->getModel('album')->select()->columns(array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)')))->where($where);
        $count = $this->getModel('album')->selectWith($select)->current()->count;
        $paginator = \Pi\Paginator\Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($config['album_perpage']);
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(array(
            'template' => $this->url('.gallery', array('module' => $params['module'], 'page' => '%page%')),
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
        $this->view()->headTitle(__('Gallery'));
        $this->view()->headDescription(__('Gallery for show images'), 'set');
        $this->view()->headKeywords(__('Gallery,images'), 'set');
        $this->view()->setTemplate('index_index');
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('config', $config);

        if (!empty($category)) {
            $this->view()->assign('category', $category);
        }
        if (!empty($subcategory)) {
            $this->view()->assign('subcategorizes', $subcategory);
        }
        if (!empty($album)) {
            $this->view()->assign('albums', $album);
        }
    }

}