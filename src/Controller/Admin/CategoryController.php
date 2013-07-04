<?php
/**
 * Gallery admin category controller
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
use Module\Gallery\Form\CategoryForm;
use Module\Gallery\Form\CategoryFilter;

class CategoryController extends ActionController
{
    protected $categoryColumns = array(
        'id', 'pid', 'title', 'alias', 'information', 'keywords',
        'description', 'image', 'path', 'create', 'order', 'status'
    );

    public function indexAction()
    {
        // Get page
        $page = $this->params('p', 1);
        // Get info
        $columns = array('id', 'pid', 'title', 'alias', 'create', 'status');
        $select = $this->getModel('category')->select()->columns($columns)->order(array('create DESC', 'id DESC'));
        $rowset = $this->getModel('category')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $category[$row->id] = $row->toArray();
            $category[$row->id]['create'] = date('Y/m/d H:i:s', $category[$row->id]['create']);
        }
        // Set paginator
        $paginator = \Pi\Paginator\Paginator::factory($category);
        $paginator->setItemCountPerPage($this->config('admin_perpage'));
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(array(
            // Use router to build URL for each page
            'pageParam' => 'p',
            'totalParam' => 't',
            'router' => $this->getEvent()->getRouter(),
            'route' => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'params' => array(
                'module' => $this->getModule(),
                'controller' => 'category',
                'action' => 'index',
            ),
        ));
        // Set view
        $this->view()->setTemplate('category_index');
        $this->view()->assign('categories', $paginator);

    }

    public function updateAction()
    {
        // Get id
        $id = $this->params('id');
        $form = new CategoryForm('category', $this->getModule());
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setInputFilter(new CategoryFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                foreach (array_keys($values) as $key) {
                    if (!in_array($key, $this->categoryColumns)) {
                        unset($values[$key]);
                    }
                }
                // Set keywords
                $keywords = ($values['keywords']) ? $values['keywords'] : $values['title'];
                $values['keywords'] = Pi::service('api')->gallery(array('Text', 'keywords'), $keywords);
                // Set description
                $description = ($values['description']) ? $values['description'] : $values['title'];
                $values['description'] = Pi::service('api')->gallery(array('Text', 'description'), $description);
                // Set alias
                $alias = ($values['alias']) ? $values['alias'] : $values['title'];
                $values['alias'] = Pi::service('api')->gallery(array('Text', 'alias'), $alias, $values['id'], $this->getModel('category'));
                // Set if new
                if (empty($values['id'])) {
                    // Set time
                    $values['create'] = time();
                    // Set order
                    $select = $this->getModel('category')->select()->columns(array('order'))->order(array('order DESC'))->limit(1);
                    $lastrow = $this->getModel('category')->selectWith($select);
                    $values['order'] = $lastrow + 1;
                }
                // Save values
                if (!empty($values['id'])) {
                    $row = $this->getModel('category')->find($values['id']);
                } else {
                    $row = $this->getModel('category')->createRow();
                }
                $row->assign($values);
                $row->save();
                // Check it save or not
                if ($row->id) {
                    $message = __('Category data saved successfully.');
                    $class = 'alert-success';
                    $this->jump(array('action' => 'index'), $message);
                } else {
                    $message = __('Category data not saved.');
                    $class = 'alert-error';
                }
            } else {
                $message = __('Invalid data, please check and re-submit.');
                $class = 'alert-error';
            }
        } else {
            if ($id) {
                $values = $this->getModel('category')->find($id)->toArray();
                $form->setData($values);
                $message = 'You can edit this Category';
                $class = '';
            } else {
                $message = 'You can add new Category';
                $class = '';
            }
        }
        // Set view
        $this->view()->setTemplate('category_update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add a Category'));
        $this->view()->assign('message', $message);
        $this->view()->assign('class', $class);
    }

    public function deleteAction()
    {

    }
}