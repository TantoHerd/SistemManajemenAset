<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetSpecification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class NetworkController extends Controller
{
    /**
     * List semua perangkat jaringan
     */
    public function devices(Request $request)
    {
        $categoryIds = \App\Models\Category::whereIn('name', [
            'Router', 'Access Point', 'Desktop PC', 
            'Server', 'Digital Video Recorder', 'Kamera CCTV', 
            'Komputer All In One', 'Laptop', 'Network Attached Storage', 
            'Network Switch', 'Network Video Recorder', 'Printer'
        ])->pluck('id')->toArray();

        $query = Asset::with(['category', 'location', 'specifications'])
            ->whereIn('category_id', $categoryIds)
            ->whereHas('specifications', function($q) {
                $q->where('spec_key', 'ip_address')
                ->whereNotNull('spec_value')
                ->where('spec_value', '!=', '');
            });

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->category . '%');
            });
        }

        // Search by IP
        if ($request->has('ip')) {
            $query->whereHas('specifications', function($q) use ($request) {
                $q->where('spec_key', 'ip_address')
                ->where('spec_value', 'LIKE', '%' . $request->ip . '%');
            });
        }

        $devices = $query->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $devices->map(function($device) {
                return $this->formatDevice($device);
            }),
            'meta' => [
                'current_page' => $devices->currentPage(),
                'last_page' => $devices->lastPage(),
                'per_page' => $devices->perPage(),
                'total' => $devices->total(),
            ]
        ]);
    }

    /**
     * Detail perangkat jaringan
     */
    public function deviceDetail($id)
    {
        $device = Asset::with(['category', 'location', 'specifications'])
            ->find($id);

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatDevice($device, true)
        ]);
    }

    /**
     * Cari perangkat berdasarkan IP
     */
    public function deviceByIp(Request $request)
    {
        $request->validate([
            'ip' => 'required|ip'
        ]);

        $spec = AssetSpecification::where('spec_key', 'ip_address')
            ->where('spec_value', $request->ip)
            ->first();

        if (!$spec) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat dengan IP ' . $request->ip . ' tidak ditemukan'
            ], 404);
        }

        $device = Asset::with(['category', 'location', 'specifications'])
            ->find($spec->asset_id);

        return response()->json([
            'success' => true,
            'data' => $this->formatDevice($device, true)
        ]);
    }

    /**
     * Ping perangkat
     */
    public function ping($id)
    {
        $device = Asset::with('specifications')->find($id);

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat tidak ditemukan'
            ], 404);
        }

        $ip = $device->specifications->where('spec_key', 'ip_address')->first();

        if (!$ip) {
            return response()->json([
                'success' => false,
                'message' => 'IP Address tidak ditemukan untuk perangkat ini'
            ], 404);
        }

        // Ping menggunakan sistem operasi
        $pingResult = $this->pingDevice($ip->spec_value);

        // Update response time di database
        if ($pingResult['success']) {
            $spec = AssetSpecification::where('asset_id', $device->id)
                ->where('spec_key', 'response_time')
                ->first();

            if ($spec) {
                $spec->update(['spec_value' => $pingResult['response_time']]);
            } else {
                AssetSpecification::create([
                    'asset_id' => $device->id,
                    'spec_key' => 'response_time',
                    'spec_value' => $pingResult['response_time']
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'asset_id' => $device->id,
                'name' => $device->name,
                'ip_address' => $ip->spec_value,
                'status' => $pingResult['success'] ? 'up' : 'down',
                'response_time_ms' => $pingResult['response_time'] ?? null,
                'packet_loss' => $pingResult['packet_loss'] ?? 0,
                'last_check' => now()->toDateTimeString()
            ]
        ]);
    }

    /**
     * Ringkasan status semua perangkat
     */
    public function statusSummary()
    {
        $categoryIds = \App\Models\Category::whereIn('name', [
            'Router', 'Switch', 'Access Point', 'Firewall', 'Server'
        ])->pluck('id')->toArray();

        $devices = Asset::with('specifications')
            ->whereIn('category_id', $categoryIds)
            ->get();

        $summary = [
            'total' => $devices->count(),
            'by_status' => [
                'active' => $devices->where('status', 'available')->count(),
                'in_use' => $devices->where('status', 'in_use')->count(),
                'maintenance' => $devices->where('status', 'maintenance')->count(),
                'damaged' => $devices->where('status', 'damaged')->count(),
            ],
            'by_category' => [],
            'ip_list' => [],
        ];

        // Per kategori
        foreach ($devices->groupBy('category_id') as $categoryId => $items) {
            $category = \App\Models\Category::find($categoryId);
            $summary['by_category'][$category->name] = $items->count();
        }

        // List IP
        foreach ($devices as $device) {
            $ip = $device->specifications->where('spec_key', 'ip_address')->first();
            if ($ip) {
                $summary['ip_list'][] = [
                    'asset_id' => $device->id,
                    'name' => $device->name,
                    'ip_address' => $ip->spec_value,
                    'status' => $device->status
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $summary,
            'last_updated' => now()->toDateTimeString()
        ]);
    }

    /**
     * Discover network (scan IP range)
     */
    public function discover(Request $request)
    {
        $request->validate([
            'subnet' => 'required|string',
            'start' => 'required|integer|min:1|max:254',
            'end' => 'required|integer|min:1|max:254',
        ]);

        $subnet = $request->subnet;
        $start = $request->start;
        $end = $request->end;

        $results = [];

        for ($i = $start; $i <= $end; $i++) {
            $ip = $subnet . '.' . $i;
            $ping = $this->pingDevice($ip);

            $results[] = [
                'ip' => $ip,
                'status' => $ping['success'] ? 'up' : 'down',
                'response_time' => $ping['response_time'] ?? null,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $results,
            'summary' => [
                'total' => count($results),
                'up' => count(array_filter($results, fn($r) => $r['status'] == 'up')),
                'down' => count(array_filter($results, fn($r) => $r['status'] == 'down')),
            ]
        ]);
    }

    /**
     * Health check semua perangkat (ping all)
     */
    public function healthCheck()
    {
        $categoryIds = \App\Models\Category::whereIn('name', [
            'Router', 'Switch', 'Access Point', 'Firewall', 'Server'
        ])->pluck('id')->toArray();

        $devices = Asset::with('specifications')
            ->whereIn('category_id', $categoryIds)
            ->get();

        $results = [];

        foreach ($devices as $device) {
            $ip = $device->specifications->where('spec_key', 'ip_address')->first();
            
            if ($ip) {
                $ping = $this->pingDevice($ip->spec_value);
                $results[] = [
                    'asset_id' => $device->id,
                    'name' => $device->name,
                    'ip_address' => $ip->spec_value,
                    'status' => $ping['success'] ? 'up' : 'down',
                    'response_time' => $ping['response_time'] ?? null,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $results,
            'summary' => [
                'total' => count($results),
                'up' => count(array_filter($results, fn($r) => $r['status'] == 'up')),
                'down' => count(array_filter($results, fn($r) => $r['status'] == 'down')),
            ],
            'checked_at' => now()->toDateTimeString()
        ]);
    }

    /**
     * Statistik network
     */
    public function statistics()
    {
        $categoryIds = \App\Models\Category::whereIn('name', [
            'Router', 'Switch', 'Access Point', 'Firewall', 'Server'
        ])->pluck('id')->toArray();

        $devices = Asset::with('specifications')
            ->whereIn('category_id', $categoryIds)
            ->get();

        $total = $devices->count();
        $avgCpu = 0;
        $avgRam = 0;
        $avgResponse = 0;
        $cpuCount = 0;
        $ramCount = 0;
        $responseCount = 0;

        foreach ($devices as $device) {
            $cpu = $device->specifications->where('spec_key', 'cpu_usage')->first();
            if ($cpu) {
                $avgCpu += (float) $cpu->spec_value;
                $cpuCount++;
            }

            $ram = $device->specifications->where('spec_key', 'ram_usage')->first();
            if ($ram) {
                $avgRam += (float) $ram->spec_value;
                $ramCount++;
            }

            $response = $device->specifications->where('spec_key', 'response_time')->first();
            if ($response) {
                $avgResponse += (float) $response->spec_value;
                $responseCount++;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'total_devices' => $total,
                'average_cpu_usage' => $cpuCount > 0 ? round($avgCpu / $cpuCount, 1) : null,
                'average_ram_usage' => $ramCount > 0 ? round($avgRam / $ramCount, 1) : null,
                'average_response_time' => $responseCount > 0 ? round($avgResponse / $responseCount, 1) : null,
            ]
        ]);
    }

    // ============ PRIVATE METHODS ============

    /**
     * Format device untuk response API
     */
    private function formatDevice($device, $detail = false)
    {
        $specs = [];
        foreach ($device->specifications as $spec) {
            $specs[$spec->spec_key] = $spec->spec_value;
        }

        $data = [
            'id' => $device->id,
            'asset_code' => $device->asset_code,
            'name' => $device->name,
            'category' => $device->category->name ?? null,
            'location' => $device->location->name ?? null,
            'status' => $device->status,
            'status_label' => $device->status_label,
            'ip_address' => $specs['ip_address'] ?? null,
            'mac_address' => $specs['mac_address'] ?? null,
            'specifications' => $specs,
            'created_at' => $device->created_at->toDateTimeString(),
            'updated_at' => $device->updated_at->toDateTimeString(),
        ];

        if ($detail) {
            $data['specifications'] = $specs;
            $data['last_ping'] = $specs['last_ping'] ?? null;
        }

        return $data;
    }

    /**
     * Ping device menggunakan system ping
     */
    private function pingDevice($ip, $count = 1)
    {
        $os = strtoupper(substr(PHP_OS, 0, 3));
        
        if ($os === 'WIN') {
            $command = "ping -n {$count} -w 1000 {$ip}";
        } else {
            $command = "ping -c {$count} -W 1 {$ip}";
        }

        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            // Parse ping response untuk dapat response time
            $responseTime = null;
            foreach ($output as $line) {
                if (preg_match('/time[=<](\d+\.?\d*)\s*ms/', $line, $matches)) {
                    $responseTime = (float) $matches[1];
                    break;
                }
            }

            return [
                'success' => true,
                'response_time' => $responseTime,
                'packet_loss' => 0,
            ];
        }

        return [
            'success' => false,
            'response_time' => null,
            'packet_loss' => 100,
        ];
    }

    public function devicesWithIp(Request $request)
    {
        $categoryIds = \App\Models\Category::whereIn('name', [
            'Router', 'Access Point', 'Desktop PC', 
            'Server', 'Digital Video Recorder', 'Kamera CCTV', 
            'Komputer All In One', 'Laptop', 'Network Attached Storage', 
            'Network Switch', 'Network Video Recorder', 'Printer'
        ])->pluck('id')->toArray();

        $query = Asset::with(['category', 'location', 'specifications'])
            ->whereIn('category_id', $categoryIds)
            ->whereHas('specifications', function($q) {
                $q->where('spec_key', 'ip_address')
                ->whereNotNull('spec_value')
                ->where('spec_value', '!=', '');
            })
            ->orderBy('name');

        $devices = $query->get();

        return response()->json([
            'success' => true,
            'data' => $devices->map(function($device) {
                return $this->formatDevice($device);
            }),
            'summary' => [
                'total' => $devices->count(),
                'by_category' => $devices->groupBy('category.name')->map->count(),
            ]
        ]);
    }
}