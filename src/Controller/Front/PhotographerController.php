<?php
/**
 * Gallery photographer controller
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

class PhotographerController extends ActionController
{

    public function indexAction()
    {
        // Get info
        $select = $this->getModel('photographer')->select()->order(array('count DESC'));
        $rowset = $this->getModel('photographer')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = $row->toArray();
            $user = Pi::model('user_account')->find($row->author);
            $list[$row->id]['identity'] = $user->identity;
        }
        // Set view
        $this->view()->headTitle(__('List of all photographers'));
        $this->view()->headDescription(__('List of all photographers'), 'set');
        $this->view()->headKeywords(__('List,photographers'), 'set');
        $this->view()->setTemplate('photographer_index');
        $this->view()->assign('photographers', $list);
    }

    public function profileAction()
    {
        // Get params
        $alias = $this->params('alias');
        $module = $this->params('module');
        $page = $this->params('page', 1);
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Find user
        $user = Pi::model('user_account')->find($alias, 'identity')->toArray();
        unset($user['credential'], $user['salt'], $user['active']);
        $user['avatar'] = Pi::url('static/avatar/avatar.jpg');
        $title = sprintf(__('All photos from %s'), $user['identity']);
        // Get story
        $columns = array('id', 'title', 'alias', 'album', 'image', 'path', 'hits', 'comments', 'create', 'author');
        $order = array('create DESC', 'id DESC');
        $offset = (int)($page - 1) * $config['album_perpage'];
        $where = array('status' => 1, 'author' => $user['id']);
        $select = $this->getModel('photo')->select()->columns($columns)->where($where)->order($order)->offset($offset)->limit(intval($config['album_perpage']));
        $rowset = $this->getModel('photo')->selectWith($select);
        foreach ($rowset as $row) {
            $photo[$row->id] = $row->toArray();
            $photo[$row->id]['mediumurl'] = Pi::url('/upload/' . $this->config('image_path') . '/medium/' . $photo[$row->id]['path'] . '/' . $photo[$row->id]['image']);
            $photo[$row->id]['thumburl'] = Pi::url('/upload/' . $this->config('image_path') . '/thumb/' . $photo[$row->id]['path'] . '/' . $photo[$row->id]['image']);
            $photo[$row->id]['url'] = $this->url('.gallery', array('module' => $module, 'controller' => 'photo', 'id' => $photo[$row->id]['id']));
        }
        // Set paginator
        $select = $this->getModel('photo')->select()->columns(array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)')))->where($where);
        $count = $this->getModel('photo')->selectWith($select)->current()->count;
        $paginator = \Pi\Paginator\Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($config['album_perpage']);
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(array(
            'template' => $this->url('.gallery', array('module' => $module, 'controller' => 'photographer', 'action' => 'profile', 'alias' => $alias, 'page' => '%page%')),
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
        $this->view()->headTitle($title);
        $this->view()->headKeywords($title, 'set');
        $this->view()->headKeywords(__('photo,photographer,') . $user['identity'], 'set');
        $this->view()->setTemplate('photographer_profile');
        $this->view()->assign('user', $user);
        $this->view()->assign('photos', $photo);
        $this->view()->assign('paginator', $paginator);
        $this->view()->assign('config', $config);
    }

}