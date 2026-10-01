<?php

namespace App\Contracts;

interface NotesQuestionGeneratorContract
{
    /** @param array{count:int,types:array<string>,difficulty:string} $options */
    public function generate(array $filePaths, array $options): array;
}
