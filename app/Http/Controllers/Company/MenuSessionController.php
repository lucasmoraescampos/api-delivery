<?php

namespace App\Http\Controllers\Company;

use App\Company;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\MenuSession;
use App\Setting;
use Illuminate\Support\Facades\Auth;

class MenuSessionController extends Controller
{
    public function index()
    {
        $sessions = MenuSession::where('company_id', Auth::id())->get();

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

        if ($session == null) {

            return response()->json([
                'success' => false,
                'message' => 'Sessão não encontrada!'
            ]);
        }

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
                'message' => 'Quantidade máxima de sessões excedida!'
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
        $request->validate([
            'name' => 'required|string'
        ]);

        $session = MenuSession::where('id', $id)
            ->where('company_id', Auth::id())
            ->first();

        if ($session == null) {

            return response()->json([
                'success' => false,
                'message' => 'Sessão não encontrada!'
            ]);
        }

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
                'message' => 'Você precisa desvincular esta sessão de todos os seus produtos!'
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
            'sessions' => 'required|array'
        ]);

        $position = 1;

        foreach ($request->sessions as $session) {

            dd($session);

            MenuSession::where('id', $session->id)
                ->update([
                    'position' => $position
                ]);

            $position++;

        }

        return response()->json([
            'success' => true,
            'message' => 'Sessões reordenadas com sucesso!'
        ]);
    }
}
