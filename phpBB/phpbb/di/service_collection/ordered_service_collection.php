<?php
/**
 *
 * This file is part of the phpBB Forum Software package.
 *
 * @copyright (c) phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * For full copyright and license information, please see
 * the docs/CREDITS.txt file.
 *
 */

namespace phpbb\di\service_collection;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Collection of services in a specified order
 */
class ordered_service_collection extends service_collection
{
	/**
	 * @var bool
	 */
	protected $is_ordered;

	/**
	 * @var array
	 */
	protected $service_ids;

	/**
	 * Constructor
	 *
	 * @param ContainerInterface $container Container object
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->is_ordered = false;
		$this->service_ids = array();

		parent::__construct($container);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetExists($index)
	{
		if (!$this->is_ordered)
		{
			$this->sort_services();
		}

		return parent::offsetExists($index);
	}

	/**
	 * {@inheritdoc}
	 */
	public function offsetGet($index)
	{
		if (!$this->is_ordered)
		{
			$this->sort_services();
		}

		return parent::offsetGet($index);
	}

	/**
	 * Adds a service ID to the collection
	 *
	 * @param string	$service_id
	 * @param int		$order
	 */
	public function add($service_id, $order)
	{
		if ($this->is_ordered)
		{
			return;
		}

		$order = (int) $order;

		$this->service_ids[$order][] = $service_id;
	}

	protected function sort_services()
	{
		if ($this->is_ordered)
		{
			return;
		}

		ksort($this->service_ids);
		foreach ($this->service_ids as $service_order_group)
		{
			foreach ($service_order_group as $service_id)
			{
				$this->offsetSet($service_id, null);
			}
		}

		$this->is_ordered = true;
	}
}
