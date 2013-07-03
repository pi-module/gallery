<?php
/**
 * Gallery module config
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

return array(
// Front section
    'front' => array(
        array(
            'title' => __('Global page'),
            'block' => 1,
        ),
        array(
            'title' => __('Index page'),
            'controller' => 'index',
            'block' => 1,
        ),
        array(
            'title' => __('Album page'),
            'controller' => 'album',
            'block' => 1,
        ),
        array(
            'title' => __('Photo page'),
            'controller' => 'photo',
            'block' => 1,
        ),
        array(
            'title' => __('Submit page'),
            'controller' => 'submit',
            'block' => 1,
        ),
        array(
            'title' => __('Photographer page'),
            'controller' => 'photographer',
            'block' => 1,
        ),
    ),
// Admin section
    'admin' => array(

    ),
// Feed section
    'feed' => array(
        array(
            'cache_expire' => 0,
            'cache_level' => '',
            'title' => __('Recent Photos'),
            'controller' => 'photo',
        ),
        array(
            'cache_expire' => 0,
            'cache_level' => '',
            'title' => __('Recent Album'),
            'controller' => 'album',
        ),
    ),
);
