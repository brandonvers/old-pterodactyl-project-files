<?php 

namespace Pterodactyl\Http\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Prologue\Alerts\AlertsMessageBag;
use Pterodactyl\Http\Controllers\Controller;

use Pterodactyl\Models\Ticket;
use Pterodactyl\Models\TicketCategory;
use Pterodactyl\Models\TicketPriority;
use Pterodactyl\Models\TicketComment;

class TicketsController extends Controller 
{

    /**
     * @var \Prologue\Alerts\AlertsMessageBag
     */
    protected $alert;

    /**
     * AnnouncementsController constructor.
     * @param AlertsMessageBag $alert
     */
    public function __construct(AlertsMessageBag $alert)
    {
        $this->alert = $alert;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function index(Request $request)
    {

        $search = $request->get('query');

        $querystringArray = Input::only(['query']);

        if (isset($_REQUEST['query'])) {
            $tickets = Ticket::where('title', 'like', '%'.$search.'%')
            ->orwhere('ticket_id', 'like', '%'.$search.'%')
            ->orderBy("id", "asc")
            ->paginate(15)
            ->appends($querystringArray);
        } else {
            $tickets = Ticket::paginate(15)
            ->appends($querystringArray);
        }
        $categories = TicketCategory::all();
        $priorities = TicketPriority::all();

        return view('admin.tickets.index', [
            'tickets' => $tickets, 
            'categories' => $categories, 
            'priorities' => $priorities
        ]);
    }

    public function view(Request $request, $id)
    {

        $ticket = Ticket::where("id", $id)->firstOrFail();
        $category = TicketCategory::where("id", $ticket->category_id)->first();
        $priority = TicketPriority::where("id", $ticket->priority_id)->first();
        $comments = TicketComment::where("ticket_id", $ticket->id)->orderBy('id', 'DESC')->get();

        return view('admin.tickets.view', [
            'ticket' => $ticket,
            'category' => $category,
            'priority' => $priority,
            'comments' => $comments
        ]);
    }

    public function reply(Request $request, $id)
    {

        $this->validate($request, [
            'message' => 'required',
            'status' => 'required'
        ]);

        $id = (int) $request->input('id', 0);
        $message = trim(strip_tags($request->input('message', '')));
        $status = (int) $request->input('status', 3);

        $ticket = DB::table('tickets')->where('id', '=', $id)->first();

        DB::table('ticket_comments')->insert([
            'ticket_id' => $id,
            'user_id' => $request->user()->id,
            'comment' => $message,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('tickets')->where("id", $ticket->id)->update([
            'status' => $status,
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->back()->with("status", "Your comment has been submitted.");
    }

    public function close(Request $request, $id)
    {

        $id = (int) $id;

        DB::table('tickets')->where("id", $id)->update([
            'status' => 0,
        ]);

        return redirect()->back()->with("status", "Your ticket has been closed.");
    }

    public function delete(Request $request)
    {

        $id = (int) $request->input('id', '');

        $ticket = DB::table('tickets')->where('id', '=', $id)->get();
        if (count($ticket) < 1) {
            return response()->json(['error' => 'Ticket not found.'])->setStatusCode(500);
        }

        DB::table('tickets')->where('id', '=', $id)->delete();

        return response()->json(['success' => true]);

    }

}