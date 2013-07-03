<?php
/**
 * Gallery module Photo class
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
 * Pi::service('api')->gallery(array('Photo', 'Last'), $count);
 * Pi::service('api')->gallery(array('Photo', 'Top'), $count, $day);
 * Pi::service('api')->gallery(array('Photo', 'Random'), $count);
 */

class Photo extends AbstractApi
{
    /*
      * List of last images
      */
    public function Last($count)
    {
        return $this->GetImage($count, array('create DESC', 'id DESC'));
    }

    /*
      * List of Top images
      */
    public function Top($count, $day = 7)
    {
        return $this->GetImage($count, array('hits DESC', 'id DESC'), array('`create` > ?' => 86400 * $day));
    }

    /*
      * List of Random images
      */
    public function Random($count)
    {
        return $this->GetImage($count, array(new \Zend\Db\Sql\Predicate\Expression('RAND()')));
    }

    /*
      * List of alowed albums
      */
    protected function GetImage($count = 4, $order, $where = array())
    {
        $path = 'upload/gallery';
        $columns = array('id', 'title', 'alias', 'image', 'path');
        //$where['album'] = $this->Album();
        $where['status'] = 1;
        $select = Pi::model('photo', $this->getModule())->select()->columns($columns)->where($where)->order($order)->limit(intval($count));
        $rowset = Pi::model('photo', $this->getModule())->selectWith($select);
        foreach ($rowset as $row) {
            $photo[$row->id] = $row->toArray();
            $photo[$row->id]['mediumurl'] = Pi::url($path . '/medium/' . $photo[$row->id]['path'] . '/' . $photo[$row->id]['image']);
            $photo[$row->id]['thumburl'] = Pi::url($path . '/thumb/' . $photo[$row->id]['path'] . '/' . $photo[$row->id]['image']);
            $photo[$row->id]['url'] = Pi::url('gallery/photo/' . $photo[$row->id]['id']);
        }
        return $photo;
    }

    /*
      * List of alowed albums
      */
    protected function Album()
    {
        /* Need acl system */
    }
}