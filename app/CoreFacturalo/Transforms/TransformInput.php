<?php

namespace App\CoreFacturalo\Transforms;

use App\CoreFacturalo\Transforms\Inputs\DocumentInput;
use App\CoreFacturalo\Transforms\Inputs\InvoiceInput;
use App\CoreFacturalo\Transforms\Inputs\NoteInput;
use App\CoreFacturalo\Transforms\Inputs\SummaryInput;
use App\CoreFacturalo\Transforms\Inputs\VoidedInput;
use Closure;

class TransformInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  $type
     * @param  $isWeb
     * @return mixed
     */
    public function handle($request, Closure $next, $type, $isWeb)
    {
        if($type === 'document') {
            $originalAttributes = $this->originalAttributeDocument($request->all(), $isWeb);
        } elseif ($type === 'summary') {
            $originalAttributes = $this->originalAttributeSummary($request->all(), $isWeb);
        } else {
            $originalAttributes = $this->originalAttributeVoided($request->all(), $isWeb);
        }
        $request->replace($originalAttributes);
        return $next($request);
    }

    private function originalAttributeDocument($inputs, $isWeb)
    {
        $aux_document = DocumentInput::transform($inputs, $isWeb);
        $document = $aux_document['document'];
        if(in_array($document['document_type_id'], ['01', '03'])) {
            $aux_document_base = InvoiceInput::transform($inputs, $document, $isWeb);
        } else {
            $aux_document_base = NoteInput::transform($inputs, $document, $isWeb);
        }
        $document['group_id'] = $aux_document_base['group_id'];

        $original_attributes = [
            'type' => $aux_document_base['type'],
            'document' => $document,
            'document_base' => $aux_document_base['document_base'],
            'actions' => $aux_document['actions'],
            'success' => true,
        ];

        return $original_attributes;
    }

    private function originalAttributeSummary($inputs, $isWeb)
    {
        $summary = SummaryInput::transform($inputs);

        $original_attributes = [
            'type' => 'summary',
            'summary' => $summary,
            'success' => true
        ];
        return $original_attributes;
    }

    private function originalAttributeVoided($inputs, $isWeb)
    {
        $summary = VoidedInput::transform($inputs);

        $original_attributes = [
            'type' => 'voided',
            'voided' => $summary,
            'success' => true
        ];
        return $original_attributes;
    }
}