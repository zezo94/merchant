<div class="card section-card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span class="fw-bold">قائمة المستخدمين</span>
        <span class="badge bg-primary badge-status">{{ $users->total() }} مستخدم</span>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle table-hover users-table">
                <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الدور</th>
                    <th>Root</th>
                    <th>تاريخ الإنشاء</th>
                    <th>إجراءات</th>
                </tr>
                </thead>

                <tbody>
                @forelse($users as $user)
                    @php
                        $isTargetSuperAdmin = $user->hasRole('super-admin');
                        $isTargetRoot = (bool) $user->is_root;
                        $canManageTarget = $isRoot ? true : (!$isTargetRoot && !$isTargetSuperAdmin);
                    @endphp

                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @forelse($user->roles as $role)
                                <span class="badge bg-dark user-role-badge">{{ $role->name }}</span>
                            @empty
                                <span class="text-muted">بدون دور</span>
                            @endforelse
                        </td>
                        <td>
                            @if($user->is_root)
                                <span class="badge bg-danger">ROOT</span>
                            @else
                                <span class="text-muted">لا</span>
                            @endif
                        </td>
                        <td>{{ optional($user->created_at)->format('Y-m-d H:i') }}</td>
                        <td>
                            @include('users.partials.user-index-row-actions', [
                                'user' => $user,
                                'canManageTarget' => $canManageTarget,
                                'isTargetRoot' => $isTargetRoot,
                            ])
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">لا يوجد مستخدمون</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
</div>
