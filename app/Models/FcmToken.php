<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
	/**
     * Constants.
     */
    const PLATFORM_WEB = 'web';

    const PLATFORM_ANDROID = 'android';

    const PLATFORM_IOS = 'ios';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
        'user_id',
        'company_deliveryman_id',
		'token',
		'platform'
	];
}
