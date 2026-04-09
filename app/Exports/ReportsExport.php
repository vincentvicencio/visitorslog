<?php

namespace App\Exports;

use App\Models\Visitor;
use App\Models\Location;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportsExport implements FromCollection, WithHeadings, WithStyles
{
    protected $filters;

        public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        \Log::info('ReportsExport Filters:', $this->filters);
        
        $query = Visitor::with('visitorType')
            ->withoutTrashed();

        // Apply status filter - handle comma-separated values like "0" or "0,1"
        if (isset($this->filters['status']) && $this->filters['status'] !== '') {
            $statuses = array_map('intval', array_filter(explode(',', (string)$this->filters['status']), function($v) { return trim($v) !== ''; }));
            \Log::info('Applying status filter:', $statuses);
            if (!empty($statuses)) {
                $query->whereIn('status', $statuses);
            }
        }

        // Apply location filter
        if (isset($this->filters['location']) && $this->filters['location'] !== '') {
            \Log::info('Applying location filter:', [$this->filters['location']]);
            $query->where('location', $this->filters['location']);
        }

        // Apply search filter
        if (!empty($this->filters['search'])) {
            $keywords = strtolower($this->filters['search']);
            $query->where(function ($q) use ($keywords) {
                $q->where('first_name',      'LIKE', "%{$keywords}%")
                    ->orWhere('middle_name', 'LIKE', "%{$keywords}%")
                    ->orWhere('last_name',   'LIKE', "%{$keywords}%")
                    ->orWhere('visitors_ids_number',  'LIKE', "%{$keywords}%")
                    ->orWhere('phone_number','LIKE', "%{$keywords}%")
                    ->orWhereHas('visitorType', function ($qt) use ($keywords) {
                        $qt->where('name', 'LIKE', "%{$keywords}%");
                    });
            });
        }

        // Apply date filters
        if (!empty($this->filters['date_from'])) {
            $query->whereDate('created_at', '>=', $this->filters['date_from']);
        }

        if (!empty($this->filters['date_to'])) {
            $query->whereDate('created_at', '<=', $this->filters['date_to']);
        }

        // Apply visitor type filter
        if (!empty($this->filters['visitor_type'])) {
            $query->where('visitors_type_id', $this->filters['visitor_type']);
        }

        $data = $query->orderBy('id', 'desc')->get();

        $companyLocations = collect(session('all_location', []))->keyBy(function ($item) {
            return (string) data_get($item, 'id');
        });
        $guardLocations = Location::query()->get(['id', 'name'])->keyBy(function ($item) {
            return (string) $item->id;
        });

        return $data->map(function ($visitor) use ($companyLocations, $guardLocations) {
            $locationLabel = (string) $visitor->location;

            if (is_numeric($locationLabel)) {
                $companyLocation = $companyLocations->get($locationLabel);
                $guardLocation = $guardLocations->get($locationLabel);

                if ($companyLocation) {
                    $locationLabel = data_get($companyLocation, 'name', '');
                } elseif ($guardLocation) {
                    $locationLabel = $guardLocation->name;
                }
            }

            $status = $visitor->status == 1 ? 'Timed Out' : 'Active';

            $fullName = trim(implode(' ', array_filter([
                $visitor->first_name,
                $visitor->middle_name,
                $visitor->last_name
            ])));

            $timeIn  = $visitor->time_in  ? \Carbon\Carbon::parse($visitor->time_in)->format('h:i A') : '-';
            $timeOut = $visitor->time_out ? \Carbon\Carbon::parse($visitor->time_out)->format('h:i A') : '-';

            return [
                'Name'         =>     $fullName,
                'Location'     =>     $locationLabel,
                'Phone'        =>     $visitor->phone_number,
                'Visitor Type' =>     $visitor->visitorType?->name ?? '-',
                'ID Number'    =>     $visitor->visitors_ids_number,
                'Date Visited' =>     $visitor->created_at->format('F d, Y'),
                'Day'          =>     $visitor->created_at->format('l'),
                'Time In'      =>     $timeIn,
                'Time Out'     =>     $timeOut,
                'Status'       =>     $status,
                'Timed In By'  =>     user_name($visitor->created_by) ?? '-',
                'Timed Out By' =>     user_name($visitor->updated_by) ?? '-',
                'Created At'   =>     $visitor->created_at->format('F j, Y H:i A'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Name',
            'Location',
            'Phone',
            'Visitor Type',
            'ID Number',
            'Date Visited',
            'Day',
            'Time In',
            'Time Out',
            'Status',
            'Timed In By',
            'Timed Out By',
            'Created At',
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