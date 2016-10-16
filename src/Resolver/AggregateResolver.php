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

use Countable;
use IteratorAggregate;
use Traversable;
use Zend\Stdlib\PriorityQueue;

/**
 * An {@link AggregateResolver} class.
 *
 * @package Xloit\Bridge\Zend\Locale\Resolver
 */
class AggregateResolver implements Countable, IteratorAggregate, ResolverInterface
{
    /**
     *
     *
     * @var string
     */
    const FAILURE_NO_RESOLVERS = 'AggregateResolver::FailureNoResolvers';

    /**
     *
     *
     * @var string
     */
    const FAILURE_NOT_FOUND = 'AggregateResolver::FailureNotFound';

    /**
     * Last lookup failure
     *
     * @var false|string
     */
    protected $lastFailure = false;

    /**
     *
     *
     * @var ResolverInterface
     */
    protected $lastSuccessfulResolver;

    /**
     *
     *
     * @var PriorityQueue
     */
    protected $queue;

    /**
     * Constructor to prevent {@link AggregateResolver} from being loaded more than once.
     */
    public function __construct()
    {
        $this->queue = new PriorityQueue();
    }

    /**
     * Retrieve an external iterator.
     *
     * @link  http://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return Traversable An instance of an object implementing {@link \Iterator} or {@link Traversable}
     */
    public function getIterator()
    {
        return $this->queue;
    }

    /**
     * Count elements of an object.
     *
     * @link  http://php.net/manual/en/countable.count.php
     *
     * @return int The custom count as an integer. The return value is cast to an integer.
     */
    public function count()
    {
        return $this->queue->count();
    }

    /**
     * Attach a resolver.
     *
     * @param  ResolverInterface $resolver
     * @param  int               $priority
     *
     * @return static
     */
    public function attach(ResolverInterface $resolver, $priority = 1)
    {
        $this->queue->insert($resolver, $priority);

        return $this;
    }

    /**
     * Return the last successful resolver, if any.
     *
     * @return ResolverInterface
     */
    public function getLastSuccessfulResolver()
    {
        return $this->lastSuccessfulResolver;
    }

    /**
     * Get last failure.
     *
     * @return false|string
     */
    public function getLastFailure()
    {
        return $this->lastFailure;
    }

    /**
     *
     *
     * @return string
     */
    public function resolve()
    {
        $this->lastFailure            = false;
        $this->lastSuccessfulResolver = null;

        if (0 === count($this->queue)) {
            $this->lastFailure = static::FAILURE_NO_RESOLVERS;

            return null;
        }

        /** @var ResolverInterface $resolver */
        foreach ($this->queue as $resolver) {
            $resource = $resolver->resolve();

            if ($resource) {
                // Resource found; return it
                $this->lastSuccessfulResolver = $resolver;

                return $resource;
            }
        }

        $this->lastFailure = static::FAILURE_NOT_FOUND;

        return null;
    }
}
