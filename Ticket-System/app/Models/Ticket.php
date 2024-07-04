<?php

namespace Pterodactyl\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model {
	
    protected $fillable = [
    	'status',
    	'title', 
        'ticket_id', 
        'category_id', 
        'priority_id', 
        'user_id', 
    ];

    public function ticketcategory()
    {
        return $this->belongsTo(TicketCategory::class);
    }

    public function ticketpriority()
    {
        return $this->belongsTo(TicketPriority::class);
    }

    public function ticketcomments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
