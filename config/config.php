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
    'category' => array(
        array(
            'title' => __('Admin'),
            'name' => 'admin'
        ),
        array(
            'title' => __('Show Photo'),
            'name' => 'photo'
        ),
        array(
            'title' => __('Show Album'),
            'name' => 'album'
        ),
        array(
            'title' => __('Feed'),
            'name' => 'feed'
        ),
        array(
            'title' => __('Image'),
            'name' => 'image'
        ),
        array(
            'title' => __('Social'),
            'name' => 'social'
        ),
        array(
            'title' => __('List of images'),
            'name' => 'list'
        ),
        array(
            'title' => __('Vote'),
            'name' => 'vote'
        ),
    ),
    'item' => array(
        // Generic
        'advertisement' => array(
            'title' => __('Advertisement'),
            'description' => '',
            'edit' => 'textarea',
            'filter' => 'string',
            'value' => ''
        ),
        // Admin
        'admin_perpage' => array(
            'category' => 'admin',
            'title' => __('Perpage'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 12
        ),
        // Album
        'album_perpage' => array(
            'category' => 'album',
            'title' => __('Perpage'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 20
        ),
        'album_columns' => array(
            'title' => __('Columns'),
            'description' => '',
            'edit' => array(
                'type' => 'select',
                'options' => array(
                    'options' => array(
                        'span12' => __('1 column'),
                        'span6' => __('2 columns'),
                        'span4' => __('3 columns'),
                        'span3' => __('4 columns'),
                        'span2' => __('6 columns'),
                        'span1' => __('12 columns'),
                    ),
                ),
            ),
            'filter' => 'string',
            'value' => 'span3',
            'category' => 'album',
        ),
        'album_tags' => array(
            'category' => 'album',
            'title' => __('Tags'),
            'description' => __('Number of tags in tag controller'),
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 50
        ),
        'album_title' => array(
            'category' => 'album',
            'title' => __('Show Title'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        // Photo
        'photo_album' => array(
            'category' => 'photo',
            'title' => __('Show Album'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'photo_author' => array(
            'category' => 'photo',
            'title' => __('Show Author'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'photo_date' => array(
            'category' => 'photo',
            'title' => __('Show Date'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'photo_hits' => array(
            'category' => 'photo',
            'title' => __('Show Hits'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'photo_nav' => array(
            'category' => 'photo',
            'title' => __('Show Nav'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'photo_coms' => array(
            'category' => 'photo',
            'title' => __('Show Comments'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'photo_download' => array(
            'category' => 'photo',
            'title' => __('Show Download'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'photo_title' => array(
            'category' => 'photo',
            'title' => __('Show Title'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'photo_information' => array(
            'category' => 'photo',
            'title' => __('Show Information'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        // Feed 
        'feed_icon' => array(
            'category' => 'feed',
            'title' => __('Show feed icon'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'feed_num' => array(
            'category' => 'feed',
            'title' => __('Feed number'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 10
        ),
        // Image
        'image_size' => array(
            'category' => 'image',
            'title' => __('Image Size'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 10000000
        ),
        'image_path' => array(
            'category' => 'image',
            'title' => __('Image path'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'string',
            'value' => 'gallery'
        ),
        'image_extension' => array(
            'category' => 'image',
            'title' => __('Image Extension'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'string',
            'value' => 'jpg,png,gif'
        ),
        'image_largeh' => array(
            'category' => 'image',
            'title' => __('Large Image height'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 1200
        ),
        'image_largew' => array(
            'category' => 'image',
            'title' => __('Large Image width'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 1200
        ),
        'image_mediumh' => array(
            'category' => 'image',
            'title' => __('Medium Image height'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 600
        ),
        'image_mediumw' => array(
            'category' => 'image',
            'title' => __('Medium Image width'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 600
        ),
        'image_thumbh' => array(
            'category' => 'image',
            'title' => __('Thumb Image height'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 200
        ),
        'image_thumbw' => array(
            'category' => 'image',
            'title' => __('Thumb Image width'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 200
        ),
        // Social
        'social_bookmark' => array(
            'category' => 'social',
            'title' => __('Show Bookmark'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'social_gplus' => array(
            'category' => 'social',
            'title' => __('Show Google Plus'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'social_facebook' => array(
            'category' => 'social',
            'title' => __('Show facebook'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'social_twitter' => array(
            'category' => 'social',
            'title' => __('Show twitter'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        // List 
        'list_lastphoto' => array(
            'category' => 'list',
            'title' => __('Show last photo-bar'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'list_randomphoto' => array(
            'category' => 'list',
            'title' => __('Show random photo-bar'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 0
        ),
        'list_topphoto' => array(
            'category' => 'list',
            'title' => __('Show top photo-bar'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 0
        ),
        'list_barnumber' => array(
            'category' => 'list',
            'title' => __('Number of images in photo-bar'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 4
        ),
        'list_topday' => array(
            'category' => 'list',
            'title' => __('Select top photos in last X days'),
            'description' => '',
            'edit' => 'text',
            'filter' => 'number_int',
            'value' => 30
        ),
        // Vote
        'vote_bar' => array(
            'category' => 'vote',
            'title' => __('Use vote system'),
            'description' => '',
            'edit' => 'checkbox',
            'filter' => 'number_int',
            'value' => 1
        ),
        'vote_type' => array(
            'category' => 'vote',
            'title' => __('VoteBar type'),
            'description' => '',
            'filter' => 'string',
            'value' => 'plus',
            'edit' => array(
                'type' => 'select',
                'options' => array(
                    'options' => array(
                        'plus' => __('Plus'),
                        'star' => __('Star'),
                    ),
                ),
            ),
        ),
    ),
);