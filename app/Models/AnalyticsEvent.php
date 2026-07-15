<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    protected $table = 'jou_analytics_events';

    const UPDATED_AT = null;

    protected $fillable = [
        'platform', 'event_name', 'visitor_id', 'session_id',
        'page_view_id', 'page_path', 'page_type', 'element_id',
        'props', 'ip', 'user_agent', 'host', 'client_ts',
        'release_token', 'release_version', 'release_deployed_at',
        'release_status', 'asset_token_status',
    ];

    protected $casts = [
        'props' => 'array',
        'client_ts' => 'integer',
        'release_deployed_at' => 'datetime',
    ];
}
