<?php
/**
 * Photo form
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

namespace Module\Gallery\Form;

use Pi;
use Pi\Form\Form as BaseForm;

class PhotoForm extends BaseForm
{
    protected $options;

    public function __construct($name = null, $options = array())
    {
        $this->module = $options['module'];
        $this->imageurl = $options['imageurl'];

        parent::__construct($name);
    }

    public function getInputFilter()
    {
        if (!$this->filter) {
            $this->filter = new PhotoFilter;
        }
        return $this->filter;
    }

    public function init()
    {
        // id
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type' => 'hidden',
            ),
        ));
        // title
        $this->add(array(
            'name' => 'title',
            'options' => array(
                'label' => __('Title'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // alias
        $this->add(array(
            'name' => 'alias',
            'options' => array(
                'label' => __('Alias'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // album
        $this->add(array(
            'name' => 'album',
            'type' => 'Module\Gallery\Form\Element\Album',
            'options' => array(
                'label' => __('Album'),
                'module' => $this->module,
            ),
        ));
        // information	  
        $this->add(array(
            'name' => 'information',
            'options' => array(
                'label' => __('Information'),
            ),
            'attributes' => array(
                'type' => 'textarea',
                'rows' => '5',
                'cols' => '40',
                'description' => '',
            )
        ));
        // tag
        if (Pi::service('module')->isActive('tag')) {
            $this->add(array(
                'name' => 'tag',
                'options' => array(
                    'label' => __('Tags'),
                ),
                'attributes' => array(
                    'type' => 'text',
                    'description' => '',
                )
            ));
        }
        // keywords
        $this->add(array(
            'name' => 'keywords',
            'options' => array(
                'label' => __('Keywords'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // description
        $this->add(array(
            'name' => 'description',
            'options' => array(
                'label' => __('Description'),
            ),
            'attributes' => array(
                'type' => 'text',
                'description' => '',
            )
        ));
        // link
        $this->add(array(
            'name' => 'link',
            'options' => array(
                'label' => __('Link'),
            ),
            'attributes' => array(
                'type' => 'url',
                'description' => '',
            )
        ));
        // Image
        if (isset($this->imageurl)) {
            $this->add(array(
                'name' => 'imageview',
                'options' => array(
                    'label' => __('Image'),
                ),
                'attributes' => array(
                    'type' => 'image',
                    'src' => $this->imageurl,
                    'height' => '200',
                    'disabled' => true,
                    'description' => '',
                )
            ));
            // id
            $this->add(array(
                'name' => 'image',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            ));
        } else {
            $this->add(array(
                'name' => 'image',
                'options' => array(
                    'label' => __('Image'),
                ),
                'attributes' => array(
                    'type' => 'file',
                    'description' => '',

                )
            ));
            $this->add(array(
                'name' => 'default',
                'type' => 'checkbox',
                'options' => array(
                    'label' => __('Default image'),
                ),
                'attributes' => array(
                    'description' => __('Use as default image in album and category'),
                )
            ));
        }
        // status
        $this->add(array(
            'name' => 'status',
            'type' => 'select',
            'options' => array(
                'label' => __('Status'),
                'value_options' => array(
                    1 => __('Published'),
                    2 => __('Pending review'),
                    3 => __('Draft'),
                    4 => __('Private'),
                ),
            ),
        ));
        // Save
        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'value' => __('Submit'),
            )
        ));
    }
}