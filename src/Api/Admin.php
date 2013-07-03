<?php
/**
 * Gallery module admin class
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

namespace Module\Gallery\Api;

use Pi;
use Pi\Application\AbstractApi;

/*
 * Pi::service('api')->gallery(array('Admin', 'Image'), $album, $image, $path);
 */

class Admin extends AbstractApi
{
    /*
      * Add / update album and category image
      */
    public function Image($id, $image, $path)
    {
        Pi::model('album', $this->getModule())->update(array('image' => $image, 'path' => $path), array('id' => $id));
        $album = Pi::model('album', $this->getModule())->find($id)->toArray();
        Pi::model('category', $this->getModule())->update(array('image' => $image, 'path' => $path), array('id' => $album['category']));
    }
}