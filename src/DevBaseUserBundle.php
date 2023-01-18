<?php

declare(strict_types=1);

namespace DevBase\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\HttpKernel\Kernel;

class DevBaseUserBundle extends Bundle
{
	/**
	 * {@inheritDoc}
	 */
	public function getPath(): string
	{
		return Kernel::VERSION_ID >= 40400 ? \dirname(__DIR__) : __DIR__;
	}
}
