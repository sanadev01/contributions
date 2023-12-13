<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\AmazonSPClients\AuthApiClient;

class ConnectionsController extends Controller
{

    public function getIndex(Request $request)
    {
        session()->flash($request->message_type ?? '', $request->message ?? '');
        $users = User::whereHas('sp_token')->when(Auth::user()->isUser(), function ($query) {
            $query->where('id', Auth::id());
        })->get();
        return view('admin.users.amazon.connections', compact('users'));
    }

    public function getStatusChange(Request $request): JsonResponse
    {
        $request->validate(['account_id' => ['required']]);

        $user->is_active = !$user->is_active;
        $user->save();
        $type = $user->is_active ? 'success' : 'error';
        $message = $user->is_active ? 'Activat Successfully ' : 'De-activte Successfully';
        return redirect("/user/amazon/connect?message_type=$type&message=$message");
    }
    public function getAuth(Request $request)
    {
        $this->validate($request, ['region' => 'required']);
        $user = Auth::user();
        return (new AuthApiClient($user, 'ACCESS_TOKEN'))
            ->authorizeConsent(
                $user->id,
                $request->get('region')
            );
    }
}
