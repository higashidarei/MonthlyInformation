<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
  protected $fillable = [
    'type',
    'title',
    'description',
    'image_url',
    'detail_url',
    'start_date',
    'end_date',
    'director',
    'author',
    'venue',
    'source',
    'source_id',
    'month_tag',
    'country',
    'genre_names',
  ];
}
