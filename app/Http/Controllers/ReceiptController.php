<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PrinterService;

class ReceiptController extends Controller
{
    protected $printerService;

    public function __construct(PrinterService $printerService)
    {
        $this->printerService = $printerService;
    }

    public function index()
    {
        return view('receipt_form');
    }

    public function print(Request $request)
    {
        $hotelName = $request->input('hotel_name');
        $items = $request->input('items');

        $results = $this->printerService->printReceipt($hotelName, $items);

        return response()->json($results);
    }
}