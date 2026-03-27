@extends('layouts.app')
@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('breadcrumb')
    <li class="breadcrumb-item active">Manajemen User</li>
@endsection

@section('content')
<div class="mb-3">
    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
        <i class="fa fa-plus"></i> Tambah User
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Paket</th>
                        <th>Undangan</th>
                        <th>Bergabung</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                    @php
                        $plan = $user->isAdmin() ? null : $user->activePlan();
                    @endphp
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <a href="{{ route('users.show', $user) }}" class="fw-bold text-dark">
                                {{ $user->name }}
                            </a>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge badge-primary me-1">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            @if($user->isAdmin())
                                <span class="badge badge-dark">Admin</span>
                            @elseif($plan)
                                <span class="badge badge-{{ $plan->badge_color }}">{{ $plan->name }}</span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-light text-dark">{{ $user->invitations_count }}</span>
                        </td>
                        <td><small class="text-muted">{{ $user->created_at->format('d M Y') }}</small></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('users.show', $user) }}" class="btn btn-info btn-xs" title="Detail">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-xs" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                                    data-confirm="Hapus user '{{ $user->name }}'?"
                                    data-confirm-ok="Hapus" data-confirm-title="Hapus User">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada user.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
