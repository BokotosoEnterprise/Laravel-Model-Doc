<?php

namespace BoktosoEnterprise\ModelDoc\Console\Commands;

use Illuminate\Console\Command;
use BoktosoEnterprise\ModelDoc\Exceptions\ModelDocumentationFailedException;
use BoktosoEnterprise\ModelDoc\Services\DocumentationGenerator;

class GenerateModelDocumentationCommand extends Command
{
    protected $signature = 'model-doc:generate {--modelClass= : Model Class to build.}';

    protected $description = "Generate PHPDoc description based on the class.";

    public function handle(DocumentationGenerator $generator): void
    {
        if ($this->option('modelClass')) {
            $model = resolve($this->option('modelClass'));
            $models = collect([new $model(),]);
        } else {
            $models = $generator->collectModels();
        }

        foreach ($models as $model) {
            try {
                $generator->generate($model);
                $this->info("Wrote {$model->getName()}");
            } catch (ModelDocumentationFailedException $exception) {
                $this->warn("Failed {$model->getName()}: {$exception->getMessage()}");
                if ($this->output->isVerbose()) {
                    $this->warn($exception);
                }
            }
        }
    }
}
