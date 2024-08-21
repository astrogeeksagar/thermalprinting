<?php

namespace App\Services;

use App\Models\Printer;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer as EscposPrinter;

class PrinterService
{
    public function printReceipt($hotelName, $items)
    {
        $results = [];
        $printers = Printer::where('is_active', true)->get();

        foreach ($printers as $printer) {
            try {
                $connector = $this->getConnector($printer);
                $escposPrinter = new EscposPrinter($connector);

                $this->printReceiptContent($escposPrinter, $hotelName, $items);

                $escposPrinter->close();
                $results[$printer->name] = ['success' => true];
            } catch (\Exception $e) {
                $results[$printer->name] = ['success' => false, 'message' => $e->getMessage()];
            }
        }
        return $results;
    }

    private function getConnector($printer)
    {
        switch ($printer->type) {
            case 'network':
                $ip = $printer->ip;
                $port = $printer->port ?? 9100;

                if (empty($ip)) {
                    throw new \Exception("IP address for network printer is not set.");
                }
                return new NetworkPrintConnector($ip, $port);

            case 'usb':
                $name = $printer->name;
                if (empty($name)) {
                    throw new \Exception("Printer name for USB printer is not set.");
                }

                // $name = "\\SagarDesk2\Hp Receipt";
                $name = "smb://SagarDesk2/Hp Receipt";
                return new WindowsPrintConnector($name);

            default:
                throw new \Exception("Unsupported printer type: " . $printer->type);
        }
    }


    private function printReceiptContent(EscposPrinter $printer, $hotelName, $items)
    {
        $printer->setJustification(EscposPrinter::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->text($hotelName . "\n");
        $printer->text("RECEIPT\n");
        $printer->setEmphasis(false);
        $printer->text("--------------------------------\n");

        $printer->setJustification(EscposPrinter::JUSTIFY_LEFT);
        $total = 0;
        foreach ($items as $item) {
            $printer->text(str_pad($item['name'], 20) . str_pad('$' . number_format($item['price'], 2), 10, ' ', STR_PAD_LEFT) . "\n");
            $total += $item['price'];
        }
        $printer->text("--------------------------------\n");

        $printer->setEmphasis(true);
        $printer->text(str_pad("Total:", 20) . str_pad('$' . number_format($total, 2), 10, ' ', STR_PAD_LEFT) . "\n");
        $printer->setEmphasis(false);

        $printer->feed(3);
        $printer->cut();
    }
}
