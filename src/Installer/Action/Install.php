<?php
/**
 * Pi module installer action
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
 * @subpackage      Installer
 * @version         $Id$
 */

namespace Module\Gallery\Installer\Action;
use Pi;
use Pi\Application\Installer\Action\Install as BasicInstall;
use Zend\EventManager\Event;

class Install extends BasicInstall
{
    protected function attachDefaultListeners()
    {
        $events = $this->events;
        $events->attach('install.pre', array($this, 'preInstall'), 1000);
        $events->attach('install.post', array($this, 'postInstall'), 1);
        parent::attachDefaultListeners();
        return $this;
    }

    public function preInstall(Event $e)
    {
        $result = array(
            'status' => true,
            'message' => sprintf('Called from %s', __METHOD__),
        );
        $e->setParam('result', $result);
    }

    public function postInstall(Event $e)
    {
        // Make path
        $dir = Pi::path('upload/' . $e->getParam('module'));
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
            chmod($dir, 0777);
        }

        // Set model
        $categoryModel = Pi::model($module = $e->getParam('module') . '/category');
        $albumModel = Pi::model($module = $e->getParam('module') . '/album');

        // Add category
        $categoryData = array(
            'id' => 1,
            'title' => __('Default Category'),
            'alias' => __('default-category'),
            'information' => __('default category for test'),
            'keywords' => __('default,category'),
            'description' => __('default-category'),
            'create' => time(),
            'order' => 1,
            'status' => 1,
        );
        $categoryModel->insert($categoryData);

        // Add album
        $albumData = array(
            'id' => 1,
            'category' => 1,
            'title' => __('Default Album'),
            'alias' => __('default-album'),
            'information' => __('Default Album for test'),
            'keywords' => __('default,album'),
            'description' => __('default album'),
            'create' => time(),
            'author' => Pi::registry('user')->id,
            'order' => 1,
            'status' => 1,
        );
        $albumModel->insert($albumData);

        // Result
        $result = array(
            'status'    => true,
            'message'   => __('Default category and album added.'),
        );
        $this->setResult('post-install', $result);
    }
}