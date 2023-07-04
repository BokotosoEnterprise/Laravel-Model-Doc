<?php

namespace BoktosoEnterprise\ModelDoc\Services\Objects;

use Illuminate\Database\Eloquent\Factories\Factory as IlluminateFactory;
use BoktosoEnterprise\ModelDoc\Exceptions\InvalidModelException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class Factory extends AbstractDocumentableClass
{
    private Model $model;

    public function __construct(SplFileInfo $fileInfo, Model $model)
    {
        parent::__construct($fileInfo);

        $this->model = $model;

        if ( ! $this->reflectionClass->isSubclassOf(IlluminateFactory::class)) {
            throw new InvalidModelException('Class does not extend Illuminate\Database\Eloquent\Factories\Factory');
        }
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public static function fromModel(Model $model): ?self
    {
        $instance = $model->getInstance();
        if (null === $instance) {
            return null;
        }

        if ( ! method_exists($instance, 'factory')) {
            return null;
        }

        /**
         * @var \Illuminate\Database\Eloquent\Factories\Factory<\Illuminate\Database\Eloquent\Model> $illuminateFactory
         *
         * @phpstan-ignore-next-line
         */
        $illuminateFactory = $instance::factory();

        $refClass = new \ReflectionClass($illuminateFactory);

        ['dirname' => $dir, 'filename' => $file] = pathinfo($refClass->getFileName());

        $finder = new Finder();
        $finder->files()->name("{$file}.php")->in($dir);

        foreach ($finder as $file) {
            return new self($file, $model);
        }

        return null;
    }
}
