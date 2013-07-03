<?php
/**
 * Gallery module Album class
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
 * Pi::service('api')->gallery(array('Album', 'List'), $album, $count);
 */

class Album extends AbstractApi
{
    /*
      * List of images in album
      */
	public function List($album, $count = 4) 
	{
		$path = 'upload/gallery';
		$columns = array('id', 'title', 'alias', 'image', 'path');
		$where = array('album' => $album, 'status' => 1);
		$order = array('create DESC','id DESC');
		$select = Pi::model('photo', $this->getModule())->select()->columns($columns)->where($where)->order($order)->limit(intval($count));
      $rowset = Pi::model('photo', $this->getModule())->selectWith($select);
      foreach ($rowset as $row) {
			$photo[$row->id] = $row->toArray();
			$photo[$row->id]['mediumurl'] = Pi::url($path . '/medium/' . $photo[$row->id]['path'] . '/' .  $photo[$row->id]['image']);
		   $photo[$row->id]['thumburl'] = Pi::url($path . '/thumb/' . $photo[$row->id]['path'] . '/' .  $photo[$row->id]['image']);
		   $photo[$row->id]['url'] = Pi::url('gallery/photo/' . $photo[$row->id]['id']);
		}

return $photo;
}
}