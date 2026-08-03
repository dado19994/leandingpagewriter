<?php

namespace App\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Str;
use RuntimeException;

class SaveWithUniqueSlug
{
    private const int MaxAttempts = 1000;

    /**
     * @param  array<string, mixed>  $attributes
     *
     * @template TModel of Model
     *
     * @param  TModel  $model
     * @return TModel
     */
    public function handle(Model $model, array $attributes, string $slugSource): Model
    {
        $baseSlug = Str::slug($slugSource) ?: 'contenuto';

        for ($attempt = 1; $attempt <= self::MaxAttempts; $attempt++) {
            $candidate = $attempt === 1 ? $baseSlug : $baseSlug.'-'.$attempt;
            $candidateExists = $model->newQuery()
                ->where('slug', $candidate)
                ->when($model->exists, fn ($query) => $query->whereKeyNot($model->getKey()))
                ->exists();

            if ($candidateExists) {
                continue;
            }

            try {
                $model->fill([...$attributes, 'slug' => $candidate]);
                $model->saveOrFail();

                return $model;
            } catch (UniqueConstraintViolationException $exception) {
                if (! $this->isSlugConflict($exception)) {
                    throw $exception;
                }
            }
        }

        throw new RuntimeException('Unable to allocate a unique slug after '.self::MaxAttempts.' attempts.');
    }

    private function isSlugConflict(UniqueConstraintViolationException $exception): bool
    {
        $hasSlugColumn = collect($exception->columns)
            ->contains(fn (string $column): bool => $column === 'slug' || Str::endsWith($column, '.slug'));

        return $hasSlugColumn || Str::contains((string) $exception->index, 'slug', ignoreCase: true);
    }
}
