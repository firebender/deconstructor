<?php

declare(strict_types=1);

namespace FireBender\Deconstructor;

use Exception;
use FireBender\Deconstructor\Concerns\ConstantsTrait;
use FireBender\Deconstructor\Concerns\InterfacesTrait;
use FireBender\Deconstructor\Concerns\LaravelTrait;
use FireBender\Deconstructor\Concerns\MethodsTrait;
use FireBender\Deconstructor\Concerns\ParentsTrait;
use FireBender\Deconstructor\Concerns\PropertiesTrait;
use FireBender\Deconstructor\Concerns\TraitsTrait;
use ReflectionObject;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;

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
     * @var SymfonyStyle
     */
    public $io;

    public function __construct()
    {
        $input = new ArgvInput();

        $output = new ConsoleOutput();

        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * @return array<string, mixed>|self
     */
    public function deconstruct(mixed $arg = null, bool $return = false): array|self
    {
        if ($arg === null) {
            return $this;
        }

        if (is_object($arg) === false) {
            throw new Exception('Can deconstruct only objects');
        }

        if ($return === true) {
            return [
                'object'     => new ReflectionObject($arg),
                'io'         => $this->io,
                'parents'    => $this->parents($arg),
                'traits'     => $this->traits($arg),
                'interfaces' => $this->interfaces($arg),
                'constants'  => $this->constants($arg),
                'properties' => $this->properties($arg),
                'methods'    => $this->methods($arg),
            ];
        }

        $payload = [
            'object'     => new ReflectionObject($arg),
            'io'         => $this->io,
            'parents'    => $this->parents($arg),
            'traits'     => $this->traits($arg),
            'interfaces' => $this->interfaces($arg),
            'constants'  => $this->formattedConstants($arg),
            'properties' => $this->formattedProperties($arg),
            'methods'    => $this->formattedMethods($arg),
        ];

        $this->output($payload);

        exit(1);
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function output(array $payload): void
    {
        $object = $payload['object'] ?? null;
        $parents = $payload['parents'] ?? null;
        $traits = $payload['traits'] ?? null;
        $interfaces = $payload['interfaces'] ?? null;
        $constants = $payload['constants'] ?? null;
        $properties = $payload['properties'] ?? null;
        $methods = $payload['methods'] ?? null;

        if ($object instanceof ReflectionObject) {
            $this->io->title($object->name);
        }

        if (is_array($parents) === true) {
            $this->io->section('Parents');
            $this->io->listing($parents);
        }

        if (is_array($traits) === true) {
            $this->io->section('Traits');
            $this->io->listing($traits);
        }

        if (is_array($interfaces) === true) {
            $this->io->section('Interfaces');
            $this->io->listing($interfaces);
        }

        if (is_array($constants) === true) {
            $this->io->section('Constants');
            $this->io->listing($constants);
        }

        if (is_array($properties) === true) {
            $this->io->section('Properties');
            $this->io->listing($properties);
        }

        if (is_array($methods) === true) {
            $this->io->section('Methods');
            $this->io->listing($methods);
        }
    }
}
