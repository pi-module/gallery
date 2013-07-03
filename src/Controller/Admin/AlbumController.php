<?php
/**
 * Gallery admin album controller
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
use Module\Gallery\Form\AlbumForm;
use Module\Gallery\Form\AlbumFilter;

class AlbumController extends ActionController
{
    protected $albumColumns = array(
        'id', 'category', 'title', 'alias', 'information', 'keywords', 'description',
        'image', 'path', 'create', 'author', 'order', 'status', 'photo'
    );

    public function indexAction()
    {
        // Get page
        $page = $this->params('p', 1);
        // Set params
        $params = array(
            'module' => $this->getModule(),
            'controller' => 'album',
            'action' => 'index',
        );
        // Set where
        $where = array();
        //  Get category
        $category = $this->params('category');
        if (!empty($category)) {
            $where['category'] = $category;
            $params['category'] = $category;
            $this->view()->assign('back', 1);
        }
        //  Get author
        $author = $this->params('author');
        if (!empty($author)) {
            $where['author'] = $author;
            $params['author'] = $author;
            $this->view()->assign('back', 1);
        }
        // Get category list
        $select = $this->getModel('category')->select()->columns(array('id', 'title', 'alias'))->order(array('id DESC'));
        $rowset = $this->getModel('category')->selectWith($select);
        // Make category list
        foreach ($rowset as $row) {
            $categoryList[$row->id] = $row->toArray();
        }
        // Get info
        $columns = array('id', 'category', 'title', 'alias', 'create', 'author', 'status', 'photo');
        $offset = (int)($page - 1) * $this->config('admin_perpage');
        $select = $this->getModel('album')->select()->where($where)->columns($columns)->offset($offset)->order(array('create DESC', 'id DESC'))->limit(intval($this->config('admin_perpage')));
        $rowset = $this->getModel('album')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $album[$row->id] = $row->toArray();
            $user = Pi::model('user_account')->find($album[$row->id]['author'])->toArray();
            $album[$row->id]['categoryid'] = $categoryList[$row->category]['id'];
            $album[$row->id]['categorytitle'] = $categoryList[$row->category]['title'];
            $album[$row->id]['identity'] = $user['identity'];
            $album[$row->id]['create'] = date('Y/m/d H:i:s', $album[$row->id]['create']);
        }
        // Set paginator
        $select = $this->getModel('album')->select()->columns(array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)')))->where($where);
        $count = $this->getModel('album')->selectWith($select)->current()->count;
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
        $this->view()->setTemplate('album_index');
        $this->view()->assign('albums', $album);
        $this->view()->assign('paginator', $paginator);

    }

    public function updateAction()
    {
        // Get id
        $id = $this->params('id');
        $form = new AlbumForm('album', $this->getModule());
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setInputFilter(new AlbumFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                foreach (array_keys($values) as $key) {
                    if (!in_array($key, $this->albumColumns)) {
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
                $values['alias'] = $this->alias($alias, $values['id'], $this->getModel('album'));
                // Set if new
                if (empty($values['id'])) {
                    // Set time
                    $values['create'] = time();
                    // Set user
                    $values['author'] = Pi::registry('user')->id;
                    // Set order
                    $select = $this->getModel('album')->select()->columns(array('order'))->order(array('order DESC'))->limit(1);
                    $lastrow = $this->getModel('album')->selectWith($select);
                    $values['order'] = $lastrow + 1;
                }
                // Save values
                if (!empty($values['id'])) {
                    $row = $this->getModel('album')->find($values['id']);
                } else {
                    $row = $this->getModel('album')->createRow();
                }
                $row->assign($values);
                $row->save();
                // Check it save or not
                if ($row->id) {
                    $message = __('Album data saved successfully.');
                    $class = 'alert-success';
                    $this->jump(array('action' => 'index'), $message);
                } else {
                    $message = __('Album data not saved.');
                    $class = 'alert-error';
                }
            } else {
                $message = __('Invalid data, please check and re-submit.');
                $class = 'alert-error';
            }
        } else {
            if ($id) {
                $values = $this->getModel('album')->find($id)->toArray();
                $form->setData($values);
                $message = 'You can edit this album';
                $class = '';
            } else {
                $message = 'You can add new album';
                $class = '';
            }
        }
        // Set view
        $this->view()->setTemplate('album_update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add a Album'));
        $this->view()->assign('message', $message);
        $this->view()->assign('class', $class);
    }

    public function deleteAction()
    {

    }
}