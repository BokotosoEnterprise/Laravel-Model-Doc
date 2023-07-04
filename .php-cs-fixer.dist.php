<?php

return BoktosoEnterprise\Fixer\Config::make()
    ->in(__DIR__)
    ->preset(
        new BoktosoEnterprise\Fixer\Presets\PrettyLaravel()
    )
    ->out();
