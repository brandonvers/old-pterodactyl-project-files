<?php 

namespace Pterodactyl\Http\Controllers\Api\Client;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Pterodactyl\Exceptions\DisplayException;
use Pterodactyl\Http\Requests\Api\Client\TicketsRequest;

use Pterodactyl\Models\Ticket;
use Pterodactyl\Models\TicketCategory;
use Pterodactyl\Models\TicketPriority;
use Pterodactyl\Models\TicketComment;


class TicketsController extends ClientApiController 
{

    public function index(TicketsRequest $request): array
    {

    	$tickets = DB::table('tickets')->orderBy('updated_at', 'DESC')->where('user_id', '=', $request->user()->id)->get();
        $categories = DB::table('ticket_categories')->get();
        $priorities = DB::table('ticket_priorities')->get();

        foreach ($tickets as $key => $ticket) {
            $tickets[$key]->category = DB::table('ticket_categories')->select(['id', 'name',])->where('id', '=', $ticket->category_id)->first();
            $tickets[$key]->priority = DB::table('ticket_priorities')->select(['id', 'name',])->where('id', '=', $ticket->priority_id)->first();
        }

        return [
            'success' => true,
            'data' => [
                'tickets' => $tickets,
                'categories' => $categories,
                'priorities' => $priorities
            ],
        ];

    }


    public function create(TicketsRequest $request): array
    {

        $this->validate($request, [
        	"title" => "required", 
        	"category" => "required", 
        	"priority" => "required", 
        	"message" => "required"
    	]);

        $ticket = new Ticket();
        $ticket->title = $request->title;
        $ticket->user_id = $request->user()->id;
        $ticket->ticket_id = strtoupper(str_random(5));
        $ticket->category_id = $request->category;
        $ticket->priority_id = $request->priority;
        $ticket->status = 1;
        $ticket->save();

        $comment = new TicketComment;
        $comment->ticket_id = $ticket->id;
        $comment->user_id = $request->user()->id;
        $comment->comment = $request->message;
        $comment->save();

        return [
            'success' => true,
            'data' => [],
        ];
    }

    public function view(TicketsRequest $request, $id): array
    {

        $tickets = DB::table('tickets')->where("id", '=', $id)->get();
        $categories = DB::table('ticket_categories')->get();
        $priorities = DB::table('ticket_priorities')->get();
        $users = DB::table('users')->get();

        if (count($tickets) < 1) {
            throw new DisplayException('Ticket not found.');
        }

        foreach ($tickets as $key => $ticket) {

            $tickets[$key]->category = DB::table('ticket_categories')
            ->select(['id', 'name',])
            ->where('id', '=', $ticket->category_id)
            ->first();

            $tickets[$key]->priority = DB::table('ticket_priorities')
            ->select(['id', 'name',])
            ->where('id', '=', $ticket->priority_id)
            ->first();

            $tickets[$key]->comments = DB::table('ticket_comments')
            ->select(['id', 'ticket_id', 'user_id', 'comment', 'updated_at',])
            ->orderBy('updated_at', 'DESC')
            ->where('ticket_id', '=', $ticket->id)
            ->get();

            foreach ($tickets[$key]->comments as $key2 => $comment) {

                $tickets[$key]->comments[$key2]->user = DB::table('users')
                ->select(['id', 'username', 'name_first', 'name_last'])
                ->where('id', '=', $comment->user_id)
                ->get();
            }
        }

        return [
            'success' => true,
            'data' => [
                'tickets' => $tickets,
                'categories' => $categories,
                'priorities' => $priorities,
            ],
        ];
    }

    public function reply(TicketsRequest $request): array
    {

        $this->validate($request, [
            'message' => 'required'
        ]);

        $id = (int) $request->input('id', 0);
        $message = trim(strip_tags($request->input('message', '')));

        $ticket = DB::table('tickets')->where('id', '=', $id)->get();
        if (count($ticket) < 1) {
            throw new DisplayException('Ticket not found.');
        }

        DB::table('ticket_comments')->insert([
            'ticket_id' => $id,
            'user_id' => $request->user()->id,
            'comment' => $message,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('tickets')->where("id", $ticket[0]->id)->update([
            'status' => 3,
            'updated_at' => Carbon::now(),
        ]);

        return [
            'success' => true,
            'data' => [],
        ];
    }

    public function close(TicketsRequest $request, $id): array
    {

        $id = (int) $id;

        DB::table('tickets')->where("id", $id)->update([
            'status' => 0,
        ]);

        return [
            'success' => true,
            'data' => [],
        ];

    }
}