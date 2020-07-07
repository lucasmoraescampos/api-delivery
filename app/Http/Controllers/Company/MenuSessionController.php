<?php

namespace App\Http\Controllers\Company;

use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MenuSession;
use App\Rules\MenuSessionRule;
use App\Setting;
use Illuminate\Support\Facades\Auth;

class MenuSessionController extends Controller
{
    public function index()
    {
        $sessions = MenuSession::where('company_id', Auth::id())
            ->orderBy('position', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }

    public function show($id)
    {
        $session = MenuSession::where('id', $id)
            ->where('company_id', Auth::id())
            ->first();

        return response()->json([
            'success' => true,
            'data' => $session
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string'
        ]);

        $company = Company::find(Auth::id());

        if ($company->getQtyMenuSessions() == Setting::getQtyMaxMenuSession()) {

            return response()->json([
                'success' => false,
                'message' => 'Você excedeu o limite máximo de sessões!'
            ]);

        }

        $menu_session = new MenuSession([
            'company_id' => $company->id,
            'name' => $request->name
        ]);

        $menu_session->createPosition();

        $menu_session->save();

        return response()->json([
            'success' => true,
            'message' => 'Sessão cadastrada com sucesso!',
            'data' => $menu_session
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->request->add(['id' => $id]);

        $request->validate([
            'id' => new MenuSessionRule(),
            'name' => 'required|string'
        ]);

        $session = MenuSession::find($id);

        $session->name = $request->name;

        $session->save();

        return response()->json([
            'success' => true,
            'message' => 'Sessão atualizada com sucesso!'
        ]);
    }

    public function delete($id)
    {
        $session = MenuSession::where('id', $id)
            ->where('company_id', Auth::id())
            ->first();

        if ($session == null) {

            return response()->json([
                'success' => false,
                'message' => 'Sessão não encontrada!'
            ]);

        }

        if ($session->isLinked()) {
            
            return response()->json([
                'success' => false,
                'message' => 'Desvincule esta sessão de todos os produtos antes de remove-lá!'
            ]);

        }

        $session->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sessão excluída com sucesso!'
        ]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'sessions' => ['required', 'array', new MenuSessionRule()]
        ]);

        $position = 1;

        foreach ($request->sessions as $session) {

            MenuSession::where('id', $session['id'])->update(['position' => $position]);

            $position++;

        }

        return response()->json([
            'success' => true,
            'message' => 'Ordem atualizada com sucesso!'
        ]);
    }
}
