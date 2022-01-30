<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\FrontFaq
 *
 * @property int $id
 * @property int|null $language_id
 * @property string $question
 * @property string $answer
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|FrontFaq newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FrontFaq newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FrontFaq query()
 * @method static \Illuminate\Database\Eloquent\Builder|FrontFaq whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontFaq whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontFaq whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontFaq whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontFaq whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FrontFaq whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FrontFaq extends Model
{
    protected $guarded = ['id'];
}
