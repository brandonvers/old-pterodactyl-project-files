<?php

namespace Pterodactyl\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * Download as PDF
     * 
     * 
     */
    public function index()
    {
        //return view('pdf.invoice', $this);

    }

    /**
     * Gets the user who owns the server.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
