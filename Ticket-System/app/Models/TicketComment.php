<?php

namespace Pterodactyl\Models;

use Illuminate\Database\Eloquent\Model;

class TicketComment extends Model {
	
	protected $fillable = [
    	'ticket_id', 
    	'user_id', 
    	'comment'
	];

    public function ticketcategory(){
    return $this->belongsTo(TicketCategory::class);}

    public function ticket(){
    return $this->belongsTo(Ticket::class);}

    public function user(){
    return $this->belongsTo(User::class);}
}
