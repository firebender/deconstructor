<?php

declare(strict_types=1);

namespace FireBender;

use ReflectionObject;
use FireBender\Deconstructor\Traits\Parents;
use FireBender\Deconstructor\Traits\Traits;
use FireBender\Deconstructor\Traits\Interfaces;
use FireBender\Deconstructor\Traits\Constants;
use FireBender\Deconstructor\Traits\Properties;
use FireBender\Deconstructor\Traits\Methods;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Exception;

class Deconstructor
{
	use Parents;
	use Traits;
	use Interfaces;
	use Constants;
	use Properties;
	use Methods;

	/**
	 * 
	 */
	protected $io;

	/**
	 * 
	 */
	public function __construct()
	{
		$input = new ArgvInput();

		$output = new ConsoleOutput();

		$this->io = new SymfonyStyle($input, $output);
	}

	/**
	 * 
	 */
	public function deconstruct(mixed $arg = null, bool $return = false)
	{
		if ($arg === null) {
			return $this;
		}

		if (is_object($arg) === false) {
			throw new Exception("Can deconstruct only objects");
		}

		if ($return === true) {
			return [
				'object' => new ReflectionObject($arg),
				'io' => $this->io,
				'parents' => $this->parents($arg),
				'traits' => $this->traits($arg),
				'interfaces' => $this->interfaces($arg),
				'constants' => $this->constants($arg),
				'properties' => $this->properties($arg),
				'methods' => $this->methods($arg),
			];
		}

		$payload = [
			'object' => new ReflectionObject($arg),
			'io' => $this->io,
			'parents' => $this->parents($arg),
			'traits' => $this->traits($arg),
			'interfaces' => $this->interfaces($arg),
			'constants' => $this->formattedConstants($arg),
			'properties' => $this->formattedProperties($arg),
			'methods' => $this->formattedMethods($arg),
		];

		$this->output($payload);

		exit;
	}

	/**
	 * 
	 */
	protected function output(Array $payload)
	{
		extract($payload);

		$this->io->title($object->name);

		$this->io->section('Parents');
		$this->io->listing($parents);

		$this->io->section('Traits');
		$this->io->listing($traits);

		$this->io->section('Interfaces');
		$this->io->listing($interfaces);

		$this->io->section('Constants');
		$this->io->listing($constants);

		$this->io->section('Properties');
		$this->io->listing($properties);

		$this->io->section('Methods');
		$this->io->listing($methods);
	}

}