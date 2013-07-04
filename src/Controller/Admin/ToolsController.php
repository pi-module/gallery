<?php
/**
 * Gallery admin tools controller
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
use Module\Gallery\Form\RebuildForm;

class ToolsController extends ActionController
{
    public function indexAction()
    {
        $form = new RebuildForm('rebuild');
        $class = '';
        $message = __('You can rebuild all your added photos, after rebuild all your old data update to new.
		                 And you must set start and end publish time.');
        if ($this->request->isPost()) {
            // Set form date
            $values = $this->request->getPost();
            // Get all story
            $where = array('create > ?' => strtotime($values['start']), 'create < ?' => strtotime($values['end']));
            $columns = array('id', 'title', 'alias', 'keywords', 'description');
            $order = array('id ASC');
            $select = $this->getModel('photo')->select()->where($where)->columns($columns)->order($order);
            $rowset = $this->getModel('photo')->selectWith($select);
            // Do rebuild
            switch ($values['rebuild']) {
                case 'alias':
                    foreach ($rowset as $row) {
                        $values['alias'] = Pi::service('api')->gallery(array('Text', 'alias'), $row->title, $row->id, $this->getModel('photo'));
                        $this->getModel('photo')->update(array('alias' => $alias), array('id' => $row->id));
                    }
                    $message = __('Finish Rebuild Alias, all story alias update');
                    break;

                case 'keywords':
                    foreach ($rowset as $row) {
                        $keywords = Pi::service('api')->gallery(array('Text', 'keywords'), $row->title);
                        $this->getModel('photo')->update(array('keywords' => $keywords), array('id' => $row->id));
                    }
                    $message = __('Finish Rebuild Meta keywords, all story Meta keywords update');
                    break;

                case 'description':
                    foreach ($rowset as $row) {
                        $description = Pi::service('api')->gallery(array('Text', 'description'), $row->title);
                        $this->getModel('photo')->update(array('description' => $description), array('id' => $row->id));
                    }
                    $message = __('Finish Rebuild Meta description, all story Meta description update');
                    break;
            }
            // Set class
            $class = 'alert-success';
        }
        $this->view()->setTemplate('tools_rebuild');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Rebuild stores'));
        $this->view()->assign('message', $message);
        $this->view()->assign('class', $class);
    }
}