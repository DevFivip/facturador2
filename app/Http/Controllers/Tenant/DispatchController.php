<?php
namespace App\Http\Controllers\Tenant;

use App\CoreFacturalo\Helpers\Storage\StorageDocument;
use App\Http\Controllers\Controller;
use App\Http\Resources\Tenant\DispatchCollection;
use App\Models\Tenant\Dispatch;
use Exception;
use Illuminate\Http\Request;

class DispatchController extends Controller
{
    use StorageDocument;

    public function index()
    {
        return view('tenant.dispatches.index');
    }

    public function columns()
    {
        return [
            'number' => 'Número'
        ];
    }

    public function records(Request $request)
    {
        $records = Dispatch::where($request->column, 'like', "%{$request->value}%")
            ->orderBy('series')
            ->orderBy('number', 'desc');

        return new DispatchCollection($records->paginate(env('ITEMS_PER_PAGE', 10)));
    }

    public function downloadExternal($type, $external_id)
    {
        $retention = Dispatch::where('external_id', $external_id)->first();
        if(!$retention) {
            throw new Exception("El código {$external_id} es inválido, no se encontro documento relacionado");
        }

        switch ($type) {
            case 'pdf':
                $folder = 'pdf';
                break;
            case 'xml':
                $folder = 'signed';
                break;
            case 'cdr':
                $folder = 'cdr';
                break;
            default:
                throw new Exception('Tipo de archivo a descargar es inválido');
        }

        return $this->downloadStorage($retention->filename, $folder);
    }
}