<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class PdfController extends Controller
{
    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function generateStatement($id)
    {
        $account = Account::with('transactions')->where('user_id', $this->user->id)->where('account_number', $id)->first();
        
        $pdf = Pdf::loadView('pdf.statement', compact('account'));

        return $pdf->download("statement_{$account->account_number}.pdf");
    }
}