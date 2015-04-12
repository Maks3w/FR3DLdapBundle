<?php

return Symfony\CS\Config\Config::create()
    ->fixers(
        [
            '-concat_without_spaces',
            '-empty_return',
            '-multiline_array_trailing_comma',
            '-phpdoc_indent',
            '-phpdoc_no_empty_return',
            '-phpdoc_params',
            '-phpdoc_to_comment',
            '-single_array_no_trailing_comma',
            'concat_with_spaces',
            'ereg_to_preg',
            'ordered_use',
        ]
    )
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->in('.')
    )
    ;
