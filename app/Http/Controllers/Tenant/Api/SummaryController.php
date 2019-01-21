<?php
namespace App\Http\Controllers\Tenant\Api;

use App\CoreFacturalo\Facturalo;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Summary;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SummaryController extends Controller
{
    public function __construct()
    {
        $this->middleware('input.transform:summary,api', ['only' => ['store']]);
    }

    public function store(Request $request)
    {
        $fact = DB::transaction(function () use($request) {
            $facturalo = new Facturalo();
            $facturalo->save($request->all());
            $facturalo->createXmlUnsigned();
            $facturalo->signXmlUnsigned();
            $facturalo->senderXmlSignedSummary();
            return $facturalo;
        });

        $document = $fact->getDocument();
        //$response = $fact->getResponse();

        return [
            'success' => true,
            'data' => [
                'external_id' => $document->external_id,
                'ticket' => $document->ticket,
            ]
        ];
    }

    public function status(Request $request)
    {
        if($request->has('external_id')) {
            $external_id = $request->input('external_id');
            $summary = Summary::where('external_id', $external_id)
                ->whereUser()
                ->first();
            if(!$summary) {
                throw new Exception("El código externo {$external_id} es inválido, no se encontró resumen relacionado");
            }
        } elseif ($request->has('ticket')) {
            $ticket = $request->input('ticket');
            $summary = Summary::where('ticket', $ticket)
                ->whereUser()
                ->first();
            if(!$summary) {
                throw new Exception("El ticket {$ticket} es inválido, no se encontró resumen relacionado");
            }
        } else {
            throw new Exception('Es requerido el código externo o ticket');
        }

        $facturalo = new Facturalo();
        $facturalo->setDocument($summary);
        $facturalo->statusSummary($summary->ticket);

        $response = $facturalo->getResponse();

        return [
            'success' => true,
            'data' => [
                'filename' => $summary->filename,
                'external_id' => $summary->external_id
            ],
            'links' => [
                'xml' => $summary->download_external_xml,
//                'pdf' => $summary->download_external_pdf,
                'cdr' => $summary->download_external_cdr,
            ],
            'response' => $response
        ];
    }
}