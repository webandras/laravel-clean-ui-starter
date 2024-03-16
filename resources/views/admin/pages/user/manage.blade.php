@extends('admin.layouts.admin')

@section('content')

    <main class="padding-1">
        <nav class="breadcrumb breadcrumb-left">
            <ol>
                <li>
                    <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                </li>
                <li>
                    <i class="fa-solid fa-angle-right"></i>
                </li>
                <li>{{ __('Manage Users') }}</li>
            </ol>
        </nav>

        <h1 class="h3 margin-top-bottom-0">{{ __('Manage users') }}</h1>

        <div class="main-content">

            <!-- Create new user -->
            <livewire:admin.user.create title="{{ __('New user') }}"
                                  :roles="$roles"
                                  :permissions="$permissions"
                                  :hasSmallButton="false"
                                  :modalId="'m-create-user'">
            </livewire:admin.user.create>

            <table>
                <thead>
                <tr class="fs-14">
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Role') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($users as $user)
                    <tr>
                        <td><b>{{ $user->name }}</b></td>
                        <td class="fs-14">{{ $user->email }}</td>
                        <td class="fs-14">{{ isset($user->role) ? $user->role->name : '' }}</td>
                        <td>
                            <div class="flex flex-row">

                                @if(auth()->user()->hasRoles('super-administrator') )

                                    <!-- Superadmins cannot be deleted or edited -->
                                    @if (!$user->hasRoles('super-administrator'))
                                    <!-- Delete user -->
                                    <livewire:admin.user.delete title="{{ __('Delete user') }}"
                                                          :user="$user"
                                                          :hasSmallButton="false"
                                                          :modalId="'m-delete-user-' . $user->id"
                                    >
                                    </livewire:admin.user.delete>

                                    <!-- Update user -->
                                    <livewire:admin.user.edit title="{{ __('Edit user') }}"
                                                        :user="$user"
                                                        :roles="$roles"
                                                        :permissions="$permissions"
                                                        :hasSmallButton="false"
                                                        :modalId="'m-edit-user-' . $user->id"
                                    >
                                    </livewire:admin.user.edit>
                                    @endif

                                @else
                                    <p class="fs-14 italic">{{ __('Super-admin cannot be deleted or edited here.') }}</p>
                                @endif
                            </div>

                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </main>
@endsection
