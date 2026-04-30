<footer class="footer py-3 bg-light border-top">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                <span class="text-muted">&copy; {{ date('Y') }} {{ $companyName ?? 'PT. NAMA PERUSAHAAN' }}</span>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <span class="text-muted"><i class="bi bi-box-seam"></i> {{ $systemName }} v1.0</span>
            </div>
        </div>
    </div>
</footer>