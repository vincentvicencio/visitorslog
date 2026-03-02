<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ValidIdType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class IDTypeController extends Controller
{
    public function idTypeIndex()
    {
        return view('pages.idtype.idtype');
    }

    /**
     * AJAX datatable data provider for valid ID types.
     */
    public function idTypeList(Request $request)
    {
        $keywords = strtolower($request->search);
        $limit    = $request->input('length');

        $rawquery = ValidIdType::withoutTrashed()->where(function ($query) use ($keywords) {
            $query->where('id_type_name', 'LIKE', "%$keywords%")
                  ->where('deleted_at', null);
        });

        $totalRecords = $rawquery->count();

        if ($request->input('draw') > 1) {
            $start        = $request->input('start');
            $column       = $request->input('order.0.column');
            $direction    = $request->input('order.0.dir');
            $order        = $request->input('columns')[$column]['data'];
            $temp         = $rawquery->get();
            $rawQuery     = $limit > 0 ? $rawquery->skip($start)->take($limit) : $rawquery;
            $data         = $rawquery->orderBy('updated_at', 'desc')->take($limit)->get();
            $totalFiltered = count($temp);
        } else {
            $data          = $rawquery->orderBy('updated_at', 'desc')->take($limit)->get();
            $totalFiltered = $totalRecords;
        }

        $newData = [];
        foreach ($data as $i => $d) {
            $newData[$i] = [
                'id_type_name'       => $d->id_type_name,
                'created_by' => $d->created_by ? user_name($d->created_by) : '-',
                'updated_by' => $d->updated_by ? user_name($d->updated_by) : '-',
                'created_at' => $d->created_at->format('F j, Y') . '<br>' . $d->created_at->format('l'),
                'action'     => '<div class="dropdown">
                                    <button class="dropdown-item btn-edit" data-id="' . $d->id . '"> Edit</button>
                                    <button class="text-danger dropdown-item btn-delete" data-id="' . $d->id . '" data-details="' . $d->id_type_name . '"> Delete</button>
                                 </div>',
            ];
        }

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data'            => $newData,
        ]);
    }

    /**
     * Save or update a valid ID type record.
     */
    public function idTypeSave(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required', 'string', 'max:40'],
            ],
            [
                'name.required' => 'Valid ID Type is required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => 1, 'errors' => $validator->errors()]);
        }

        $record_id = $request->record_id;
        $emp_code  = Auth::user()->id;
        $name      = trim($request->name);

        $duplicateQuery = ValidIdType::withoutTrashed()
                            ->whereRaw('LOWER(id_type_name) = ?', [strtolower($name)]);

        if ($record_id > 0) {
            $duplicateQuery->where('id', '!=', $record_id);
        }

        if ($duplicateQuery->exists()) {
            return response()->json([
                'status'  => 1,
                'message' => 'Valid ID Type Already Exists',
            ]);
        }

        $data = ['id_type_name' => $name];

        if ($record_id > 0) {
            $model   = ValidIdType::findOrFail($record_id);
            $oldData = $model->getOriginal();

            $model->update(['updated_by' => $emp_code] + $data);
            $message = 'Valid ID Type Successfully Updated';

            log_audit(
                'valid_id_types',
                'updated',
                $record_id,
                $oldData,
                $model->getAttributes(),
                'update'
            );
        } else {
            $model = ValidIdType::create(['created_by' => $emp_code] + $data);
            $message = 'Valid ID Type Successfully Created';

            log_audit(
                'valid_id_types',
                'created',
                $model->id,
                null,
                $model->toArray(),
                'save'
            );
        }

        return response()->json([
            'status'  => 0,
            'title'   => 'Success',
            'message' => $message,
        ]);
    }

    /**
     * Return a single record for editing.
     */
    public function idTypeSearch(Request $request)
    {
        $record = ValidIdType::find($request->id);
        if (!$record) {
            return response()->json([
                'status'  => 1,
                'message' => 'No Data Found',
            ]);
        }

        return response()->json([
            'status' => 0,
            'data'   => ['id' => $record->id, 'name' => $record->id_type_name],
        ]);
    }

    /**
     * Soft‑delete a valid ID type.
     */
    public function idTypeDelete(Request $request)
    {
        $record = ValidIdType::find($request->id);
        if (!$record) {
            return response()->json([
                'status'  => 1,
                'message' => 'Record not found',
            ]);
        }

        $details = $record->id_type_name;
        $oldData = $record->toArray();
        $record->update(['deleted_by' => Auth::user()->id]);
        $record->delete();

        log_audit(
            'valid_id_types',
            'deleted',
            $request->id,
            $oldData,
            null,
            'delete'
        );

        return response()->json([
            'status'  => 0,
            'title'   => 'Success',
            'message' => 'Valid ID Type Successfully Deleted',
        ]);
    }
}
