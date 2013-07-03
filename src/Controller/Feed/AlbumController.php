<?php
/**
 * Gallery feed album controller
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

class Gallery_AlbumController extends Pi_Zend_Controller_Action_Feed
{

    public function indexAction()
    {
        // Set title
        $this->feed('title', _r('Recent Albums'));
        // Set model
        $album_model = \Pi::service('module')->model('album', 'gallery');
        // Get feed limit
        $limit = \App\Gallery\General::ModuleConfigs('feed', 'feed_num');
        // Get feeds
        $rowset = $album_model->Album_Rss($limit);
        // Make feed array
        foreach ($rowset as $row) {
            $entry = array();
            $entry['title'] = $row->title;
            $entry['description'] = $row->information;
            $entry['lastUpdate'] = $row->create;
            $entry['link'] = \App\Gallery\General::AlbumUrl($row->alias);
            $this->entry($entry);
        }
    }

}