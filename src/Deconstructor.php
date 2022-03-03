<?php

declare(strict_types=1);

namespace FireBender\Deconstructor;

use ReflectionObject;
use FireBender\Deconstructor\Concerns\ParentsTrait;
use FireBender\Deconstructor\Concerns\TraitsTrait;
use FireBender\Deconstructor\Concerns\InterfacesTrait;
use FireBender\Deconstructor\Concerns\ConstantsTrait;
use FireBender\Deconstructor\Concerns\PropertiesTrait;
use FireBender\Deconstructor\Concerns\MethodsTrait;
use FireBender\Deconstructor\Concerns\LaravelTrait;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Exception;

class Deconstructor
{
	use ParentsTrait;
	use TraitsTrait;
	use InterfacesTrait;
	use ConstantsTrait;
	use PropertiesTrait;
	use MethodsTrait;
	use LaravelTrait;

	/**
	 * 
	 */
	public $io;

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

		exit(1);
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