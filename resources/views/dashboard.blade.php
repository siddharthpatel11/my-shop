<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="row g-4">
                {{-- Welcome Card --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
                        <div class="card-body p-0">
                            <div class="d-flex align-items-center p-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="bg-white bg-opacity-25 rounded-circle p-3 me-4">
                                    <i class="fas fa-user-check text-white fa-2x"></i>
                                </div>
                                <div>
                                    <h3 class="text-white fw-bold mb-1">Welcome back, {{ auth()->user()->name }}!</h3>
                                    <p class="text-white text-opacity-75 mb-0">You have successfully authenticated with 2FA. Your session is secure.</p>
                                </div>
                            </div>
                            <div class="p-4 bg-white">
                                <div class="row text-center g-4">
                                    <div class="col-md-3 border-end">
                                        <h6 class="text-muted small text-uppercase mb-2">Account Status</h6>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                                            <i class="fas fa-check-circle me-1"></i> Active
                                        </span>
                                    </div>
                                    <div class="col-md-3 border-end">
                                        <h6 class="text-muted small text-uppercase mb-2">Security</h6>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill">
                                            <i class="fas fa-shield-alt me-1"></i> 2FA Enabled
                                        </span>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-center align-items-center h-100">
                                            <p class="mb-0 text-muted">
                                                <i class="far fa-clock me-1"></i> Last login activity: {{ now()->format('M d, Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions or Placeholder Stats --}}
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 15px;">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                                <i class="fas fa-box text-primary"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">Products</h5>
                        </div>
                        <p class="text-muted small">Manage your inventory and product listings.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-sm mt-2 rounded-pill">View Products</a>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 15px;">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3">
                                <i class="fas fa-shopping-bag text-success"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">Orders</h5>
                        </div>
                        <p class="text-muted small">Track and process customer orders.</p>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-success btn-sm mt-2 rounded-pill">View Orders</a>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 15px;">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 p-2 rounded-3 me-3">
                                <i class="fas fa-envelope text-info"></i>
                            </div>
                            <h5 class="mb-0 fw-bold">Contacts</h5>
                        </div>
                        <p class="text-muted small">Respond to customer inquiries and messages.</p>
                        <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-info btn-sm mt-2 rounded-pill">View Messages</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
