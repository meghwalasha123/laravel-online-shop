<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Kyslik\ColumnSortable\Sortable;

class Wallet extends Model
{
    use HasFactory; 
    // use HasFactory, Sortable; 
    
    protected $fillable = [
        'user_uni_id',
        'reference_id',
        'gateway_order_id',
        'gateway_payment_id',
        'transaction_code',
        'wallet_history_description',
        'transaction_amount',
        'amount',
        'main_type',
        'created_by',
        'admin_percentage',
        'gst_amount',
        'astro_amount',
        'admin_amount',
        'tds_amount',
        'gateway_charge',
        'coupan_amount',
        'status',
        'gift_status',
        'offer_status',
        'offer_amount',
        'payment_method',
        'where_from',
    ];

    protected $casts = [
      'created_at' => 'datetime:Y-m-d H:i:s',
      'updated_at' => 'datetime:Y-m-d H:i:s',
    ];
    
    public $sortableAs = ['this_month_credit', 'this_month_debit', 'this_month_balance', 'total_credit', 'total_debit', 'total_balance'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }


    public function user()
    {
      return $this->belongsTo(User::class,'user_uni_id','user_uni_id');
    }
     
   
    // public function setDoAttribute($value)
    // {
    //     $this->attributes['created_at'] = Carbon::createFromFormat('F j, Y', $value)->toDateString();
    // }
    
}
