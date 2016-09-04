<?php
/**
 * This source file is part of Xloit project.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License that is bundled with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * <http://www.opensource.org/licenses/mit-license.php>
 * If you did not receive a copy of the license and are unable to obtain it through the world-wide-web,
 * please send an email to <license@xloit.com> so we can send you a copy immediately.
 *
 * @license   MIT
 * @link      http://xloit.com
 * @copyright Copyright (c) 2016, Xloit. All rights reserved.
 */

namespace Xloit\Bridge\Zend\Locale\Resolver;

use Zend\Http\Request;

/**
 * A {@link CookieResolver} class.
 *
 * @since   0.0.1
 * @package Xloit\Bridge\Zend\Locale\Resolver
 */
class CookieResolver implements ResolverInterface
{
    /**
     *
     *
     * @since 0.0.1
     * @var string
     */
    const DEFAULT_COOKIE_NAME = 'xloit.locale';

    /**
     *
     *
     * @since 0.0.1
     * @var Request
     */
    protected $request;

    /**
     *
     *
     * @since 0.0.1
     * @var string
     */
    protected $cookieName;

    /**
     * Constructor to prevent {@link CookieResolver} from being loaded more than once.
     *
     * @since 0.0.1
     *
     * @param Request $request
     * @param string  $cookieName
     */
    public function __construct(Request $request, $cookieName = null)
    {
        $this->setRequest($request);

        if ($cookieName) {
            $this->setCookieName($cookieName);
        }
    }

    /**
     *
     *
     * @since 0.0.1
     * @return string
     */
    public function resolve()
    {
        /** @noinspection TypeUnsafeComparisonInspection */
        if (PHP_SAPI == 'cli') {
            // not supported on console
            return false;
        }

        /** @var $cookie \Zend\Http\Header\Cookie */
        $cookie     = $this->getRequest()->getCookie();
        $cookieName = $this->getCookieName();

        if (array_key_exists($cookieName, $cookie)) {
            return $cookie[$cookieName];
        }

        return null;
    }

    /**
     *
     *
     * @since 0.0.1
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     *
     *
     * @since 0.0.1
     *
     * @param Request $request
     *
     * @return static
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     *
     *
     * @since 0.0.1
     * @return string
     */
    public function getCookieName()
    {
        return $this->cookieName;
    }

    /**
     *
     *
     * @since 0.0.1
     *
     * @param string $cookieName
     *
     * @return static
     */
    public function setCookieName($cookieName)
    {
        $this->cookieName = $cookieName;

        return $this;
    }
}
