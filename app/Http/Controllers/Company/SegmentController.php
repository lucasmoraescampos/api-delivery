<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Repositories\SegmentRepositoryInterface;
use Illuminate\Http\Request;

class SegmentController extends Controller
{
    private $segmentRepository;

    public function __construct(SegmentRepositoryInterface $segmentRepository)
    {
        $this->segmentRepository = $segmentRepository;
    }

    public function index($company_id)
    {
        $segments = $this->segmentRepository->getByCompany($company_id);

        return response()->json([
            'success' => true,
            'data' => $segments
        ]);
    }

    public function store(Request $request, $company_id)
    {
        $attributes = $request->all() + ['company_id' => $company_id];

        $segment = $this->segmentRepository->create($attributes);

        return response()->json([
            'success' => true,
            'message' => 'Segmento cadastrado com sucesso',
            'data' => $segment
        ]);
    }

    public function reorder(Request $request, $company_id)
    {
        $attributes = $request->all() + ['company_id' => $company_id];

        $segment = $this->segmentRepository->reorder($attributes);

        return response()->json([
            'success' => true,
            'message' => 'Segmentos ordenados com sucesso',
            'data' => $segment
        ]);
    }

    public function update(Request $request, $company_id, $id)
    {
        $attributes = $request->all() + ['company_id' => $company_id];

        $segment = $this->segmentRepository->update($id, $attributes);

        return response()->json([
            'success' => true,
            'message' => 'Segmento atualizado com sucesso',
            'data' => $segment
        ]);
    }

    public function delete($company_id, $id)
    {
        $this->segmentRepository->delete($id, $company_id);

        return response()->json([
            'success' => true,
            'message' => 'Segmento excluído com sucesso'
        ]);
    }
}
