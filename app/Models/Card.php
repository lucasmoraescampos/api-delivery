<?php

namespace App\Models;

use App\Casts\Image;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'user_id',
    'number',
    'expiration_month',
    'expiration_year',
    'security_code',
    'holder_name',
    'document_type',
    'document_number',
    'provider',
    'icon'
  ];

  /**
   * The attributes that should be casted to native types.
   *
   * @var array
   */
  protected $casts = [
    'user_id' => 'integer',
    'icon'    => Image::class
  ];
}
