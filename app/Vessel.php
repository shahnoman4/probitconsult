<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vessel extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */

     protected $casts = [
        'expected_date' => 'datetime',
        'arrival_date' => 'datetime',
        'selling_date' => 'datetime',
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Return list of Vessel for a business
     *
     * @param  int  $business_id
     * @param  bool  $show_none = false
     * @return array
     */
    public static function forDropdown($business_id, $show_none = false, $filter_use_for_repair = false)
    {
        $query = Vessel::where('business_id', $business_id);

        if ($filter_use_for_repair) {
            $query->where('use_for_repair', 1);
        }

        $vessels = $query->orderBy('name', 'asc')
                    ->pluck('name', 'id');

        if ($show_none) {
            $vessels->prepend(__('lang_v1.none'), '');
        }

        return $vessels;
    }
}
