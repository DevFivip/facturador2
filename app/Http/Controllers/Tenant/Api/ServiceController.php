<?php
namespace App\Http\Controllers\Tenant\Api;

use App\Core\Cpe\ConsultCdrService;
use App\Core\Services\Dni\Dni;
use App\Core\Services\Extras\ValidateCpe;
use App\Core\Services\Ruc\Sunat;
use App\Http\Controllers\Controller;
use App\Models\Tenant\Catalogs\Department;
use App\Models\Tenant\Catalogs\District;
use App\Models\Tenant\Catalogs\Province;
use App\Models\Tenant\Document;
use Illuminate\Http\Request;
use App\Core\Services\Ruc\ExchangeRate;
use Carbon\Carbon;
use App\Models\Tenant\ExchangeRate as ExchangeRateModel;

class ServiceController extends Controller
{
    public function ruc($number)
    {
        $service = new Sunat();
        $res = $service->get($number);
        if ($res) {
            $province_id = Province::idByDescription($res->provincia);
            return [
                'success' => true,
                'data' => [
                    'name' => $res->razonSocial,
                    'trade_name' => $res->nombreComercial,
                    'address' => $res->direccion,
                    'phone' => implode(' / ', $res->telefonos),
                    'department' => ($res->departamento)?:'LIMA',
                    'department_id' => Department::idByDescription($res->departamento),
                    'province' => ($res->provincia)?:'LIMA',
                    'province_id' => $province_id,
                    'district' => ($res->distrito)?:'LIMA',
                    'district_id' => District::idByDescription($res->distrito, $province_id),
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => $service->getError()
            ];
        }
    }

    public function dni($number)
    {
        $res = Dni::search($number);

        return $res;
    }

    public function validateCpe(Request $request)
    {
        $validateCpe = new ValidateCpe();
        $documents = Document::whereIn('external_id', $request->input('documents'))->get();
        $res = [];
        foreach($documents as $document) {
            $res[] = $validateCpe->search($document->document_type_code, $document->series, $document->number, $document->date_of_issue);
        }
        return $res;
    }

    public function consultStatus($documents)
    {
        $consultCdrService = new ConsultCdrService();
        $res = $consultCdrService->getStatus('01', 'F001', 5);

        return $res;
    }

    public function consultCdrStatus($documents)
    {
        $consultCdrService = new ConsultCdrService();
        $res = $consultCdrService->getStatusCdr('01', 'F001', 4);

        return $res;
    }

    public function exchange_rate(Request $request)
    {
        $records = [];
        if (!$request['last_date']) {
            $records = ExchangeRateModel::where('date', $request['date'])->get()->keyBy('date')->toArray();
        }
        if (empty($records)) {
            $exchange_rate = new ExchangeRate($request['cur_date'],$request['last_date']);
            $records = $exchange_rate->get();
            if ($records) {
                foreach ($records as $key => $item) {
                    if (!ExchangeRateModel::where('date',$key)->first() && (Carbon::today()->toDateString() != $key || $item['date'] == $item['date_original'])) {
                        ExchangeRateModel::create($item);
                    }
                }
            }
        }
        if ($records) {
            return [
                'success' => true,
                'message' => 'Tipos de cambio obtenidos',
                'data' => $records
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Error'
            ];
        }
    }
}