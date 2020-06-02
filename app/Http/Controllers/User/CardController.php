<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Rules\UserCardRule;
use App\UserCard;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    public function index()
    {
        $cards = UserCard::all();

        return response()->json([
            'success' => true,
            'data' => $cards      
        ]);
    }

    public function show($id)
    {
        $request = new Request();

        $request->request->add(['id' => $id]);

        $request->validate(['id' => new UserCardRule()]);

        $card = UserCard::find($id);

        return response()->json([
            'success' => true,
            'data' => $card          
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'number' => 'required|string|max:20',
            'expiration_month' => 'required|string|min:2|max:2',
            'expiration_year' => 'required|string|min:4|max:4',
            'security_code' => 'required|string|max:5',
            'holder_name' => 'required|string|max:100',
            'holder_document_number' => 'required|string|max:20',
            'payment_method' => 'required|string|max:40'
        ]);

        $card = new UserCard([
            'user_id' => Auth::id(),
            'number' => $request->number,
            'expiration_month' => $request->expiration_month,
            'expiration_year' => $request->expiration_year,
            'security_code' => $request->security_code,
            'holder_name' => $request->holder_name,
            'holder_document_number' => $request->holder_document_number,
            'payment_method' => $request->payment_method,
        ]);

        $card->setHolderDocumentType();

        $card->setLastFourDigits();

        $card->save();

        return response()->json([
            'success' => true,
            'data' => $card,
            'message' => 'Cartão cadastrado com sucesso!'            
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->request->add(['id' => $id]);

        $request->validate([
            'id' => new UserCardRule(),
            'expiration_month' => 'required|string|min:2|max:2',
            'expiration_year' => 'required|string|min:4|max:4',
            'security_code' => 'required|string|max:5',
            'holder_name' => 'required|string|max:100',
            'holder_document_number' => 'required|string|max:20'
        ]);

        $card = UserCard::find($id);

        $card->expiration_month = $request->expiration_month;

        $card->expiration_year = $request->expiration_year;

        $card->security_code = $request->security_code;

        $card->holder_name = $request->holder_name;

        $card->holder_document_number = $request->holder_document_number;

        $card->setHolderDocumentType();

        $card->save();

        return response()->json([
            'success' => true,
            'data' => $card,
            'message' => 'Cartão atualizado com sucesso!'            
        ]);
    }

    public function delete($id)
    {
        $request = new Request();

        $request->request->add(['id' => $id]);

        $request->validate(['id' => new UserCardRule()]);

        UserCard::where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cartão excluído com sucesso!'      
        ]);
    }
}
