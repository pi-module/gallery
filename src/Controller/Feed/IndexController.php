<?php
/**
 * Gallery feed index controller
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

class Gallery_IndexController extends Pi_Zend_Controller_Action_Feed
{

    public function indexAction()
    {
        // Set title
        $this->feed('title', _r('Recent Photos'));
        // Set model
        $photo_model = \Pi::service('module')->model('photo', 'gallery');
        // Get feed limit
        $limit = \App\Gallery\General::ModuleConfigs('feed', 'feed_num');
        // Get feeds
        $rowset = $photo_model->Photo_Rss($limit);
        // Make feed array
        foreach ($rowset as $row) {
            $entry = array();
            $entry['title'] = $row->title;
            $entry['description'] = $row->information;
            $entry['lastUpdate'] = $row->create;
            $entry['link'] = \App\Gallery\General::PhotoUrl($row->alias);
            $this->entry($entry);
        }
    }

}