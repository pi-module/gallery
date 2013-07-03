<?php
/**
 * Index route implementation
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
 * @author          Taiwen Jiang <taiwenjiang@tsinghua.org.cn>
 * @author          Hossein Azizabadi <azizabadi@faragostaresh.com>
 * @since           3.0
 * @package         Module\Gallery
 * @subpackage      Route
 * @version         $Id$
 */

namespace Module\Gallery\Route;

use Pi\Mvc\Router\Http\Standard;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\RequestInterface as Request;

/**
 * sample url
 *
 */
class Gallery extends Standard
{
    protected $prefix = '/gallery';

    /**
     * Default values.
     *
     * @var array
     */
    protected $defaults = array(
        'module' => 'gallery',
        'controller' => 'index',
        'action' => 'index',
    );

    /**
     * match(): defined by Route interface.
     *
     * @see    Route::match()
     * @param  Request $request
     * @return RouteMatch
     */
    public function match(Request $request, $pathOffset = null)
    {
        $result = $this->canonizePath($request, $pathOffset);
        if (null === $result) {
            return null;
        }
        list($path, $pathLength) = $result;
        if (empty($path)) {
            return null;
        }

        // Get path
        $controller = explode($this->paramDelimiter, $path, 2);

        // Set controller
        if (isset($controller[0]) && in_array($controller[0], array('album', 'category', 'index', 'management', 'photo', 'photographer', 'tag'))) {
            $matches['controller'] = urldecode($controller[0]);
        } elseif (isset($controller[0]) && $controller[0] == 'page') {
            $matches['page'] = intval($controller[1]);
            $matches['controller'] = 'index';
        }

        // Make Match
        if (isset($matches['controller'])) {
            switch ($matches['controller']) {
                case 'category':
                    $matches['controller'] = 'index';
                    if (!empty($controller[1])) {
                        $categoryPath = explode($this->paramDelimiter, $controller[1], 2);
                        if (!is_numeric($categoryPath[0])) {
                            $matches['alias'] = urldecode($categoryPath[0]);
                        } else {
                            $matches['id'] = intval($categoryPath[0]);
                        }
                        if (isset($categoryPath[1]) && $categoryPath[1] == 'page') {
                            $matches['page'] = intval($categoryPath[2]);
                        }
                    }
                    break;

                case 'album':
                    if (!empty($controller[1])) {
                        $albumPath = explode($this->paramDelimiter, $controller[1], 3);
                        if (!is_numeric($albumPath[0])) {
                            $matches['alias'] = urldecode($albumPath[0]);
                        } else {
                            $matches['id'] = intval($albumPath[0]);
                        }
                        if (isset($albumPath[1]) && $albumPath[1] == 'page') {
                            $matches['page'] = intval($albumPath[2]);
                        }
                    }
                    break;

                case 'photo':
                    if (!empty($controller[1])) {
                        $photoPath = explode($this->paramDelimiter, $controller[1], 2);
                        if ($photoPath[0] == 'download') {
                            $matches['action'] = 'download';
                            if (!is_numeric($photoPath[1])) {
                                $matches['alias'] = urldecode($photoPath[1]);
                            } else {
                                $matches['id'] = intval($photoPath[1]);
                            }
                        } elseif ($photoPath[0] == 'send') {
                            $matches['action'] = 'send';
                            if (!is_numeric($photoPath[1])) {
                                $matches['alias'] = urldecode($photoPath[1]);
                            } else {
                                $matches['id'] = intval($photoPath[1]);
                            }
                        } else {
                            if (!is_numeric($photoPath[0])) {
                                $matches['alias'] = urldecode($photoPath[0]);
                            } else {
                                $matches['id'] = intval($photoPath[0]);
                            }
                        }

                    }
                    break;

                case 'photographer':
                    if (!empty($controller[1])) {
                        $photographerPath = explode($this->paramDelimiter, $controller[1], 4);
                        if ($photographerPath[0] == 'profile') {
                            $matches['action'] = 'profile';
                            $matches['alias'] = urldecode($photographerPath[1]);
                            if (isset($photographerPath[2]) && $photographerPath[2] == 'page') {
                                $matches['page'] = intval($photographerPath[3]);
                            }
                        } else {
                            if (isset($photographerPath[0]) && $photographerPath[0] == 'page') {
                                $matches['page'] = intval($photographerPath[1]);
                            }
                        }

                    }
                    break;

                case 'management':
                    if (!empty($controller[1])) {
                        $managementPath = explode($this->paramDelimiter, $controller[1]);
                        if ($managementPath[0] == 'submit') {
                            $matches['action'] = 'submit';
                        } elseif ($managementPath[0] == 'delete') {
                            $matches['action'] = 'delete';
                        } elseif ($managementPath[0] == 'page') {
                            $matches['page'] = intval($managementPath[1]);
                        }
                    }
                    break;

                case 'tag':
                    if (!empty($controller[1])) {
                        $tagPath = explode($this->paramDelimiter, $controller[1]);
                        $matches['alias'] = urldecode($tagPath[0]);
                        if (isset($tagPath[1]) && $tagPath[1] == 'page') {
                            $matches['page'] = intval($tagPath[2]);
                        }
                    }
                    break;
            }
        }
        return new RouteMatch(array_merge($this->defaults, $matches), $pathLength);
    }

    /**
     * assemble(): Defined by Route interface.
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     */
    public function assemble(array $params = array(), array $options = array())
    {
        $mergedParams = array_merge($this->defaults, $params);
        if (!$mergedParams) {
            return $this->prefix;
        }

        if (!empty($mergedParams['module'])) {
            $url['module'] = $mergedParams['module'];
        }
        if (!empty($mergedParams['controller']) && $mergedParams['controller'] != 'index') {
            $url['controller'] = $mergedParams['controller'];
        }
        if (!empty($mergedParams['action']) && $mergedParams['action'] != 'index') {
            $url['action'] = $mergedParams['action'];
        }
        if (!empty($mergedParams['alias'])) {
            $url['alias'] = $mergedParams['alias'];
        }
        if (!empty($mergedParams['id'])) {
            $url['id'] = $mergedParams['id'];
        }
        if (!empty($mergedParams['page'])) {
            $url['page'] = 'page' . $this->paramDelimiter . $mergedParams['page'];
        }

        $url = implode($this->paramDelimiter, $url);

        if (empty($url)) {
            return $this->prefix;
        }
        return $this->paramDelimiter . $url;
    }
}