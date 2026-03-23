<?php

namespace App\Exports;

use App\Models\Location;
use App\Models\EmployeeLogs;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeExport implements FromCollection, WithHeadings, WithStyles
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = EmployeeLogs::query()
            // ->where('status', 1)
            ->withoutTrashed();

        // Apply search filter
        if (!empty($this->filters['search'])) {
            $keywords = strtolower($this->filters['search']);
            $query->where(function ($q) use ($keywords) {
                $q->where('first_name',      'LIKE', "%{$keywords}%")
                    ->orWhere('last_name',   'LIKE', "%{$keywords}%")
                    ->orWhere('emp_code',    'LIKE', "%{$keywords}%")
                    ->orWhere('full_name',    'LIKE', "%{$keywords}%");
            });
        }

        // Apply date filters
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        $companyLocations = collect(session('all_location', []))->keyBy(fn($item) => (string) data_get($item, 'id'));
        $guardLocations   = Location::query()->get(['id', 'name'])->keyBy(fn($item) => (string) $item->id);

        $data = $query->orderBy('id', 'desc')->get();

        return $data->map(function ($log) use ($companyLocations, $guardLocations) {
            $locationLabel = (string) $log->location;

            if (is_numeric($locationLabel)) {
                $companyLocation = $companyLocations->get($locationLabel);
                $guardLocation = $guardLocations->get($locationLabel);

                if ($companyLocation) {
                    $locationLabel = data_get($companyLocation, 'name', '');
                } elseif ($guardLocation) {
                    $locationLabel = $guardLocation->name;
                }
            }

            $time = $log->time ? Carbon::parse($log->time)->format('h:i A') : '-';
            $activity = $log->activity ?: '-';
            $status = (int) $log->status === 1 ? 'Out' : 'In';

        return [
                'Emp Code'      => $log->emp_code ?? '-',
                'Full Name'     => $log->full_name ?? trim(($log->first_name ?? '') . ' ' . ($log->last_name ?? '')),
                'Location'      => $locationLabel ?: '-',
                'Log Date'      => $log->created_at ? Carbon::parse($log->created_at)->format('F d, Y') : '-',
                'Time'          => $time,
                'Activity'      => $activity,
                'Status'        => $status,
                'Logged By'     => $log->created_by ? user_name($log->created_by) : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Emp Code',
            'Full Name',
            'Location',
            'Log Date',
            'Time',
            'Activity',
            'Status',
            'Logged By',
            'Timed Out By'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
