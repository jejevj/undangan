@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 offset-lg-1">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Detail Kampanye: {{ $campaign->name }}</h4>
                    <div>
                        <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn btn-warning btn-sm">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Nama Kampanye:</th>
                                    <td>{{ $campaign->name }}</td>
                                </tr>
                                <tr>
                                    <th>Kode:</th>
                                    <td><code>{{ $campaign->code }}</code></td>
                                </tr>
                                <tr>
                                    <th>URL Registrasi:</th>
                                    <td>
                                        <a href="{{ route('register') }}?ref={{ $campaign->code }}" target="_blank" class="text-primary">
                                            {{ route('register') }}?ref={{ $campaign->code }}
                                        </a>
                                        <button class="btn btn-sm btn-outline-secondary ml-2" onclick="copyToClipboard('{{ route('register') }}?ref={{ $campaign->code }}')">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Deskripsi:</th>
                                    <td>{{ $campaign->description ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Plan:</th>
                                    <td><span class="badge badge-info">{{ $campaign->pricingPlan->name }}</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Status:</th>
                                    <td><span class="badge {{ $campaign->getStatusBadgeClass() }}">{{ $campaign->getStatusLabel() }}</span></td>
                                </tr>
                                <tr>
                                    <th>Kuota:</th>
                                    <td>
                                        @if($campaign->max_users > 0)
                                            <strong>{{ $campaign->used_count }}</strong> / {{ $campaign->max_users }} user
                                            <br><small class="text-muted">Sisa: {{ $campaign->getRemainingSlots() }}</small>
                                        @else
                                            Unlimited <small class="text-muted">({{ $campaign->used_count }} user terdaftar)</small>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tanggal Mulai:</th>
                                    <td>{{ $campaign->start_date ? $campaign->start_date->format('d M Y') : 'Langsung aktif' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Berakhir:</th>
                                    <td>{{ $campaign->end_date ? $campaign->end_date->format('d M Y') : 'Tanpa batas' }}</td>
                                </tr>
                                <tr>
                                    <th>Dibuat:</th>
                                    <td>{{ $campaign->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <h5 class="mb-3">User yang Menggunakan Kampanye Ini ({{ $campaign->users->count() }})</h5>
                    
                    @if($campaign->users->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Tanggal Daftar</th>
                                    <th>Status Subscription</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($campaign->users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                                    <td>
                                        @php $activeSub = $user->activeSubscription(); @endphp
                                        @if($activeSub)
                                            <span class="badge badge-success">{{ $activeSub->plan->name }}</span>
                                        @else
                                            <span class="badge badge-secondary">Tidak ada</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        Belum ada user yang menggunakan kampanye ini.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('URL berhasil disalin!');
    }, function(err) {
        console.error('Gagal menyalin: ', err);
    });
}
</script>
@endsection
