<?php declare(strict_types = 1);

namespace Nettrine\ORM\Mapping;

use Doctrine\ORM\Mapping\EntityListenerResolver;
use InvalidArgumentException;
use Nette\DI\Container;

class ContainerEntityListenerResolver implements EntityListenerResolver
{

	/** @var Container */
	private $container;

	/** @var object[] */
	protected $instances = [];

	/**
	 * @param Container $container
	 */
	public function __construct(Container $container)
	{
		$this->container = $container;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param string|NULL $className
	 * @return void
	 */
	public function clear($className = NULL): void
	{
		if ($className === NULL) {
			$this->instances = [];

			return;
		}

		if (isset($this->instances[$className = trim($className, '\\')])) {
			unset($this->instances[$className]);
		}
	}

	/**
	 * @param object $object
	 * @return void
	 */
	public function register($object): void
	{
		if (!is_object($object)) {
			throw new InvalidArgumentException(sprintf('An object was expected, but got "%s".', gettype($object)));
		}

		$this->instances[get_class($object)] = $object;
	}

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 * @param string $className
	 * @return object
	 */
	public function resolve($className)
	{
		if (isset($this->instances[$className = trim($className, '\\')])) {
			return $this->instances[$className];
		}

		if (($service = $this->container->getByType($className, FALSE))) {
			$this->instances[$className] = $this->container->getByType($className);
		} else {
			$this->instances[$className] = new $className();
		}

		return $this->instances[$className];
	}

}
