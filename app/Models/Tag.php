<?php

namespace App\Models;

use App\Support\Scout\Rules\SimpleMatchRule;
use App\Support\Scout\TagIndexConfigurator;
use App\Traits\Hashidable;
use App\Traits\Randomable;
use CyrildeWit\EloquentViewable\Contracts\Viewable as ViewableContract;
use CyrildeWit\EloquentViewable\Viewable;
use ScoutElastic\Searchable;
use Spatie\Tags\Tag as TagModel;

class Tag extends TagModel implements ViewableContract
{
    use Hashidable;
    use Randomable;
    use Searchable;
    use Viewable;

    /**
     * @var string
     */
    protected $indexConfigurator = TagIndexConfigurator::class;

    /**
     * @var array
     */
    protected $searchRules = [
        SimpleMatchRule::class,
    ];

    /**
     * @var array
     */
    protected $mapping = [
        'properties' => [
            'name' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
            ],
        ],
    ];

    /**
     * @return array
     */
    public function toSearchableArray(): array
    {
        return $this->only(['id', 'name']);
    }

    /**
     * @return morphedByMany
     */
    public function media()
    {
        return $this->morphedByMany(Media::class, 'taggable', 'taggables');
    }

    /**
     * @return int
     */
    public function getViewsAttribute(): int
    {
        return views($this)->unique()->count();
    }

    /**
     * @return string
     */
    public function getPlaceholderUrlAttribute(): string
    {
        return asset('storage/images/placeholders/empty.png');
    }

    /**
     * @param Builder $query
     * @param array   $tags
     * @param string  $type
     * @param string  $locale
     *
     * @return Builder
     */
    public function scopeWithSlugTranslated($query, array $tags = [], string $type = null, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $query
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->where(function ($query) use ($tags, $locale) {
                foreach ($tags as $tag) {
                    $query->orWhereJsonContains("slug->{$locale}", $tag);
                }
            });
    }
}
