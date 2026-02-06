<?php

namespace App\Exports;

use App\Models\Visitor;
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
        $query = Visitor::with('visitorType')
            ->where('status', 0)
            ->withoutTrashed();

        // Apply search filter
        if (!empty($this->filters['search'])) {
            $keywords = strtolower($this->filters['search']);
            $query->where(function ($q) use ($keywords) {
                $q->where('first_name', 'LIKE', "%{$keywords}%")
                    ->orWhere('middle_name', 'LIKE', "%{$keywords}%")
                    ->orWhere('last_name', 'LIKE', "%{$keywords}%")
                    ->orWhere('visitor_id', 'LIKE', "%{$keywords}%")
                    ->orWhere('phone_number', 'LIKE', "%{$keywords}%")
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
            $query->where('visitor_type', $this->filters['visitor_type']);
        }

        $data = $query->orderBy('id', 'desc')->get();

        $location = collect(session('all_location', []));

        return $data->map(function ($visitor) use ($location) {
            $locationLabel = '';
            foreach ($location as $record) {
                if ($visitor->location == $record['id']) {
                    $locationLabel = $record['name'];
                    break;
                }
            }

            $status = $visitor->status == 0 ? 'Active' : 'Timed Out';

            $fullName = trim(implode(' ', array_filter([
                $visitor->first_name,
                $visitor->middle_name,
                $visitor->last_name
            ])));

            $timeIn = $visitor->time_in ? \Carbon\Carbon::parse($visitor->time_in)->format('h:i A') : '-';
            $timeOut = $visitor->time_out ? \Carbon\Carbon::parse($visitor->time_out)->format('h:i A') : '-';

            return [
                'Name' => $fullName,
                'Location' => $locationLabel,
                'Phone' => $visitor->phone_number,
                'Visitor Type' => $visitor->visitorType?->name ?? '-',
                'ID Number' => $visitor->visitor_id,
                'Date Visited' => $visitor->created_at->format('F d, Y'),
                'Day' => $visitor->created_at->format('l'),
                'Time In' => $timeIn,
                'Time Out' => $timeOut,
                'Status' => $status,
                'Created By' => $visitor->getEmpName($visitor->created_by),
                'Updated By' => $visitor->getEmpName($visitor->updated_by) ?? '-',
                'Created At' => $visitor->created_at->format('F j, Y H:i A'),
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
            'Created By',
            'Updated By',
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
