<?php

namespace Modules\Job\Models;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Application;
use Modules\Clean\Casts\HtmlSpecialCharsCast;
use Modules\Job\Database\Factories\ClientFactory;

class Client extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const RECORDS_PER_PAGE = 10;

    public static array $clientTypes = [
        'company',
        'private person',
    ];

    protected $fillable = [
        'event_id',
        'client_detail_id',
        'name',
        'address',
        'type'
    ];

    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'title' => HtmlSpecialCharsCast::class,
        'address' => HtmlSpecialCharsCast::class,
    ];


    /**
     * Create a new factory instance for the model.
     *
     * @return Factory
     */
    protected static function newFactory(): Factory
    {
        return ClientFactory::new();
    }


    public function scopePaginatedClients($query)
    {
        return $query->orderBy('name', 'ASC')
            ->with(['jobs', 'client_detail'])
            ->paginate(Client::RECORDS_PER_PAGE)->withQueryString();
    }


    /**
     * @return HasMany
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }


    /**
     * @return HasOne
     */
    public function client_detail(): HasOne
    {
        return $this->hasOne(ClientDetail::class, 'id', 'client_detail_id');
    }

}
