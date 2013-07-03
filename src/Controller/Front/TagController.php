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

class TagController extends ActionController
{
    public function indexAction()
    {
        /*
           * This is a sample page
           * This page view is like album view and show list of photos from selected tag
           * And list of photo id's needed from tag module
           * Paginator and photo array perhaps needed rewrite for use low memory
           */
        // Get info from url
        $alias = $this->params('alias');
        $module = $this->params('module');
        $page = $this->params('page', 1);
        // Check alias
        if (empty($alias)) {
            $this->jump(array('route' => '.gallery', 'module' => $module, 'controller' => 'index'), __('The tag not found.'));
        }
        // Get config
        $config = Pi::service('registry')->config->read($module);
        // Set offset
        $offset = (int)($page - 1) * $config['album_perpage'];
        // Get photo Id from tag module
        $tags = Pi::service('tag')->getList($module, $alias, null, $config['album_tags'], $offset);
        // Check alias
        if (empty($tags)) {
            $this->jump(array('route' => '.gallery', 'module' => $module, 'controller' => 'index'), __('The tag not found.'));
        }
        foreach ($tags as $tag) {
            $tagId[] = $tag['item'];
        }
        // Album id
        //$album  = array(); // allowed albums
        // Get list of photos
        $columns = array('id', 'title', 'alias', 'album', 'image', 'path', 'hits', 'comments', 'create', 'author');
        $order = array('create DESC', 'id DESC');
        $where = array('status' => 1, 'id' => $tagId, /* 'album' => $album*/);
        $select = $this->getModel('photo')->select()->columns($columns)->where($where)->order($order)->offset($offset)->limit(intval($config['album_perpage']));
        $rowset = $this->getModel('photo')->selectWith($select);
        foreach ($rowset as $row) {
            $photo[$row->id] = $row->toArray();
            $photo[$row->id]['create'] = date('Y/m/d H:i:s', $photo[$row->id]['create']);
            $photo[$row->id]['originalurl'] = Pi::url('/upload/' . $this->config('image_path') . '/original/' . $photo[$row->id]['path'] . '/' . $photo[$row->id]['image']);
            $photo[$row->id]['largeurl'] = Pi::url('/upload/' . $this->config('image_path') . '/large/' . $photo[$row->id]['path'] . '/' . $photo[$row->id]['image']);
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
	      'template' => $this->url('.gallery', array('module' => $module, 'controller' => 'tag', 'alias' => urlencode($alias) 'page' => '%page%')),
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
      $this->view()->headTitle($alias);
      $this->view()->headDescription($alias, 'set');
		$this->view()->headKeywords($alias, 'set');
      $this->view()->setTemplate('tag_index');
      $this->view()->assign('photos', $photo);
      $this->view()->assign('paginator', $paginator);
      $this->view()->assign('config', $config);
	}
}	