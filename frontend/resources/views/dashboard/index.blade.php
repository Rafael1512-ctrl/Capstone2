@extends('layouts.app')

@section('content')
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="mb-6">
          <h1 class="fs-3 mb-1">Dashboard</h1>
          <p>Welcome back! Here's what's happening with your inventory.</p>
        </div>
      </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-3">
      <div class="col-lg-3.col-12">
        <div class="card p-4 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-2">
          <div class="d-flex gap-3">
            <div class="icon-shape icon-md bg-primary text-white rounded-2">
              <i class="ti ti-report-analytics fs-4"></i>
            </div>
            <div>
              <h2 class="mb-3 fs-6">Total Sales</h2>
              <h3 class="fw-bold mb-0">${{ number_format($totalSales ?? 0, 2) }}</h3>
              <p class="text-primary mb-0 small">Completed orders</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3.col-12">
        <div class="card p-4 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-2">
          <div class="d-flex gap-3">
            <div class="icon-shape icon-md bg-success text-white rounded-2">
              <i class="ti ti-box-seam fs-4"></i>
            </div>
            <div>
              <h2 class="mb-3 fs-6">Total Products</h2>
              <h3 class="fw-bold mb-0">{{ $totalProducts ?? 0 }}</h3>
              <p class="text-success mb-0 small">Items in inventory</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3.col-12">
        <div class="card p-4 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-2">
          <div class="d-flex gap-3">
            <div class="icon-shape icon-md bg-warning text-white rounded-2">
              <i class="ti ti-alert-triangle fs-4"></i>
            </div>
            <div>
              <h2 class="mb-3 fs-6">Low Stock</h2>
              <h3 class="fw-bold mb-0">{{ $lowStock ?? 0 }}</h3>
              <p class="text-warning mb-0 small">Items ≤ 10 units</p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3.col-12">
        <div class="card p-4 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-2">
          <div class="d-flex gap-3">
            <div class="icon-shape icon-md bg-danger text-white rounded-2">
              <i class="ti ti-package-off fs-4"></i>
            </div>
            <div>
              <h2 class="mb-3 fs-6">Out of Stock</h2>
              <h3 class="fw-bold mb-0">{{ $outOfStock ?? 0 }}</h3>
              <p class="text-danger mb-0 small">Need restock</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Profit, Refund, Expenses Cards -->
    <div class="row g-3 mb-3">
      <div class="col-lg-4.col-12">
        <div class="card">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between border-bottom pb-5 mb-3">
              <div>
                <h3 class="fw-bold h4">${{ number_format($totalProfit ?? 0, 2) }}</h3>
                <span>Total Profit</span>
              </div>
              <div>
                <i class="ti ti-layers-subtract fs-1 text-primary"></i>
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center small">
              <div class="text-muted">
                <span class="text-success">+35%</span> vs Last Month
              </div>
              <div><a class="link-primary text-decoration-underline" href="/reports">View</a></div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4.col-12">
        <div class="card">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between border-bottom pb-5 mb-3">
              <div>
                <h3 class="fw-bold h4">${{ number_format($totalRefunds ?? 0, 2) }}</h3>
                <span>Total Cancelled / Refunds</span>
              </div>
              <div>
                <i class="ti ti-credit-card fs-1 text-danger"></i>
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center small">
              <div class="text-muted">
                <span class="text-danger">-20%</span> vs Last Month
              </div>
              <div><a class="link-primary text-decoration-underline" href="/reports">View</a></div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4.col-12">
        <div class="card">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between border-bottom pb-5 mb-3">
              <div>
                <h3 class="fw-bold h4">${{ number_format($totalExpenses ?? 0, 2) }}</h3>
                <span>Total Expenses</span>
              </div>
              <div>
                <i class="ti ti-cash-banknote fs-1 text-warning"></i>
              </div>
            </div>
            <div class="d-flex justify-content-between align-items-center small">
              <div class="text-muted">
                <span class="text-warning">-20%</span> vs Last Month
              </div>
              <div><a class="link-primary text-decoration-underline" href="/reports">View</a></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-3">
      <div class="col-12.col-lg-6">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center bg-transparent px-4 py-3">
            <h3 class="h5 mb-0">Sales vs Purchase</h3>
            <div>
              <select class="form-select form-select-sm">
                <option selected>This Year</option>
                <option>This Month</option>
                <option>This Week</option>
              </select>
            </div>
          </div>
          <div class="card-body p-4">
            <div id="salesPurchaseChart"></div>
          </div>
        </div>
      </div>
      <div class="col-12.col-lg-6">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center bg-transparent px-4 py-3">
            <h3 class="h5 mb-0">Overall Information</h3>
            <div>
              <select class="form-select form-select-sm">
                <option selected>Last 6 Months</option>
                <option>This Month</option>
                <option>This Week</option>
              </select>
            </div>
          </div>
          <div class="card-body p-4">
            <h3 class="h6">Customers Overview</h3>
            <div class="row align-items-center">
              <div class="col-sm-6">
                <div id="customerChart"></div>
              </div>
              <div class="col-sm-6">
                <div class="row">
                  <div class="col-6 border-end">
                    <div class="text-center">
                      <h2 class="mb-1">5.5K</h2>
                      <p class="text-success mb-2">First Time</p>
                      <span class="badge bg-success">
                        <i class="ti ti-arrow-up-left me-1"></i>25%
                      </span>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="text-center">
                      <h2 class="mb-1">3.5K</h2>
                      <p class="text-warning mb-2">Return</p>
                      <span class="badge bg-success badge-xs d-inline-flex align-items-center">
                        <i class="ti ti-arrow-up-left me-1"></i>21%
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row text-center border-top mt-4 pt-4">
              <div class="col-4 border-end">
                <h3 class="fw-bold mb-2">6,987</h3>
                <small class="text-secondary">Suppliers</small>
              </div>
              <div class="col-4 border-end">
                <h3 class="fw-bold mb-2">4,896</h3>
                <small class="text-secondary">Customers</small>
              </div>
              <div class="col-4">
                <h3 class="fw-bold mb-2">487</h3>
                <small class="text-secondary">Orders</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Cards -->
    <div class="row g-3">
      <!-- Top Selling Products -->
      <div class="col-lg-4">
        <div class="card h-100">
          <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
            <h4 class="mb-0 h5">Top Selling Products</h4>
            <button class="btn btn-sm btn-outline-secondary">
              <i class="ti ti-calendar"></i> Today
            </button>
          </div>
          <ul class="list-group list-group-flush">
            @if ($hasTopProducts)
              @foreach ($topProducts as $p)
                <li class="list-group-item d-flex align-items-center gap-3">
                  <img class="rounded" src="/assets/images/{{ $p['image'] }}" width="48">
                  <div class="flex-grow-1">
                    <p class="mb-1">{{ $p['name'] }}</p>
                    <div class="d-flex align-items-center gap-2 text-muted">
                      <small class="fw-semibold">${{ number_format($p['price'], 2) }}</small>
                      <small>•</small>
                      <small>{{ $p['units_sold'] }} Units</small>
                    </div>
                  </div>
                </li>
              @endforeach
            @else
              <li class="list-group-item text-muted text-center">No data available</li>
            @endif
          </ul>
        </div>
      </div>

      <!-- Low Stock Products -->
      <div class="col-lg-4">
        <div class="card h-100">
          <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
            <div class="d-flex align-items-center">
              <h4 class="mb-0 h5">Low Stock Products</h4>
            </div>
            <a class="small text-primary text-decoration-underline" href="/inventory">View All</a>
          </div>
          <ul class="list-group list-group-flush">
            @if ($hasLowStockProducts)
              @foreach ($lowStockProducts as $p)
                <li class="list-group-item d-flex align-items-center gap-3">
                  <img class="rounded" src="/assets/images/{{ $p['image'] }}" width="48">
                  <div class="flex-grow-1">
                    <p class="mb-1">{{ $p['name'] }}</p>
                    <small>ID: #{{ $p['code'] }}</small>
                  </div>
                  <div class="d-flex flex-column gap-0 align-items-center">
                    <span class="fw-semibold text-danger">{{ $p['quantity'] < 10 ? '0' . $p['quantity'] : $p['quantity'] }}</span>
                    <small class="text-muted">In Stock</small>
                  </div>
                </li>
              @endforeach
            @else
              <li class="list-group-item text-muted text-center">All stock levels are good</li>
            @endif
          </ul>
        </div>
      </div>

      <!-- Recent Sales -->
      <div class="col-lg-4">
        <div class="card h-100">
          <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
            <h4 class="mb-0 h5">Recent Sales</h4>
            <button class="btn btn-sm btn-outline-secondary">
              <i class="ti ti-calendar-event"></i> Weekly
            </button>
          </div>
          <ul class="list-group list-group-flush">
            @if ($hasRecentSales)
              @foreach ($recentSales as $s)
                <li class="list-group-item d-flex align-items-center gap-3">
                  <img class="rounded" src="/assets/images/{{ $s['image'] }}" width="48">
                  <div class="flex-grow-1">
                    <p class="mb-1">{{ $s['name'] }}</p>
                    <div class="d-flex align-items-center gap-2 text-muted">
                      <small class="fw-semibold">{{ $s['category'] }}</small>
                      <small>•</small>
                      <small>${{ number_format($s['total'], 2) }}</small>
                    </div>
                  </div>
                  @php
                    $badgeClass = $s['status'] === 'Completed' ? 'bg-success-subtle text-success' : ($s['status'] === 'Processing' ? 'bg-primary-subtle text-primary' : ($s['status'] === 'Pending' ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger'));
                  @endphp
                  <span class="badge {{ $badgeClass }}">{{ $s['status'] }}</span>
                </li>
              @endforeach
            @else
              <li class="list-group-item text-muted text-center">No recent sales</li>
            @endif
          </ul>
        </div>
      </div>
    </div>
  </div>
@endsection
