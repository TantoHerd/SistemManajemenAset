<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reminder Maintenance</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 20px; text-align: center; color: white;">
            <h2 style="margin: 0;">SIMASET</h2>
            <p>Reminder Maintenance</p>
        </div>
        
        <div style="padding: 20px; border: 1px solid #ddd;">
            <p>Yth. {{ $user->name }},</p>
            
            <p>Ini adalah pengingat bahwa jadwal maintenance akan segera dilaksanakan:</p>
            
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px; background: #f5f5f5; width: 120px;"><strong>Judul</strong></td>
                    <td style="padding: 8px;">{{ $maintenance->title }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; background: #f5f5f5;"><strong>Tanggal</strong></td>
                    <td style="padding: 8px;">{{ $date }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; background: #f5f5f5;"><strong>Aset</strong></td>
                    <td style="padding: 8px;">{{ $maintenance->asset->name ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; background: #f5f5f5;"><strong>Teknisi</strong></td>
                    <td style="padding: 8px;">{{ $maintenance->technician ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; background: #f5f5f5;"><strong>Pengingat</strong></td>
                    <td style="padding: 8px;">H-{{ $daysBefore }}</td>
                </tr>
            </table>
            
            <p style="margin-top: 20px;">
                <a href="{{ url('/admin/maintenances/' . $maintenance->id) }}" 
                   style="background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                    Lihat Detail Maintenance
                </a>
            </p>
            
            <p>Terima kasih.</p>
            <p><strong>Tim SIMASET</strong></p>
        </div>
        
        <div style="text-align: center; padding: 20px; font-size: 12px; color: #999;">
            © {{ date('Y') }} SIMASET - Sistem Manajemen Aset IT
        </div>
    </div>
</body>
</html>