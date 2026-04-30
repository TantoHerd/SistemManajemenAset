<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Maintenance;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Cek dan generate semua notifikasi
     */
    public static function checkAndGenerate()
    {
        self::checkWarrantyExpiring();
        self::checkMaintenanceDue();
        self::checkAssetOverdue();
    }

    /**
     * Cek garansi yang hampir habis
     */
    private static function checkWarrantyExpiring()
    {
        $assets = Asset::whereNotNull('warranty_expiry')
            ->where('warranty_expiry', '>=', now())
            ->where('warranty_expiry', '<=', now()->addDays(30))
            ->get();

        foreach ($assets as $asset) {
            $daysUntil = now()->diffInDays($asset->warranty_expiry);
            
            // Notifikasi H-30 dan H-7
            if (in_array($daysUntil, [30, 7, 3, 1])) {
                // Cek apakah sudah ada notifikasi yang sama (hindari duplikat)
                $exists = Notification::where('type', Notification::TYPE_WARRANTY_EXPIRING)
                    ->where('message', 'like', "%{$asset->id}%")
                    ->whereDate('created_at', now())
                    ->exists();

                if (!$exists) {
                    $title = "Garansi Akan Berakhir";
                    $message = "Aset <strong>{$asset->name}</strong> ({$asset->asset_code}) garansinya akan berakhir dalam <strong>{$daysUntil} hari</strong> ({$asset->warranty_expiry->format('d M Y')})";
                    $link = route('admin.assets.show', $asset);

                    // Kirim ke semua admin
                    $admins = User::where('role', 'admin')->get();
                    foreach ($admins as $admin) {
                        Notification::createNotification(
                            $admin->id,
                            Notification::TYPE_WARRANTY_EXPIRING,
                            $title,
                            $message,
                            $link
                        );
                    }
                }
            }
        }
    }

    /**
     * Cek maintenance yang terjadwal
     */
    private static function checkMaintenanceDue()
    {
        $maintenances = Maintenance::with('asset')
            ->where('status', 'pending')
            ->whereNotNull('maintenance_date')
            ->whereDate('maintenance_date', '<=', now()->addDays(2))
            ->whereDate('maintenance_date', '>=', now())
            ->get();

        foreach ($maintenances as $maintenance) {
            $daysUntil = now()->diffInDays($maintenance->maintenance_date);
            $label = $daysUntil == 0 ? 'hari ini' : "besok";

            $exists = Notification::where('type', Notification::TYPE_MAINTENANCE_DUE)
                ->where('message', 'like', "%{$maintenance->id}%")
                ->whereDate('created_at', now())
                ->exists();

            if (!$exists) {
                $title = "Maintenance Terjadwal";
                $message = "Maintenance <strong>{$maintenance->title}</strong> untuk aset <strong>{$maintenance->asset->name}</strong> dijadwalkan <strong>{$label}</strong> ({$maintenance->maintenance_date->format('d M Y')})";
                $link = route('admin.maintenances.show', $maintenance);

                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    Notification::createNotification(
                        $admin->id,
                        Notification::TYPE_MAINTENANCE_DUE,
                        $title,
                        $message,
                        $link
                    );
                }
            }
        }
    }

    /**
     * Cek aset yang overdue (belum check-in)
     */
    private static function checkAssetOverdue()
    {
        // Aset yang statusnya masih in_use lebih dari 30 hari
        // Ini contoh sederhana, bisa disesuaikan dengan logika bisnis
        $assets = Asset::where('status', 'in_use')
            ->where('updated_at', '<=', now()->subDays(30))
            ->get();

        foreach ($assets as $asset) {
            $daysOverdue = $asset->updated_at->diffInDays(now());

            $exists = Notification::where('type', Notification::TYPE_ASSET_OVERDUE)
                ->where('message', 'like', "%{$asset->id}%")
                ->whereDate('created_at', now())
                ->exists();

            if (!$exists) {
                $userName = $asset->assignedTo->name ?? 'Tidak diketahui';
                $title = "Aset Overdue";
                $message = "Aset <strong>{$asset->name}</strong> ({$asset->asset_code}) sudah <strong>{$daysOverdue} hari</strong> digunakan oleh <strong>{$userName}</strong> tanpa check-in";
                $link = route('admin.assets.show', $asset);

                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    Notification::createNotification(
                        $admin->id,
                        Notification::TYPE_ASSET_OVERDUE,
                        $title,
                        $message,
                        $link
                    );
                }
            }
        }
    }
}